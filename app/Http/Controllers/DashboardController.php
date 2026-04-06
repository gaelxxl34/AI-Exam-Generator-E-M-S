<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth for user session
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Services\AuditService;
use App\Services\DownloadLogService;
use App\Services\CacheService;
use App\Services\FirestoreRestService;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $db = app(FirestoreRestService::class);

        try {
            // 🔹 Get the current admin's email from session
            $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
            \Log::info("🔍 Current user email: $currentUserEmail");

            // 🔹 Fetch admin's data to get faculties
            $userSnapshots = $db->queryCollection('Users', 'email', '==', $currentUserEmail);

            if (empty($userSnapshots)) {
                \Log::error("❌ User not found: $currentUserEmail");
                return back()->withErrors(['error' => 'User not found.']);
            }

            $currentUserData = $userSnapshots[0];
            $adminFaculties = $currentUserData['faculties'] ?? ($currentUserData['faculty'] ?? []);

            // Convert to array if it's a single string
            if (!is_array($adminFaculties)) {
                $adminFaculties = [$adminFaculties];
            }

            \Log::info("🔍 Admin Faculties: " . json_encode($adminFaculties));

            // 🔹 Fetch all lecturers (Since Firestore doesn't allow array contains for direct filtering)
            $lecturersQuery = $db->queryCollection('Users', 'role', '==', 'lecturer');
            $lecturerCount = 0;

            foreach ($lecturersQuery as $lecturer) {
                    $lecturerFaculties = $lecturer['faculties'] ?? [];

                    // 🔥 **Check if any faculty in `adminFaculties` matches lecturer's faculties**
                    if (!empty(array_intersect($adminFaculties, $lecturerFaculties))) {
                    $lecturerCount++;
                }
            }

            \Log::info("✅ Total Lecturers Found: $lecturerCount");

            // 🔹 Fetch past exams matching the admin’s faculties
            $pastExamsQuery = $db->getCollection('pastExams');
            $pastExamsCount = 0;

            foreach ($pastExamsQuery as $exam) {
                $examFaculty = $exam['faculty'] ?? null;
                if ($examFaculty && in_array($examFaculty, $adminFaculties)) {
                    $pastExamsCount++;
                }
            }

            // 🔹 Fetch courses matching the admin’s faculties
            $coursesQuery = $db->getCollection('Courses');
            $coursesCount = 0;

            foreach ($coursesQuery as $course) {
                $courseFaculty = $course['faculty'] ?? null;
                if ($courseFaculty && in_array($courseFaculty, $adminFaculties)) {
                    $coursesCount++;
                }
            }

            return view('admin.dashboard', [
                'lecturerCount' => $lecturerCount,
                'pastExamsCount' => $pastExamsCount,
                'coursesCount' => $coursesCount,
                'faculty' => implode(', ', $adminFaculties),
            ]);

        } catch (\Exception $e) {
            \Log::error("❌ Error in adminDashboard: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error loading dashboard: ' . $e->getMessage()]);
        }
    }




    public function genAdminDashboard()
    {
        $db = app(FirestoreRestService::class);

        // Fetch the current user's email and faculty
        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;

        // Fetch current user's data to get their faculty
        $currentUserSnapshots = $db->queryCollection('Users', 'email', '==', $currentUserEmail);

        if (empty($currentUserSnapshots)) {
            \Log::error("User not found with email: $currentUserEmail");
            throw new \Exception('User not found.');
        }

        $currentUserData = $currentUserSnapshots[0];
        $currentUserFaculty = $currentUserData['faculty'] ?? 'No faculty assigned';
        \Log::info("Current user faculty: $currentUserFaculty");

        $containsComma = strpos($currentUserFaculty, ',') !== false;
        \Log::info("Faculty field contains comma: " . ($containsComma ? 'Yes' : 'No'));

        if ($containsComma) {
            // If faculty field contains a comma, fetch counts without faculty filters
            $lecturerCount = count($db->queryCollection('Users', 'role', '==', 'lecturer'));
            $pastExamsCount = count($db->getCollection('pastExams'));
            $coursesCount = count($db->getCollection('Courses'));
            \Log::info("Fetching counts for all faculties.");
        } else {
            // Filter and count documents based on a specific faculty
            $lecturerCount = count($db->runQuery('Users', [['field' => 'role', 'op' => '==', 'value' => 'lecturer'], ['field' => 'faculty', 'op' => '==', 'value' => $currentUserFaculty]]));
            $pastExamsCount = count($db->queryCollection('pastExams', 'faculty', '==', $currentUserFaculty));
            $coursesCount = count($db->queryCollection('Courses', 'faculty', '==', $currentUserFaculty));
            \Log::info("Fetching counts for specific faculty: $currentUserFaculty");
        }

        \Log::info("Counts - Lecturers: $lecturerCount, Past Exams: $pastExamsCount, Courses: $coursesCount");

        // Pass the counts to the view
        return view('genadmin.gen-dashboard', [
            'lecturerCount' => $lecturerCount,
            'pastExamsCount' => $pastExamsCount,
            'coursesCount' => $coursesCount,
            'faculty' => $currentUserFaculty  // Optional, to display on dashboard if needed
        ]);
    }

