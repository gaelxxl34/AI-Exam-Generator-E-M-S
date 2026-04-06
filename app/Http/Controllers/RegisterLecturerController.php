<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Kreait\Firebase\Factory;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Services\FirestoreRestService;



class RegisterLecturerController extends Controller
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



    public function registerLecturer(Request $request)
    {
        \Log::info('Register lecturer method called');

        // Validate input
        $validatedData = $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'faculties' => 'required|array|min:1', // Ensure at least one faculty is selected
            'courses' => 'required|array|min:1', // Ensure at least one course is selected
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
                'role' => 'lecturer',
                'faculties' => $validatedData['faculties'],
                'courses' => $validatedData['courses'],
            ]);

            \Log::info('Lecturer data added to Firestore with faculties and courses');

            return redirect()->intended('/admin/lecturer-list')->with('success', 'Lecturer registered successfully.');
        } catch (\Throwable $e) {
            \Log::error('Error registering lecturer: ' . $e->getMessage());
            return back()->withErrors(['upload_error' => 'Error registering lecturer.'])->with('message', 'Error registering lecturer: ' . $e->getMessage());
        }
    }





    public function lecturerList()
    {
        \Log::info('lecturerList method called');

        try {
            $db = app(FirestoreRestService::class);

            // Fetch the current user's email and faculty
            $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
            $currentUserSnapshots = $db->queryCollection('Users', 'email', '==', $currentUserEmail);

            if (empty($currentUserSnapshots)) {
                \Log::error("User not found with email: $currentUserEmail");
                throw new \Exception('User not found.');
            }

            $currentUserData = $currentUserSnapshots[0];
            $userFaculty = $currentUserData['faculty'] ?? 'default_faculty';

            \Log::info("🔍 User Faculty: $userFaculty");

            // Fetch all lecturers
            $lecturerSnapshot = $db->queryCollection('Users', 'role', '==', 'lecturer');

            $lecturersByFaculty = [];

            foreach ($lecturerSnapshot as $lecturer) {
                    $lecturerFaculties = $lecturer['faculties'] ?? [];
                    $lecturerCourses = $lecturer['courses'] ?? [];

                    // Check if lecturer belongs to the same faculty
                    if (is_array($lecturerFaculties) && in_array($userFaculty, $lecturerFaculties)) {
                        $lecturersByFaculty[$userFaculty][] = [
                            'id' => $lecturer['id'],
                            'firstName' => $lecturer['firstName'] ?? 'N/A',
                            'lastName' => $lecturer['lastName'] ?? 'N/A',
                            'email' => $lecturer['email'] ?? 'N/A',
                            'courses' => $lecturerCourses,
                        ];
                    }
            }

            \Log::info('✅ Lecturers fetched successfully.', ['count' => count($lecturersByFaculty)]);
            return view('admin.lecturer-list', ['lecturersByFaculty' => $lecturersByFaculty]);

        } catch (\Exception $e) {
            \Log::error('❌ Error in lecturerList: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to fetch lecturers.']);
        }
    }






    public function editLecturer($id)
    {
        try {
            $db = app(FirestoreRestService::class);

            // Fetch logged-in user's faculty
            $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
            $currentUserSnapshots = $db->queryCollection('Users', 'email', '==', $currentUserEmail);

            if (empty($currentUserSnapshots)) {
                \Log::error("Firestore user not found with email: $currentUserEmail");
                return back()->withErrors(['error' => 'Current user not found in Firestore.']);
            }

            $currentUserData = $currentUserSnapshots[0];
            $adminFaculty = $currentUserData['faculty'] ?? null;

            if (!$adminFaculty) {
                \Log::error("Current user has no assigned faculty.");
                return back()->withErrors(['error' => 'Faculty information missing for current user.']);
            }

            // Fetch lecturer data by ID
            $lecturerSnapshot = $db->getDocument('Users', $id);

            if ($lecturerSnapshot === null) {
                return back()->withErrors(['error' => 'Lecturer not found']);
            }

            // Prepare lecturer data
            $lecturerData = [
                'id' => $lecturerSnapshot['id'],
                'firstName' => $lecturerSnapshot['firstName'] ?? 'N/A',
                'lastName' => $lecturerSnapshot['lastName'] ?? 'N/A',
                'email' => $lecturerSnapshot['email'] ?? 'N/A',
                'faculties' => $lecturerSnapshot['faculties'] ?? [],
                'courses' => $lecturerSnapshot['courses'] ?? [],
            ];

            // Define available faculties (Static List)
            $availableFaculties = ['FST', 'FBM', 'FOE', 'FOL', 'HEC'];

            // Fetch only courses from the logged-in user's faculty
            $coursesSnapshot = $db->queryCollection('Courses', 'faculty', '==', $adminFaculty);

            $courseNames = [];
            foreach ($coursesSnapshot as $course) {
                    $courseNames[] = [
                        'id' => $course['id'],
                        'name' => $course['name'],
                        'code' => $course['code'] ?? 'N/A',
                    ];
            }

            return view('admin.edit-lecturer', [
                'lecturer' => $lecturerData,
                'availableFaculties' => $availableFaculties,
                'courseNames' => $courseNames, // Only courses matching the logged-in user's faculty
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error fetching lecturer: ' . $e->getMessage()]);
        }
    }






public function updateLecturer(Request $request, $id)
{
    try {
        // Validation
        $validatedData = $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'faculties' => 'required|array|min:1', // Ensure at least one faculty is selected
            'courses' => 'nullable|array', // Allow empty courses - lecturers can have no courses assigned
        ]);

        $db = app(FirestoreRestService::class);
        $auth = app('firebase.auth');

        // Get lecturer document
        $lecturerSnapshot = $db->getDocument('Users', $id);

        if ($lecturerSnapshot === null) {
            return back()->withErrors(['error' => 'Lecturer not found in Firestore.']);
        }

        // Get the current user's faculty (admin making the update)
        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
        $currentUserSnapshots = $db->queryCollection('Users', 'email', '==', $currentUserEmail);

        if (empty($currentUserSnapshots)) {
            return back()->withErrors(['error' => 'Current user not found.']);
        }

        $currentUserData = $currentUserSnapshots[0];
        $adminFaculty = $currentUserData['faculty'] ?? null;

        // Get existing lecturer data
        $existingCourses = $lecturerSnapshot['courses'] ?? [];

        // Get courses from admin's faculty to identify which ones to manage
        $coursesSnapshot = $db->queryCollection('Courses', 'faculty', '==', $adminFaculty);

        $adminFacultyCourses = [];
        foreach ($coursesSnapshot as $course) {
                $adminFacultyCourses[] = $course['name'];
        }

        // Remove courses from admin's faculty that are no longer selected
        $coursesToKeep = array_filter($existingCourses, function($course) use ($adminFacultyCourses) {
            return !in_array($course, $adminFacultyCourses);
        });

        // Merge with newly selected courses from admin's faculty (handle empty courses)
        $newCourses = $validatedData['courses'] ?? [];
        $mergedCourses = array_unique(array_merge(array_values($coursesToKeep), $newCourses));

        // Update lecturer data
        $db->updateDocument('Users', $id, [
            'firstName' => $validatedData['firstName'],
            'lastName' => $validatedData['lastName'],
            'email' => $validatedData['email'],
            'faculties' => $validatedData['faculties'],
            'courses' => array_values($mergedCourses),
        ]);

        // Update Firebase Authentication Email
        $user = $auth->getUser($id);
        if ($validatedData['email'] !== $user->email) {
            $auth->changeUserEmail($id, $validatedData['email']);
        }

        return back()->with('success', 'Lecturer updated successfully.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Error updating lecturer: ' . $e->getMessage()]);
    }
}





    public function deleteLecturer($id)
    {
        try {
            // Initialize Firebase services
            $db = app(FirestoreRestService::class);
            $auth = app('firebase.auth');

            // Delete the Firestore document
            $db->deleteDocument('Users', $id);

            // Delete the user from Firebase Authentication
            $auth->deleteUser($id);

            // Redirect to the lecturer list with a success message
            return redirect()->route('admin.lecturer-list')->with('success', 'Lecturer deleted successfully.');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return back()->with('error', 'Error deleting lecturer: ' . $e->getMessage());
        }
    }

}

           
   