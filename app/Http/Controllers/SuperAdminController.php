<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Auth as FirebaseAuth;

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


            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $usersRef = $database->collection('Users');

            $usersRef->document($createdUser->uid)->set([
                'firstName' => $validatedData['firstName'],
                'lastName' => $validatedData['lastName'],
                'email' => $validatedData['email'],
                // 'profile_picture' => $imagePath,
                'created_at' => new \DateTime(),
                'faculty' => $validatedData['faculty'], // Use the faculty from the validated data
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
            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $usersRef = $database->collection('Users');

            // Query for users where role is 'admin', 'dean' or 'genadmin'
            $adminQuery = $usersRef->where('role', 'in', ['admin', 'genadmin', 'dean']);
            $adminSnapshot = $adminQuery->documents();

            $adminsByRole = [];
            $storage = app('firebase.storage');
            $bucket = $storage->getBucket();

            foreach ($adminSnapshot as $admin) {
                $adminData = $admin->data();
                $role = $adminData['role'] ?? 'Other';
                // $imageReference = $bucket->object($adminData['profile_picture']);

                // Generate a signed URL for the image
                // $profilePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(new \DateTime('+5 minutes')) : null;

                $adminsByRole[$role][] = [
                    'id' => $admin->id(),
                    'firstName' => $adminData['firstName'] ?? 'N/A',
                    'lastName' => $adminData['lastName'] ?? 'N/A',
                    'email' => $adminData['email'] ?? 'N/A',
                    'role' => $adminData['role'] ?? 'N/A',
                    // 'profile_picture' => $profilePictureUrl,
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
            // Get a reference to the Firestore database
            $database = app('firebase.firestore')->database();

            // Query Firestore to get the admin by ID
            $adminRef = $database->collection('Users')->document($id);
            $adminSnapshot = $adminRef->snapshot();

            if ($adminSnapshot->exists()) {
              
                // Prepare admin data
                $adminData = [
                    'id' => $adminSnapshot->id(),
                    'firstName' => $adminSnapshot->data()['firstName'] ?? 'N/A',
                    'lastName' => $adminSnapshot->data()['lastName'] ?? 'N/A',
                    'email' => $adminSnapshot->data()['email'] ?? 'N/A',
                    'faculty' => $adminSnapshot->data()['faculty'] ?? 'N/A',
                    'role' => $adminSnapshot->data()['role'] ?? 'N/A',  // Include role in the data
                ];

                return view('superadmin.edit-admin', ['admin' => $adminData]); // Ensure the view name is correct
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
            $firestore = app('firebase.firestore')->database();
            $auth = app('firebase.auth');

            // Fetch the current user data
            $adminRef = $firestore->collection('Users')->document($id);
            $adminSnapshot = $adminRef->snapshot();

            if (!$adminSnapshot->exists()) {
                return back()->with('error', 'User not found.');
            }

            $currentData = $adminSnapshot->data();
            $currentEmail = $currentData['email'] ?? null;

            // **Update Firestore Data**
            $adminRef->update([
                ['path' => 'firstName', 'value' => $validatedData['firstName']],
                ['path' => 'lastName', 'value' => $validatedData['lastName']],
                ['path' => 'email', 'value' => $validatedData['email']],
                ['path' => 'role', 'value' => $validatedData['role']],
                ['path' => 'faculty', 'value' => $validatedData['faculty']],
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
            // Initialize Firebase services
            $firestore = app('firebase.firestore')->database();
            $auth = app('firebase.auth');
            $storage = app('firebase.storage')->getBucket();

            // Get the Firestore document reference
            $lecturerRef = $firestore->collection('Users')->document($id);
            $lecturerSnapshot = $lecturerRef->snapshot();

            // Delete profile picture from Firebase Storage if it exists
            if ($lecturerSnapshot->exists() && isset($lecturerSnapshot['profile_picture'])) {
                $profilePicturePath = $lecturerSnapshot->data()['profile_picture'];
                if ($storage->object($profilePicturePath)->exists()) {
                    $storage->object($profilePicturePath)->delete();
                }
            }

            // Delete the Firestore document
            $lecturerRef->delete();

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
            $firestore = app('firebase.firestore')->database();
            $usersRef = $firestore->collection('Users')->where('role', '==', 'lecturer');
            $lecturers = $usersRef->documents();

            $lecturersByFaculty = [];
            
            foreach ($lecturers as $lecturer) {
                if ($lecturer->exists()) {
                    $data = $lecturer->data();
                    
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
                        'id' => $lecturer->id(),
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
            
            $firestore = app('firebase.firestore')->database();
            $userRef = $firestore->collection('Users')->document($uid);
            $userSnapshot = $userRef->snapshot();

            if (!$userSnapshot->exists()) {
                \Log::error("❌ User with UID {$uid} not found in Firestore.");
                return response()->json([
                    'success' => false, 
                    'message' => 'User not found in the system.'
                ], 404);
            }

            // Fetch current status and toggle
            $userData = $userSnapshot->data();
            $currentStatus = $userData['disabled'] ?? false;
            $newStatus = !$currentStatus; // Toggle the status

            \Log::info("Current status: " . ($currentStatus ? 'disabled' : 'enabled') . ", New status: " . ($newStatus ? 'disabled' : 'enabled'));

            // Update Firestore
            $userRef->update([
                ['path' => 'disabled', 'value' => $newStatus]
            ]);

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
            
            $firestore = app('firebase.firestore')->database();
            $usersRef = $firestore->collection('Users')->where('role', '==', 'lecturer');
            $lecturers = $usersRef->documents();

            $updateCount = 0;
            $failedCount = 0;
            $batchSize = 50; // Process in batches to avoid overwhelming Firebase
            $currentBatch = 0;
            
            foreach ($lecturers as $lecturer) {
                if ($lecturer->exists()) {
                    try {
                        $lecturer->reference()->update([
                            ['path' => 'disabled', 'value' => $disable]
                        ]);
                        \Log::info("Lecturer {$lecturer->id()} status set to " . ($disable ? 'DISABLED' : 'ENABLED'));
                        $updateCount++;
                        $currentBatch++;
                        
                        // Add small delay every batch to prevent rate limiting
                        if ($currentBatch >= $batchSize) {
                            usleep(100000); // 0.1 second delay
                            $currentBatch = 0;
                            \Log::info("Processed batch of {$batchSize} lecturers. Total processed: {$updateCount}");
                        }
                    } catch (\Exception $e) {
                        $failedCount++;
                        \Log::error("Failed to update lecturer {$lecturer->id()}: " . $e->getMessage());
                    }
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
            
            $firestore = app('firebase.firestore')->database();
            $userRef = $firestore->collection('Users')->document($uid);
            $userSnapshot = $userRef->snapshot();

            if (!$userSnapshot->exists()) {
                \Log::error("❌ User with UID {$uid} not found in Firestore.");
                return response()->json([
                    'success' => false, 
                    'message' => 'User not found in the system.'
                ], 404);
            }

            // Check if user is a lecturer
            $userData = $userSnapshot->data();
            if (($userData['role'] ?? '') !== 'lecturer') {
                return response()->json([
                    'success' => false, 
                    'message' => 'User is not a lecturer.'
                ], 400);
            }

            // Clear the courses array
            $userRef->update([
                ['path' => 'courses', 'value' => []]
            ]);

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
                
                $firestore = app('firebase.firestore')->database();
                $usersRef = $firestore->collection('Users')->where('role', '==', 'lecturer');
                $lecturers = $usersRef->documents();

                $updateCount = 0;
                $batchSize = 50; // Process in batches to avoid overwhelming Firebase
                $currentBatch = 0;
                
                foreach ($lecturers as $lecturer) {
                    if ($lecturer->exists()) {
                        try {
                            $lecturer->reference()->update([
                                ['path' => 'courses', 'value' => []]
                            ]);
                            \Log::info("Courses cleared for lecturer {$lecturer->id()}");
                            $updateCount++;
                            $currentBatch++;
                            
                            // Add small delay every batch to prevent rate limiting
                            if ($currentBatch >= $batchSize) {
                                usleep(100000); // 0.1 second delay
                                $currentBatch = 0;
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to clear courses for lecturer {$lecturer->id()}: " . $e->getMessage());
                        }
                    }
                }

                return response()->json([
                    'success' => true, 
                    'message' => "Successfully cleared courses for {$updateCount} lecturers."
                ], 200);
            } else {
                // Clear courses for specific lecturers
                \Log::info("Bulk clear courses for selected lecturers: " . implode(', ', $lecturerIds));
                
                $firestore = app('firebase.firestore')->database();
                $updateCount = 0;
                $failedCount = 0;
                $batchSize = 50; // Process in batches
                $currentBatch = 0;

                foreach ($lecturerIds as $uid) {
                    try {
                        $userRef = $firestore->collection('Users')->document($uid);
                        $userSnapshot = $userRef->snapshot();

                        if ($userSnapshot->exists()) {
                            $userData = $userSnapshot->data();
                            if (($userData['role'] ?? '') === 'lecturer') {
                                $userRef->update([
                                    ['path' => 'courses', 'value' => []]
                                ]);
                                $updateCount++;
                                $currentBatch++;
                                \Log::info("Courses cleared for lecturer {$uid}");
                                
                                // Add small delay every batch to prevent rate limiting
                                if ($currentBatch >= $batchSize) {
                                    usleep(100000); // 0.1 second delay
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

}
