<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth for user session
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        try {
            // 🔹 Get the current admin's email from session
            $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
            \Log::info("🔍 Current user email: $currentUserEmail");

            // 🔹 Fetch admin's data to get faculties
            $userQuery = $database->collection('Users')->where('email', '==', $currentUserEmail);
            $userSnapshots = $userQuery->documents();

            if ($userSnapshots->isEmpty()) {
                \Log::error("❌ User not found: $currentUserEmail");
                return back()->withErrors(['error' => 'User not found.']);
            }

            $currentUserData = $userSnapshots->rows()[0]->data();
            $adminFaculties = $currentUserData['faculties'] ?? ($currentUserData['faculty'] ?? []);

            // Convert to array if it's a single string
            if (!is_array($adminFaculties)) {
                $adminFaculties = [$adminFaculties];
            }

            \Log::info("🔍 Admin Faculties: " . json_encode($adminFaculties));

            // 🔹 Fetch all lecturers (Since Firestore doesn't allow array contains for direct filtering)
            $lecturersQuery = $database->collection('Users')->where('role', '==', 'lecturer')->documents();
            $lecturerCount = 0;

            foreach ($lecturersQuery as $lecturer) {
                if ($lecturer->exists()) {
                    $lecturerFaculties = $lecturer->data()['faculties'] ?? [];

                    // 🔥 **Check if any faculty in `adminFaculties` matches lecturer's faculties**
                    if (!empty(array_intersect($adminFaculties, $lecturerFaculties))) {
                        $lecturerCount++;
                    }
                }
            }

            \Log::info("✅ Total Lecturers Found: $lecturerCount");

            // 🔹 Fetch past exams matching the admin’s faculties
            $pastExamsQuery = $database->collection('pastExams')->documents();
            $pastExamsCount = 0;

            foreach ($pastExamsQuery as $exam) {
                if ($exam->exists()) {
                    $examFaculty = $exam->data()['faculty'] ?? null;
                    if ($examFaculty && in_array($examFaculty, $adminFaculties)) {
                        $pastExamsCount++;
                    }
                }
            }

            // 🔹 Fetch courses matching the admin’s faculties
            $coursesQuery = $database->collection('Courses')->documents();
            $coursesCount = 0;

            foreach ($coursesQuery as $course) {
                if ($course->exists()) {
                    $courseFaculty = $course->data()['faculty'] ?? null;
                    if ($courseFaculty && in_array($courseFaculty, $adminFaculties)) {
                        $coursesCount++;
                    }
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
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Fetch the current user's email and faculty
        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;

        // Fetch current user's data to get their faculty
        $userRef = $database->collection('Users')->where('email', '==', $currentUserEmail);
        $currentUserSnapshots = $userRef->documents();

        if ($currentUserSnapshots->isEmpty()) {
            \Log::error("User not found with email: $currentUserEmail");
            throw new \Exception('User not found.');
        }

        $currentUserDocument = iterator_to_array($currentUserSnapshots)[0];
        $currentUserFaculty = $currentUserDocument->data()['faculty'] ?? 'No faculty assigned';
        \Log::info("Current user faculty: $currentUserFaculty");

        $containsComma = strpos($currentUserFaculty, ',') !== false;
        \Log::info("Faculty field contains comma: " . ($containsComma ? 'Yes' : 'No'));

        if ($containsComma) {
            // If faculty field contains a comma, fetch counts without faculty filters
            $lecturerCount = $database->collection('Users')->where('role', '==', 'lecturer')->documents()->size();
            $pastExamsCount = $database->collection('pastExams')->documents()->size();
            $coursesCount = $database->collection('Courses')->documents()->size();
            \Log::info("Fetching counts for all faculties.");
        } else {
            // Filter and count documents based on a specific faculty
            $lecturerCount = $database->collection('Users')->where('role', '==', 'lecturer')->where('faculty', '==', $currentUserFaculty)->documents()->size();
            $pastExamsCount = $database->collection('pastExams')->where('faculty', '==', $currentUserFaculty)->documents()->size();
            $coursesCount = $database->collection('Courses')->where('faculty', '==', $currentUserFaculty)->documents()->size();
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
    $faculty = session('user_faculty');
    \Log::info("🟢 Starting optimized dashboard stats generation for faculty:", ['faculty' => $faculty]);

    if (!is_array($faculty)) {
        $faculty = [$faculty];
    }

    $firestore = app('firebase.firestore')->database();
    $usersRef = $firestore->collection('Users');
    $coursesRef = $firestore->collection('Courses');
    $examsRef = $firestore->collection('Exams');

    $allLecturers = [];
    $lecturerDataMap = [];
    $facultyCourses = [];
    $allExams = [];

    $submittedCourses = [];
    $lecturerSubmissions = [];
    $incompleteExams = [];
    $questionCountPerSection = ['A' => 0, 'B' => 0, 'C' => 0];
    $sectionExamCount = ['A' => 0, 'B' => 0, 'C' => 0];
    $submissionsByMonth = [];

    $minQuestions = [
        "FST" => ["A" => 2, "B" => 12],
        "FBM" => ["A" => 2, "B" => 12],
        "FOE" => ["A" => 6, "B" => 6],
        "HEC" => ["A" => 20, "B" => 10],
        "FOL" => ["A" => 2, "B" => 4, "C" => 5]
    ];

    $pendingExams = 0;
    $approvedExams = 0;
    $declinedExams = 0;

    foreach ($faculty as $fac) {
        \Log::info("🔍 Processing faculty: $fac");

        // Lecturers
        $usersSnapshot = $usersRef
            ->where('faculties', 'array-contains', $fac)
            ->where('role', '==', 'lecturer')
            ->documents();

        foreach ($usersSnapshot as $userDoc) {
            if ($userDoc->exists()) {
                $data = $userDoc->data();
                $email = $data['email'] ?? null;
                if ($email) {
                    $allLecturers[] = $email;
                    $lecturerDataMap[$email] = $data;
                }
            }
        }

        \Log::info("👨‍🏫 Lecturers fetched: " . count($allLecturers));

        // Courses
        $coursesSnapshot = $coursesRef->where('faculty', '==', $fac)->documents();

        foreach ($coursesSnapshot as $doc) {
            if ($doc->exists()) {
                $data = $doc->data();
                $name = strtolower(trim($data['name'] ?? ''));
                $facultyCourses[$name] = $data;
            }
        }

        \Log::info("📘 Courses fetched: " . count($facultyCourses));

        // Exams
        $examsSnapshot = $examsRef->where('faculty', '==', $fac)->documents();

        foreach ($examsSnapshot as $doc) {
            if ($doc->exists()) {
                $data = $doc->data();
                $data['id'] = $doc->id();
                $allExams[] = $data;

                if (!isset($data['status'])) $pendingExams++;
                elseif ($data['status'] === 'Approved') $approvedExams++;
                elseif ($data['status'] === 'Declined') $declinedExams++;

                if (isset($data['created_at'])) {
                    try {
                        $month = \Carbon\Carbon::parse($data['created_at'])->format('Y-m');
                        $submissionsByMonth[$month] = ($submissionsByMonth[$month] ?? 0) + 1;
                    } catch (\Exception $e) {
                        \Log::warning("⚠️ Invalid created_at", ['value' => $data['created_at']]);
                    }
                }

                foreach (['A', 'B', 'C'] as $section) {
                    if (isset($data['sections'][$section])) {
                        $count = count($data['sections'][$section]);
                        $questionCountPerSection[$section] += $count;
                        $sectionExamCount[$section]++;
                    }
                }

                $requiredCounts = $minQuestions[$fac] ?? [];
                foreach ($requiredCounts as $section => $minCount) {
                    $actualCount = isset($data['sections'][$section]) ? count($data['sections'][$section]) : 0;
                    if ($actualCount < $minCount) {
                        $incompleteExams[] = $data;
                        break;
                    }
                }

                $submittedCourses[] = strtolower(trim($data['courseUnit'] ?? ''));
            }
        }

        \Log::info("🧾 Exams fetched: " . count($allExams));

        // Participation
        foreach ($lecturerDataMap as $email => $lecturer) {
            $lecturerCourses = $lecturer['courses'] ?? [];
            foreach ($lecturerCourses as $courseUnit) {
                $unit = strtolower(trim($courseUnit));
                if (isset($facultyCourses[$unit]) && in_array($unit, $submittedCourses)) {
                    $lecturerSubmissions[] = $email;
                    break;
                }
            }
        }
    }

    $lecturerSubmissions = array_unique(array_filter($lecturerSubmissions));
    $allLecturers = array_unique($allLecturers);

    \Log::info("✅ Participating lecturers: " . count($lecturerSubmissions));
    \Log::info("📌 Missing courses: " . count(array_diff(array_keys($facultyCourses), $submittedCourses)));
    \Log::info("📋 Final — Pending: $pendingExams | Approved: $approvedExams | Declined: $declinedExams | Incomplete: " . count($incompleteExams));

    $averageQuestions = [];
    foreach ($questionCountPerSection as $section => $total) {
        $averageQuestions[$section] = $sectionExamCount[$section] > 0
            ? round($total / $sectionExamCount[$section], 2)
            : 0;
    }

    $missingCourses = array_diff(array_keys($facultyCourses), $submittedCourses);

    // Attach lecturer info to incomplete exams
    foreach ($incompleteExams as &$exam) {
        $unit = strtolower(trim($exam['courseUnit'] ?? ''));
        $matched = false;
        foreach ($lecturerDataMap as $email => $lecturer) {
            foreach ($lecturer['courses'] ?? [] as $course) {
                if (strtolower(trim($course)) === $unit) {
                    $exam['lecturerName'] = $lecturer['firstName'] ?? 'Unknown';
                    $exam['lecturerEmail'] = $email;
                    $matched = true;
                    break;
                }
            }
            if ($matched) break;
        }

        $exam['lecturerName'] = $exam['lecturerName'] ?? 'Unknown';
        $exam['lecturerEmail'] = $exam['lecturerEmail'] ?? 'N/A';
        $exam['status'] = $exam['status'] ?? 'Pending Review';
    }

    return compact(
        'pendingExams',
        'approvedExams',
        'declinedExams',
        'facultyCourses',
        'lecturerSubmissions',
        'allLecturers',
        'missingCourses',
        'submissionsByMonth',
        'averageQuestions',
        'incompleteExams'
    );
}


public function dashboardStats()
{
    $data = $this->getDashboardData();
    return view('deans.dean-dashboard', $data);
}


public function exportDashboardReport()
{
    $data = $this->getDashboardData();
    $pdf = Pdf::loadView('deans.dashboard-report', $data)->setPaper('a4', 'portrait');
    return $pdf->download('faculty-dashboard-report.pdf');
}



public function index()
{
    set_time_limit(360); // 1 minute timeout only for this function

    try {
        $faculty = session('user_faculty');
        \Log::info("Fetching courses for faculty:", ['faculty' => $faculty]);

        if (!is_array($faculty)) {
            $faculty = [$faculty];
        }

        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $usersRef = $firestore->collection('Users');
        $coursesRepoRef = $firestore->collection('Courses');

        $courses = [];

        $minQuestions = [
            "FST" => ["A" => 2, "B" => 12],
            "FBM" => ["A" => 2, "B" => 12],
            "FOE" => ["A" => 6, "B" => 6],
            "HEC" => ["A" => 20, "B" => 10],
            "FOL" => ["A" => 2, "B" => 4, "C" => 5]
        ];

        foreach ($faculty as $fac) {
            $query = $examsRef->where('faculty', '==', $fac);
            $examsSnapshot = $query->documents();

            foreach ($examsSnapshot as $document) {
                if ($document->exists()) {
                    $examData = $document->data();
                    $examData['id'] = $document->id();
                    $examData['status'] = $examData['status'] ?? 'Pending Review';

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
                        $courseUnit = $examData['courseUnit'];
                        \Log::info("➡️ Looking for course code for courseUnit: {$courseUnit}");

                        // Lecturer info
                        $lecturerSnapshot = $usersRef->where('courses', 'array-contains', $courseUnit)->documents();
                        $lecturerInfo = null;
                        foreach ($lecturerSnapshot as $doc) {
                            $lecturerInfo = $doc->data();
                            break;
                        }

                        // Get course from Courses repo (fix: match by 'name')
                        $courseRepoSnapshot = $coursesRepoRef->where('name', '==', $courseUnit)->documents();
                        $courseInfo = null;
                        foreach ($courseRepoSnapshot as $doc) {
                            $courseInfo = $doc->data();
                            break;
                        }

                        if (!$courseInfo) {
                            \Log::warning("⚠️ No matching course found in Courses collection for '{$courseUnit}'");
                        } else {
                            \Log::info("✅ Found course '{$courseInfo['name']}' with code '{$courseInfo['code']}'");
                        }

                        $examData['lecturerName'] = $lecturerInfo['name'] ?? 'Unknown';
                        $examData['lecturerEmail'] = $lecturerInfo['email'] ?? 'N/A';
                        $examData['courseCode'] = $courseInfo['code'] ?? 'N/A';

                        $courses[] = $examData;
                    }
                }
            }
        }

        \Log::info("Courses fetched successfully after filtering.", ['count' => count($courses)]);
        return view('deans.dean-moderation', compact('courses'));

    } catch (\Exception $e) {
        \Log::error("❌ Error fetching courses: " . $e->getMessage());
        return back()->withErrors(['error' => 'Failed to fetch courses.']);
    }
}




    public function updateStatus(Request $request, $id)
    {
        try {
            $firestore = app('firebase.firestore')->database();
            $examRef = $firestore->collection('Exams')->document($id);
            $examSnapshot = $examRef->snapshot();

            if (!$examSnapshot->exists()) {
                return response()->json(['error' => 'Exam not found'], 404);
            }

            $status = $request->input('status');
            $examRef->update([
                ['path' => 'status', 'value' => $status]
            ]);

            return response()->json(['success' => true, 'status' => $status]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update status'], 500);
        }
    }


    public function approve($id)
    {
        $firestore = app('firebase.firestore')->database();
        $courseRef = $firestore->collection('Exams')->document($id);

        // Update status and remove comment field
        $courseRef->update([
            ['path' => 'status', 'value' => 'Approved'],
            ['path' => 'comment', 'value' => null] // Remove comment field
        ]);

        return back()->with('success', 'Course approved successfully.');
    }

    public function decline(Request $request, $id)
    {
        $firestore = app('firebase.firestore')->database();
        $courseRef = $firestore->collection('Exams')->document($id);

        $comment = $request->input('comment');

        // Update status and store comment
        $courseRef->update([
            ['path' => 'status', 'value' => 'Declined'],
            ['path' => 'comment', 'value' => $comment]
        ]);

        return back()->with('success', 'Course declined with a comment.');
    }


}