private function getDashboardData(): array
{
    set_time_limit(320);
    $faculty = session('user_faculty');
    \Log::info("🟢 Starting dashboard stats generation for faculty:", ['faculty' => $faculty]);

    if (!is_array($faculty)) {
        $faculty = [$faculty];
    }

    // Create cache key based on faculties
    $cacheKey = 'dean_dashboard_' . md5(implode('_', $faculty));
    
    // Try to get from cache (5 minutes TTL for dashboard data)
    return Cache::remember($cacheKey, 300, function () use ($faculty) {
        return $this->fetchDashboardDataFromFirestore($faculty);
    });
}

/**
 * Fetch dashboard data from Firestore (extracted for caching)
 */
private function fetchDashboardDataFromFirestore(array $faculty): array
{
    $db = app(FirestoreRestService::class);

    $lecturerDataMap = [];
    $facultyCourses = [];
    $submittedCourses = [];
    $incompleteExams = [];

    $minQuestions = [
        "FST" => ["A" => 2, "B" => 12],
        "FBM" => ["A" => 2, "B" => 12],
        "FOE" => ["A" => 4, "B" => 4],
        "HEC" => ["A" => 20, "B" => 10],
        "FOL" => ["A" => 2, "B" => 4, "C" => 5]
    ];

    $pendingExams = 0;
    $approvedExams = 0;
    $declinedExams = 0;
    $allExams = [];

    foreach ($faculty as $fac) {
        \Log::info("🔍 Processing faculty: $fac");

        // Lecturers
        $usersSnapshot = $db->runQuery('Users', [
                ['field' => 'faculties', 'op' => 'array-contains', 'value' => $fac],
                ['field' => 'role', 'op' => '==', 'value' => 'lecturer']
            ]);

        foreach ($usersSnapshot as $data) {
                $email = $data['email'] ?? null;
                if ($email) {
                    $lecturerDataMap[$email] = $data;
                }
        }

        // Courses
        $coursesSnapshot = $db->queryCollection('Courses', 'faculty', '==', $fac);
        foreach ($coursesSnapshot as $doc) {
                $name = strtolower(trim($doc['name'] ?? ''));
                $facultyCourses[$name] = $doc;
        }

        // Exams
        $examsSnapshot = $db->queryCollection('Exams', 'faculty', '==', $fac);
        foreach ($examsSnapshot as $data) {
                $allExams[] = $data;

                if (!isset($data['status'])) $pendingExams++;
                elseif ($data['status'] === 'Approved') $approvedExams++;
                elseif ($data['status'] === 'Declined') $declinedExams++;

                $submittedCourses[] = strtolower(trim($data['courseUnit'] ?? ''));
        }

        // Lecturer evaluation
        foreach ($lecturerDataMap as $email => $lecturer) {
            $lecturerName = $lecturer['firstName'] ?? 'Unknown';
            $lecturerCourses = $lecturer['courses'] ?? [];

            \Log::info("🔍 Checking lecturer: $lecturerName <$email>");

            foreach ($lecturerCourses as $courseUnit) {
                if (stripos($courseUnit, 'online') !== false) {
                    \Log::info("🟦 Skipped Online course: $courseUnit");
                    continue;
                }

                $unit = strtolower(trim($courseUnit));
                if (!isset($facultyCourses[$unit])) continue;

                $matchingExam = collect($allExams)->firstWhere(fn($exam) =>
                    strtolower(trim($exam['courseUnit'] ?? '')) === $unit
                );

                if (!$matchingExam) {
                    \Log::info("❌ No exam submitted for: $courseUnit");
                    $incompleteExams[] = [
                        'courseUnit' => $courseUnit,
                        'lecturerName' => $lecturerName,
                        'lecturerEmail' => $email,
                        'status' => 'Not Submitted',
                        'notes' => 'No exam submitted for this course.'
                    ];
                    continue;
                }

                $isIncomplete = false;
                $reasons = [];

                $required = $minQuestions[$fac] ?? [];

                foreach ($required as $section => $minCount) {
                    $actual = isset($matchingExam['sections'][$section]) ? count($matchingExam['sections'][$section]) : 0;
                    if ($actual < $minCount) {
                        $isIncomplete = true;
                        $reasons[] = "Section $section has $actual (min $minCount)";
                    }
                }

                if ($isIncomplete) {
                    \Log::warning("⚠️ Incomplete exam for $courseUnit → " . implode(', ', $reasons));
                    $incompleteExams[] = [
                        'courseUnit' => $matchingExam['courseUnit'] ?? $unit,
                        'lecturerName' => $lecturerName,
                        'lecturerEmail' => $email,
                        'status' => $matchingExam['status'] ?? 'Pending Review',
                        'notes' => implode('; ', $reasons),
                    ];
                } elseif (($matchingExam['status'] ?? '') === 'Declined') {
                    \Log::warning("🚫 Declined exam for $courseUnit");
                    $incompleteExams[] = [
                        'courseUnit' => $matchingExam['courseUnit'] ?? $unit,
                        'lecturerName' => $lecturerName,
                        'lecturerEmail' => $email,
                        'status' => 'Declined',
                        'notes' => 'This exam was declined by reviewer.',
                    ];
                } else {
                    \Log::info("✅ Complete and accepted exam for $courseUnit");
                }
            }
        }
    }

    return compact(
        'pendingExams',
        'approvedExams',
        'declinedExams',
        'facultyCourses',
        'incompleteExams'
    );
}


