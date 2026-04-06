<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\FirestoreRestService;

class SuperAdminController extends Controller
{
    protected $auth;

    public function __construct()
    {
        if (env('FIREBASE_CREDENTIALS_BASE64')) {
            $firebaseCredentialsJson = base64_decode(env('FIREBASE_CREDENTIALS_BASE64'));
            if (!$firebaseCredentialsJson) {
                throw new \Exception('Failed to decode FIREBASE_CREDENTIALS_BASE64');
            }
            $serviceAccount = json_decode($firebaseCredentialsJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to decode JSON: ' . json_last_error_msg());
            }
        } else {
            $firebaseCredentialsPath = env('FIREBASE_CREDENTIALS');
            if (!$firebaseCredentialsPath || !file_exists($firebaseCredentialsPath)) {
                throw new \Exception('Firebase credentials file path is not set or file does not exist');
            }
            $serviceAccount = $firebaseCredentialsPath;
        }

        $firebaseFactory = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->auth = $firebaseFactory->createAuth();
    }

    public function registerAdmins(Request $request)
    {
        \Log::info('Register admins method called');

        $validatedData = $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'faculty' => 'required|string', // Ensure faculty is a required input
            'role' => 'required|string'
        ]);

        try {
            $auth = app('firebase.auth');
            $userProperties = [
                'email' => $validatedData['email'],
                'emailVerified' => false,
                'password' => $validatedData['password'],
                'disabled' => false,
            ];

            $createdUser = $auth->createUser($userProperties);
            \Log::info('Firebase user created with UID: ' . $createdUser->uid);


            $db = app(FirestoreRestService::class);
            $db->setDocument('Users', $createdUser->uid, [
                'firstName' => $validatedData['firstName'],
                'lastName' => $validatedData['lastName'],
                'email' => $validatedData['email'],
                'created_at' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                'faculty' => $validatedData['faculty'],
                'role' => $validatedData['role']
            ]);
            \Log::info('Lecturer data added to Firestore with courses');

            return redirect()->intended('/superadmin/admin-list')->with('success', 'Lecturer registered successfully.');
        } catch (\Throwable $e) {
            \Log::error('Error registering lecturer: ' . $e->getMessage());
            return back()->withErrors(['upload_error' => 'Error registering lecturer.'])->with('message', 'Error registering lecturer: ' . $e->getMessage());
        }
    }

    public function adminsList()
    {
        \Log::info('adminsList method called');

        try {
            $db = app(FirestoreRestService::class);

            $admins = $db->runQuery('Users', [
                ['field' => 'role', 'op' => 'in', 'value' => ['admin', 'genadmin', 'dean']]
            ]);

            $adminsByRole = [];

            foreach ($admins as $admin) {
                $role = $admin['role'] ?? 'Other';

                $adminsByRole[$role][] = [
                    'id' => $admin['id'],
                    'firstName' => $admin['firstName'] ?? 'N/A',
                    'lastName' => $admin['lastName'] ?? 'N/A',
                    'email' => $admin['email'] ?? 'N/A',
                    'role' => $admin['role'] ?? 'N/A',
                ];
            }

            \Log::info('Admins fetched with image URLs.');
            return view('superadmin.admin-list', ['adminsByRole' => $adminsByRole]);
        } catch (\Exception $e) {
            \Log::error('Error in adminsList: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }

    public function editAdmin($id)
    {
        try {
            $db = app(FirestoreRestService::class);
            $adminSnapshot = $db->getDocument('Users', $id);

            if ($adminSnapshot) {
                $adminData = [
                    'id' => $adminSnapshot['id'],
                    'firstName' => $adminSnapshot['firstName'] ?? 'N/A',
                    'lastName' => $adminSnapshot['lastName'] ?? 'N/A',
                    'email' => $adminSnapshot['email'] ?? 'N/A',
                    'faculty' => $adminSnapshot['faculty'] ?? 'N/A',
                    'role' => $adminSnapshot['role'] ?? 'N/A',
                ];

                return view('superadmin.edit-admin', ['admin' => $adminData]);
            } else {
                return 'Admin not found';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }


    public function updateAdminData(Request $request, $id)
    {
        try {
            // Validate input data
            $validatedData = $request->validate([
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required|email',
                'role' => 'required',
                'faculty' => 'required',
            ]);

            // Initialize Firebase services
            $db = app(FirestoreRestService::class);
            $auth = app('firebase.auth');

            // Fetch the current user data
            $currentData = $db->getDocument('Users', $id);

            if (!$currentData) {
                return back()->with('error', 'User not found.');
            }

            $currentEmail = $currentData['email'] ?? null;

            // Update Firestore Data
            $db->updateDocument('Users', $id, [
                'firstName' => $validatedData['firstName'],
                'lastName' => $validatedData['lastName'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
                'faculty' => $validatedData['faculty'],
            ]);

            // **Update Firebase Authentication Email if it changed**
            if ($currentEmail !== $validatedData['email']) {
                try {
                    $user = $auth->getUserByEmail($currentEmail);
                    $auth->updateUser($user->uid, ['email' => $validatedData['email']]);
                } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                    return back()->with('error', 'User not found in Firebase Authentication.');
                } catch (\Exception $e) {
                    return back()->with('error', 'Failed to update authentication email: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Admin updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating admin: ' . $e->getMessage());
        }
    }



    public function deleteAdmin($id)
    {
        try {
            $db = app(FirestoreRestService::class);
            $auth = app('firebase.auth');

            // Get document data before deleting
            $lecturerData = $db->getDocument('Users', $id);

            // Delete profile picture from Firebase Storage if it exists
            if ($lecturerData && isset($lecturerData['profile_picture'])) {
                try {
                    $storage = app('firebase.storage')->getBucket();
                    $profilePicturePath = $lecturerData['profile_picture'];
                    if ($storage->object($profilePicturePath)->exists()) {
                        $storage->object($profilePicturePath)->delete();
                    }
                } catch (\Exception $e) {
                    \Log::warning("Could not delete profile picture: " . $e->getMessage());
                }
            }

            // Delete the Firestore document
            $db->deleteDocument('Users', $id);

            // Delete the user from Firebase Authentication
            $auth->deleteUser($id);

            // Redirect to the lecturer list with a success message
            return redirect()->route('superadmin.admin-list')->with('success', 'Lecturer deleted successfully.');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return back()->with('error', 'Error deleting lecturer: ' . $e->getMessage());
        }
    }


    // Control Lecturers starts here
    public function manageLecturers()
    {
        try {
            $db = app(FirestoreRestService::class);
            $lecturers = $db->queryCollection('Users', 'role', '==', 'lecturer');

            $lecturersByFaculty = [];
            
            foreach ($lecturers as $data) {
                    // Ensure faculties is always an array
                    $faculties = $data['faculties'] ?? [];
                    if (!is_array($faculties)) {
                        $faculties = [];
                    }
                    
                    // Ensure courses is always an array
                    $courses = $data['courses'] ?? [];
                    if (!is_array($courses)) {
                        $courses = [];
                    }
                    
                    $lecturerData = [
                        'id' => $data['id'],
                        'name' => trim(($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? '')),
                        'email' => $data['email'] ?? 'No Email',
                        'status' => $data['disabled'] ?? false,
                        'faculties' => $faculties,
                        'courses' => $courses
                    ];
                    
                    // If lecturer has multiple faculties
                    if (count($faculties) > 1) {
                        $lecturersByFaculty['Multiple Faculties'][] = $lecturerData;
                    }
                    // If lecturer has exactly one faculty
                    elseif (count($faculties) === 1) {
                        $faculty = $faculties[0];
                        $lecturersByFaculty[$faculty][] = $lecturerData;
                    }
                    // If lecturer has no faculties assigned
                    else {
                        $lecturersByFaculty['Unassigned'][] = $lecturerData;
                    }
            }

            // Sort faculties alphabetically, but keep special categories at the end
            $sortedFaculties = [];
            $specialCategories = ['Multiple Faculties', 'Unassigned'];
            
            foreach ($lecturersByFaculty as $faculty => $lecturers) {
                if (!in_array($faculty, $specialCategories)) {
                    $sortedFaculties[$faculty] = $lecturers;
                }
            }
            
            // Sort the main faculties alphabetically
            ksort($sortedFaculties);
            
            // Add special categories at the end
            foreach ($specialCategories as $category) {
                if (isset($lecturersByFaculty[$category])) {
                    $sortedFaculties[$category] = $lecturersByFaculty[$category];
                }
            }

            return view('superadmin.lecturer-control', compact('sortedFaculties'));
        } catch (\Exception $e) {
            \Log::error('Error in manageLecturers: ' . $e->getMessage());
            return view('superadmin.lecturer-control', ['sortedFaculties' => []]);
        }
    }

    public function toggleLecturerStatus($uid)
    {
        try {
            \Log::info("Attempting to toggle status for lecturer UID: {$uid}");
            
            $db = app(FirestoreRestService::class);
            $userData = $db->getDocument('Users', $uid);

            if (!$userData) {
                \Log::error("❌ User with UID {$uid} not found in Firestore.");
                return response()->json([
                    'success' => false, 
                    'message' => 'User not found in the system.'
                ], 404);
            }

            // Fetch current status and toggle
            $currentStatus = $userData['disabled'] ?? false;
            $newStatus = !$currentStatus; // Toggle the status

            \Log::info("Current status: " . ($currentStatus ? 'disabled' : 'enabled') . ", New status: " . ($newStatus ? 'disabled' : 'enabled'));

            // Update Firestore
            $db->updateDocument('Users', $uid, ['disabled' => $newStatus]);

            // Log status change clearly
            \Log::info("✅ User {$uid} was " . ($newStatus ? 'DISABLED ❌' : 'ENABLED ✅'));

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "User successfully " . ($newStatus ? 'disabled' : 'enabled') . "."
            ], 200);

        } catch (\Exception $e) {
            \Log::error("❌ Error updating user status for {$uid}: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update lecturer status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleAllLecturersStatus(Request $request)
    {
        try {
            // Increase execution time limit for bulk operations
            set_time_limit(300); // 5 minutes
            ini_set('max_execution_time', 300);
            
            // Increase memory limit if needed
            ini_set('memory_limit', '512M');
            
            $disable = $request->input('disable') === true || $request->input('disable') === 'true';
            \Log::info("Bulk toggle all lecturers to: " . ($disable ? 'disabled' : 'enabled'));
            
            $db = app(FirestoreRestService::class);
            $lecturers = $db->queryCollection('Users', 'role', '==', 'lecturer');

            $updateCount = 0;
            $failedCount = 0;
            $batchSize = 50;
            $currentBatch = 0;
            
            foreach ($lecturers as $lecturer) {
                    try {
                        $db->updateDocument('Users', $lecturer['id'], ['disabled' => $disable]);
                        \Log::info("Lecturer {$lecturer['id']} status set to " . ($disable ? 'DISABLED' : 'ENABLED'));
                        $updateCount++;
                        $currentBatch++;
                        
                        if ($currentBatch >= $batchSize) {
                            usleep(100000);
                            $currentBatch = 0;
                            \Log::info("Processed batch of {$batchSize} lecturers. Total processed: {$updateCount}");
                        }
                    } catch (\Exception $e) {
                        $failedCount++;
                        \Log::error("Failed to update lecturer {$lecturer['id']}: " . $e->getMessage());
                    }
            }

            $message = "Successfully updated {$updateCount} lecturers.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} failed.";
            }

            \Log::info("Bulk toggle completed. Success: {$updateCount}, Failed: {$failedCount}");

            return response()->json([
                'success' => true, 
                'message' => $message
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Bulk lecturer toggle failed: ' . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update lecturers: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearLecturerCourses($uid)
    {
        try {
            \Log::info("Attempting to clear courses for lecturer UID: {$uid}");
            
            $db = app(FirestoreRestService::class);
            $userData = $db->getDocument('Users', $uid);

            if (!$userData) {
                \Log::error("❌ User with UID {$uid} not found in Firestore.");
                return response()->json([
                    'success' => false, 
                    'message' => 'User not found in the system.'
                ], 404);
            }

            // Check if user is a lecturer
            if (($userData['role'] ?? '') !== 'lecturer') {
                return response()->json([
                    'success' => false, 
                    'message' => 'User is not a lecturer.'
                ], 400);
            }

            // Clear the courses array
            $db->updateDocument('Users', $uid, ['courses' => []]);

            \Log::info("✅ Courses cleared for lecturer {$uid}");

            return response()->json([
                'success' => true,
                'message' => "Courses successfully cleared for lecturer."
            ], 200);

        } catch (\Exception $e) {
            \Log::error("❌ Error clearing courses for lecturer {$uid}: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to clear lecturer courses: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearAllLecturersCourses(Request $request)
    {
        try {
            // Increase execution time limit for bulk operations
            set_time_limit(300); // 5 minutes
            ini_set('max_execution_time', 300);
            
            // Increase memory limit if needed
            ini_set('memory_limit', '512M');
            
            $lecturerIds = $request->input('lecturer_ids', []);
            
            if (empty($lecturerIds)) {
                // Clear courses for all lecturers
                \Log::info("Bulk clear courses for all lecturers");
                
                $db = app(FirestoreRestService::class);
                $lecturers = $db->queryCollection('Users', 'role', '==', 'lecturer');

                $updateCount = 0;
                $batchSize = 50;
                $currentBatch = 0;
                
                foreach ($lecturers as $lecturer) {
                        try {
                            $db->updateDocument('Users', $lecturer['id'], ['courses' => []]);
                            \Log::info("Courses cleared for lecturer {$lecturer['id']}");
                            $updateCount++;
                            $currentBatch++;
                            
                            if ($currentBatch >= $batchSize) {
                                usleep(100000);
                                $currentBatch = 0;
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to clear courses for lecturer {$lecturer['id']}: " . $e->getMessage());
                        }
                }

                return response()->json([
                    'success' => true, 
                    'message' => "Successfully cleared courses for {$updateCount} lecturers."
                ], 200);
            } else {
                // Clear courses for specific lecturers
                \Log::info("Bulk clear courses for selected lecturers: " . implode(', ', $lecturerIds));
                
                $db = app(FirestoreRestService::class);
                $updateCount = 0;
                $failedCount = 0;
                $batchSize = 50;
                $currentBatch = 0;

                foreach ($lecturerIds as $uid) {
                    try {
                        $userData = $db->getDocument('Users', $uid);

                        if ($userData) {
                            if (($userData['role'] ?? '') === 'lecturer') {
                                $db->updateDocument('Users', $uid, ['courses' => []]);
                                $updateCount++;
                                $currentBatch++;
                                \Log::info("Courses cleared for lecturer {$uid}");
                                
                                if ($currentBatch >= $batchSize) {
                                    usleep(100000);
                                    $currentBatch = 0;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $failedCount++;
                        \Log::error("Failed to clear courses for lecturer {$uid}: " . $e->getMessage());
                    }
                }

                $message = "Successfully cleared courses for {$updateCount} lecturers.";
                if ($failedCount > 0) {
                    $message .= " {$failedCount} failed.";
                }

                return response()->json([
                    'success' => true, 
                    'message' => $message
                ], 200);
            }
            
        } catch (\Exception $e) {
            \Log::error('Bulk lecturer courses clear failed: ' . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to clear lecturer courses: ' . $e->getMessage()
            ], 500);
        }
    }

    // Control Lecturers ends here

    /**
     * Archive all exams to a collection named by year and semester (e.g., archive_April_2025)
     */
    public function archiveExams(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'semester' => 'required|in:April,August,December',
        ]);

        $year = $request->input('year');
        $semester = $request->input('semester');
        $archiveCollection = 'archive_' . $semester . '_' . $year;

        try {
            $db = app(FirestoreRestService::class);
            $exams = $db->getCollection('Exams');

            $archivedCount = 0;
            foreach ($exams as $exam) {
                $examId = $exam['id'];
                unset($exam['id']); // Don't store the id field as data
                $db->setDocument($archiveCollection, $examId, $exam);
                $archivedCount++;
            }

            // Optionally, clear the Exams collection after archiving
            // foreach ($exams as $exam) {
            //     if ($exam->exists()) {
            //         $examsRef->document($exam->id())->delete();
            //     }
            // }

            return redirect()->back()->with('success', "Archived {$archivedCount} exams to {$archiveCollection}.");
        } catch (\Exception $e) {
            \Log::error('Error archiving exams: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to archive exams: ' . $e->getMessage());
        }
    }

    /**
     * Start archiving exams (AJAX, returns job id)
     */
    public function startArchiveExams(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'semester' => 'required|in:April,August,December',
        ]);
        $year = $request->input('year');
        $semester = $request->input('semester');
        $archiveCollection = 'archive_' . $semester . '_' . $year;
        $jobId = Str::uuid()->toString();
        // Start the archive process (sync for now, but chunked with progress)
        try {
            set_time_limit(600); // 10 minutes
            ini_set('max_execution_time', 600);
            $db = app(FirestoreRestService::class);
            $exams = $db->getCollection('Exams');
            $total = count($exams);
            $archived = 0;
            Cache::put('archive_progress_' . $jobId, 0, 600);
            foreach ($exams as $exam) {
                    $examId = $exam['id'];
                    unset($exam['id']);
                    $db->setDocument($archiveCollection, $examId, $exam);
                    $archived++;
                    $progress = $total > 0 ? intval(($archived / $total) * 100) : 100;
                    Cache::put('archive_progress_' . $jobId, $progress, 600);
            }
            Cache::put('archive_progress_' . $jobId, 100, 600);
            return response()->json(['job_id' => $jobId, 'message' => 'Archive started.']);
        } catch (\Exception $e) {
            Cache::put('archive_progress_' . $jobId, 100, 600);
            return response()->json(['message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Poll archive progress by job id
     */
    public function archiveExamsProgress($jobId)
    {
        $progress = Cache::get('archive_progress_' . $jobId, 0);
        return response()->json(['progress' => $progress]);
    }

    /**
     * Start deleting exams (AJAX, returns job id)
     */
    public function startDeleteExams(Request $request)
    {
        $request->validate([
            'delete_option' => 'required|in:all,number',
            'delete_count' => 'nullable|integer|min:1',
        ]);
        $option = $request->input('delete_option');
        $count = $request->input('delete_count');
        $jobId = Str::uuid()->toString();
        try {
            set_time_limit(600);
            ini_set('max_execution_time', 600);
            $db = app(FirestoreRestService::class);
            $exams = $db->getCollection('Exams');
            $total = ($option === 'number' && $count) ? min($count, count($exams)) : count($exams);
            $deleted = 0;
            Cache::put('delete_exams_progress_' . $jobId, 0, 600);
            foreach ($exams as $exam) {
                if ($deleted >= $total) break;
                    $db->deleteDocument('Exams', $exam['id']);
                    $deleted++;
                    $progress = $total > 0 ? intval(($deleted / $total) * 100) : 100;
                    Cache::put('delete_exams_progress_' . $jobId, $progress, 600);
            }
            Cache::put('delete_exams_progress_' . $jobId, 100, 600);
            return response()->json(['job_id' => $jobId, 'message' => 'Delete started.']);
        } catch (\Exception $e) {
            Cache::put('delete_exams_progress_' . $jobId, 100, 600);
            return response()->json(['message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Poll delete exams progress by job id
     */
    public function deleteExamsProgress($jobId)
    {
        $progress = Cache::get('delete_exams_progress_' . $jobId, 0);
        return response()->json(['progress' => $progress]);
    }
}