public function dashboardStats()
{
    $data = $this->getDashboardData();
    
    // Get security stats (not cached - always fresh for security)
    $faculty = session('user_faculty');
    if (!is_array($faculty)) {
        $faculty = [$faculty];
    }
    
    $auditService = app(AuditService::class);
    $downloadLogService = app(DownloadLogService::class);
    
    // Get activity stats for the dashboard cards
    $activityStats = $auditService->getFacultyActivityStats($faculty);
    $downloadStats = $downloadLogService->getFacultyDownloadStats($faculty);
    
    return view('deans.dean-dashboard', array_merge($data, [
        'activityStats' => $activityStats,
        'downloadStats' => $downloadStats,
    ]));
}

/**
 * AJAX endpoint to get faculty security activity
 */
public function getFacultySecurityActivity(Request $request)
{
    try {
        $faculty = session('user_faculty');
        if (!is_array($faculty)) {
            $faculty = [$faculty];
        }

        $auditService = app(AuditService::class);
        $securityLogs = $auditService->getFacultySecurityLogs($faculty, 50);

        return response()->json([
            'success' => true,
            'logs' => $securityLogs,
        ]);
    } catch (\Exception $e) {
        \Log::error("Failed to get security activity: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

/**
 * AJAX endpoint to get faculty download activity
 */
public function getFacultyDownloadActivity(Request $request)
{
    try {
        $faculty = session('user_faculty');
        if (!is_array($faculty)) {
            $faculty = [$faculty];
        }

        $downloadLogService = app(DownloadLogService::class);
        $downloads = $downloadLogService->getDownloadsByFaculty($faculty, 50);

        return response()->json([
            'success' => true,
            'downloads' => $downloads,
        ]);
    } catch (\Exception $e) {
        \Log::error("Failed to get download activity: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

/**
 * AJAX endpoint to get faculty exam activity
 */
public function getFacultyExamActivity(Request $request)
{
    try {
        $faculty = session('user_faculty');
        if (!is_array($faculty)) {
            $faculty = [$faculty];
        }

        $auditService = app(AuditService::class);
        $examActivity = $auditService->getFacultyExamActivity($faculty, 50);

        return response()->json([
            'success' => true,
            'activity' => $examActivity,
        ]);
    } catch (\Exception $e) {
        \Log::error("Failed to get exam activity: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

/**
 * Force refresh dashboard cache
 */
public function refreshDashboardCache()
{
    try {
        $faculty = session('user_faculty');
        if (!is_array($faculty)) {
            $faculty = [$faculty];
        }
        
        $cacheKey = 'dean_dashboard_' . md5(implode('_', $faculty));
        Cache::forget($cacheKey);
        
        return response()->json(['success' => true, 'message' => 'Cache refreshed']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}


public function exportDashboardReport()
{
    $data = $this->getDashboardData();
    $pdf = Pdf::loadView('deans.dashboard-report', $data)->setPaper('a4', 'portrait');
    return $pdf->download('faculty-dashboard-report.pdf');
}


/**
 * Dean Moderation Index - Optimized with pagination support
 * Now uses stored courseCode and lecturerEmail fields directly
 */
public function index()
{
    try {
        $faculty = session('user_faculty');
        \Log::info("Dean moderation page loading for faculty:", ['faculty' => $faculty]);

        if (!is_array($faculty)) {
            $faculty = [$faculty];
        }

        // Pass faculty to view - data will be loaded via AJAX
        return view('deans.dean-moderation', [
            'faculty' => $faculty,
            'courses' => [] // Initial empty - will load via AJAX
        ]);

    } catch (\Exception $e) {
        \Log::error("❌ Error loading dean moderation: " . $e->getMessage());
        return back()->withErrors(['error' => 'Failed to load page.']);
    }
}

/**
 * AJAX endpoint for loading exams with pagination
 * Optimized: Uses caching and efficient queries
 */
public function loadExamsAjax(Request $request)
{
    try {
        $faculty = session('user_faculty');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);
        $status = $request->input('status', 'all');
        $forceRefresh = $request->input('refresh', false);

        if (!is_array($faculty)) {
            $faculty = [$faculty];
        }

        // Create a unique cache key based on faculty and status
        $facultyKey = implode('_', $faculty);
        $cacheKey = "moderation_exams_{$facultyKey}_{$status}";
        
        // Force clear cache if refresh requested
        if ($forceRefresh) {
            Cache::forget($cacheKey);
            \Log::info("🔄 Manual cache refresh requested for: {$cacheKey}");
        }
        
        // Try to get from cache first (cache for 2 minutes for moderation data)
        $allCourses = Cache::remember($cacheKey, 120, function () use ($faculty, $status) {
            \Log::info("📊 Cache MISS - Fetching moderation exams from Firestore");
            
            $db = app(FirestoreRestService::class);

            $minQuestions = [
                "FST" => ["A" => 2, "B" => 12],
                "FBM" => ["A" => 2, "B" => 12],
                "FOE" => ["A" => 4, "B" => 4],
                "HEC" => ["A" => 20, "B" => 10],
                "FOL" => ["A" => 2, "B" => 4, "C" => 5]
            ];

            $courses = [];

            foreach ($faculty as $fac) {
                $filters = [['field' => 'faculty', 'op' => '==', 'value' => $fac]];
                
                // Filter by status at Firestore level if specified
                if ($status !== 'all') {
                    $statusMap = [
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'declined' => 'Declined'
                    ];
                    if (isset($statusMap[$status])) {
                        $filters[] = ['field' => 'status', 'op' => '==', 'value' => $statusMap[$status]];
                    }
                }
                
                $examsSnapshot = $db->runQuery('Exams', $filters);

                foreach ($examsSnapshot as $examData) {
                        $examData['status'] = $examData['status'] ?? 'Pending Review';

                        // Check minimum question requirements
                        $requiredCounts = $minQuestions[$fac] ?? [];
                        $meetsRequirement = true;

                        foreach ($requiredCounts as $section => $minCount) {
                            $actualCount = isset($examData['sections'][$section]) ? count($examData['sections'][$section]) : 0;
                            if ($actualCount < $minCount) {
                                $meetsRequirement = false;
                                break;
                            }
                        }

                        if ($meetsRequirement) {
                            // Convert timestamp for caching
                            $createdAt = $examData['created_at'] ?? null;
                            if ($createdAt && is_object($createdAt)) {
                                $createdAt = $createdAt->get()->format('Y-m-d H:i:s');
                            }
                            
                            $courses[] = [
                                'id' => $examData['id'],
                                'courseUnit' => $examData['courseUnit'] ?? 'Unknown',
                                'courseCode' => $examData['courseCode'] ?? 'N/A',
                                'lecturerEmail' => $examData['lecturerEmail'] ?? $examData['uploaded_by_email'] ?? 'N/A',
                                'lecturerName' => $examData['lecturerName'] ?? $examData['uploaded_by_name'] ?? 'Unknown',
                                'created_at' => $createdAt,
                                'status' => $examData['status'],
                                'faculty' => $fac,
                                'last_dean_edit' => $examData['last_dean_edit'] ?? null,
                            ];
                        }
                    }
                }

            // Sort by created_at descending (newest first)
            usort($courses, function($a, $b) {
                $dateA = $a['created_at'] ?? '1970-01-01';
                $dateB = $b['created_at'] ?? '1970-01-01';
                return strcmp($dateB, $dateA);
            });

            return $courses;
        });

        \Log::info("📊 Moderation list: " . count($allCourses) . " exams found (from cache or Firestore)");

        // Apply search filter if provided (searches across ALL courses)
        $search = $request->input('search', '');
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $allCourses = array_filter($allCourses, function($course) use ($searchLower) {
                $courseUnit = strtolower($course['courseUnit'] ?? '');
                $courseCode = strtolower($course['courseCode'] ?? '');
                $lecturerEmail = strtolower($course['lecturerEmail'] ?? '');
                $lecturerName = strtolower($course['lecturerName'] ?? '');
                
                return strpos($courseUnit, $searchLower) !== false ||
                       strpos($courseCode, $searchLower) !== false ||
                       strpos($lecturerEmail, $searchLower) !== false ||
                       strpos($lecturerName, $searchLower) !== false;
            });
            $allCourses = array_values($allCourses); // Re-index array
        }

        // Calculate stats from filtered/cached data
        $total = count($allCourses);
        $stats = [
            'total' => $total,
            'pending' => count(array_filter($allCourses, fn($c) => $c['status'] === 'Pending Review')),
            'approved' => count(array_filter($allCourses, fn($c) => $c['status'] === 'Approved')),
            'declined' => count(array_filter($allCourses, fn($c) => $c['status'] === 'Declined')),
        ];

        // Check if show_all is requested (no pagination)
        $showAll = $request->input('show_all', false);
        
        if ($showAll || !empty($search)) {
            // Return all courses without pagination when searching or show_all
            return response()->json([
                'success' => true,
                'courses' => $allCourses,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => $total,
                    'total' => $total,
                    'total_pages' => 1,
                    'has_more' => false,
                    'show_all' => true,
                ],
                'stats' => $stats
            ]);
        }

        // Calculate pagination on cached data
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        // Slice for current page
        $courses = array_slice($allCourses, $offset, $perPage);

        return response()->json([
            'success' => true,
            'courses' => $courses,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_more' => $page < $totalPages,
                'show_all' => false,
            ],
            'stats' => $stats
        ]);

    } catch (\Exception $e) {
        \Log::error("❌ Error loading exams via AJAX: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Failed to load exams: ' . $e->getMessage()
        ], 500);
    }
}




    public function updateStatus(Request $request, $id)
    {
        try {
            $db = app(FirestoreRestService::class);
            $examSnapshot = $db->getDocument('Exams', $id);

            if (!$examSnapshot) {
                return response()->json(['error' => 'Exam not found'], 404);
            }

            $status = $request->input('status');
            $db->updateDocument('Exams', $id, ['status' => $status]);

            return response()->json(['success' => true, 'status' => $status]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update status'], 500);
        }
    }


    public function approve($id)
    {
        $db = app(FirestoreRestService::class);
        
        // Get exam data for logging
        $examData = $db->getDocument('Exams', $id) ?? [];
        $courseUnit = $examData['courseUnit'] ?? 'Unknown';
        $faculty = $examData['faculty'] ?? null;

        // Update status and remove comment field
        $db->updateDocument('Exams', $id, ['status' => 'Approved', 'comment' => null]);

        // Invalidate moderation cache for this faculty
        $this->invalidateModerationCache($faculty);

        // Log the approval
        app(AuditService::class)->logExamApproved($id, $courseUnit);

        return back()->with('success', 'Course approved successfully.');
    }

    public function decline(Request $request, $id)
    {
        $db = app(FirestoreRestService::class);

        // Get exam data for logging
        $examData = $db->getDocument('Exams', $id) ?? [];
        $courseUnit = $examData['courseUnit'] ?? 'Unknown';
        $faculty = $examData['faculty'] ?? null;

        $comment = $request->input('comment');

        // Update status and store comment
        $db->updateDocument('Exams', $id, ['status' => 'Declined', 'comment' => $comment]);

        // Invalidate moderation cache for this faculty
        $this->invalidateModerationCache($faculty);

        // Log the decline
        app(AuditService::class)->logExamDeclined($id, $courseUnit, $comment);

        return back()->with('success', 'Course declined with a comment.');
    }

    /**
     * Invalidate moderation cache when exam status changes
     */
    private function invalidateModerationCache($faculty)
    {
        if ($faculty) {
            // Clear all status variations for this faculty
            $cacheKeys = [
                "moderation_exams_{$faculty}_all",
                "moderation_exams_{$faculty}_pending",
                "moderation_exams_{$faculty}_approved",
                "moderation_exams_{$faculty}_declined"
            ];
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            
            \Log::info("🗑️ Moderation cache invalidated for faculty: {$faculty}");
        }
    }


    /**
     * Show the Dean Review page for a specific exam
     */
    public function showReviewExam($examId)
    {
        try {
            $db = app(FirestoreRestService::class);
            $exam = $db->getDocument('Exams', $examId);

            if (!$exam) {
                return back()->withErrors(['error' => 'Exam not found.']);
            }

            // Calculate total questions
            $totalQuestions = 0;
            if (isset($exam['sections']) && is_array($exam['sections'])) {
                foreach ($exam['sections'] as $section => $questions) {
                    $totalQuestions += count($questions);
                }
            }

            return view('deans.dean-review-exam', [
                'exam' => $exam,
                'totalQuestions' => $totalQuestions,
            ]);

        } catch (\Exception $e) {
            \Log::error("❌ Error loading exam for review: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load exam.']);
        }
    }

    /**
     * Update a question as Dean (with logging)
     */
    public function deanUpdateQuestion(Request $request, $courseUnit, $sectionName, $questionIndex)
    {
        \Log::info("Dean updating question: Course Unit - {$courseUnit}, Section - {$sectionName}, Index - {$questionIndex}");

        $request->validate([
            'question' => 'required|string',
            'edit_reason' => 'required|string|min:5',
            'exam_id' => 'required|string',
        ]);

        try {
            $db = app(FirestoreRestService::class);
            $examData = $db->getDocument('Exams', $request->exam_id);

            if (!$examData) {
                return back()->withErrors(['error' => 'Exam not found.']);
            }

            if (!isset($examData['sections'][$sectionName])) {
                return back()->withErrors(['error' => "Section '{$sectionName}' not found."]);
            }

            // Store the original question for logging
            $originalQuestion = $examData['sections'][$sectionName][$questionIndex] ?? '';

            // Update the question
            $examData['sections'][$sectionName][$questionIndex] = $request->question;

            // Create dean edit log entry
            $deanEdit = [
                'type' => 'edit',
                'section' => $sectionName,
                'questionIndex' => (int) $questionIndex,
                'dean_email' => session('user_email'),
                'dean_name' => session('user_name', 'Dean'),
                'reason' => $request->edit_reason,
                'edited_at' => now()->toIso8601String(),
                'original_content_preview' => substr(strip_tags($originalQuestion), 0, 100) . '...',
            ];

            // Add to dean_edits array
            $deanEdits = $examData['dean_edits'] ?? [];
            $deanEdits[] = $deanEdit;

            // Update Firestore
            $db->updateDocument('Exams', $request->exam_id, [
                'sections' => $examData['sections'],
                'dean_edits' => $deanEdits,
                'last_dean_edit' => now()->toIso8601String(),
            ]);

            // Log the edit
            app(AuditService::class)->log('dean_question_edit', [
                'exam_id' => $request->exam_id,
                'course_unit' => $courseUnit,
                'section' => $sectionName,
                'question_index' => $questionIndex,
                'reason' => $request->edit_reason,
            ]);

            \Log::info("Dean successfully updated question.");
            return back()->with('success', 'Question updated successfully. The lecturer will be notified of this change.');

        } catch (\Exception $e) {
            \Log::error("❌ Dean update failed: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update question.']);
        }
    }

    /**
     * Log a question review (without edit)
     */
    public function logReview(Request $request)
    {
        try {
            $request->validate([
                'exam_id' => 'required|string',
                'section' => 'required|string',
                'questionIndex' => 'required|integer',
                'type' => 'required|string',
            ]);

            $db = app(FirestoreRestService::class);
            $examData = $db->getDocument('Exams', $request->exam_id);

            if (!$examData) {
                return response()->json(['error' => 'Exam not found'], 404);
            }

            // Create review log entry
            $reviewLog = [
                'type' => 'review',
                'section' => $request->section,
                'questionIndex' => $request->questionIndex,
                'dean_email' => session('user_email'),
                'dean_name' => session('user_name', 'Dean'),
                'reviewed_at' => now()->toIso8601String(),
            ];

            // Add to dean_edits array
            $deanEdits = $examData['dean_edits'] ?? [];
            $deanEdits[] = $reviewLog;

            // Update Firestore
            $db->updateDocument('Exams', $request->exam_id, [
                'dean_edits' => $deanEdits,
                'last_dean_review' => now()->toIso8601String(),
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error("❌ Failed to log review: " . $e->getMessage());
            return response()->json(['error' => 'Failed to log review'], 500);
        }
    }

    /**
     * Add a dean comment to a specific question
     */
    public function addDeanComment(Request $request)
    {
        try {
            $request->validate([
                'exam_id' => 'required|string',
                'section' => 'required|string',
                'questionIndex' => 'required|integer',
                'comment' => 'required|string|max:1000',
                'type' => 'required|string|in:suggestion,issue,general',
            ]);

            $db = app(FirestoreRestService::class);
            $examData = $db->getDocument('Exams', $request->exam_id);

            if (!$examData) {
                return response()->json(['error' => 'Exam not found'], 404);
            }

            // Create comment entry
            $newComment = [
                'section' => $request->section,
                'questionIndex' => $request->questionIndex,
                'comment' => $request->comment,
                'type' => $request->type,
                'dean_email' => session('user_email'),
                'dean_name' => session('user_firstName', 'Dean') . ' ' . session('user_lastName', ''),
                'created_at' => now()->toIso8601String(),
            ];

            // Add to dean_comments array
            $deanComments = $examData['dean_comments'] ?? [];
            $deanComments[] = $newComment;

            // Update Firestore
            $db->updateDocument('Exams', $request->exam_id, [
                'dean_comments' => $deanComments,
                'last_dean_comment' => now()->toIso8601String(),
                'has_dean_feedback' => true,
            ]);

            // Log the activity
            $this->auditService->log('dean_comment_added', [
                'exam_id' => $request->exam_id,
                'course_unit' => $examData['courseUnit'] ?? 'Unknown',
                'section' => $request->section,
                'question_index' => $request->questionIndex,
                'comment_type' => $request->type,
                'dean_email' => session('user_email'),
            ]);

            \Log::info("✅ Dean comment added to exam: " . $request->exam_id);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error("❌ Failed to add dean comment: " . $e->getMessage());
            return response()->json(['error' => 'Failed to add comment: ' . $e->getMessage()], 500);
        }
    }

}
