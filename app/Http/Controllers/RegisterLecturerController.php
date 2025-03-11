<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Kreait\Firebase\Factory;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;



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

            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $usersRef = $database->collection('Users');

            $usersRef->document($createdUser->uid)->set([
                'firstName' => $validatedData['firstName'],
                'lastName' => $validatedData['lastName'],
                'email' => $validatedData['email'],
                'created_at' => new \DateTime(),
                'role' => 'lecturer',
                'faculties' => $validatedData['faculties'], // Store as an array
                'courses' => $validatedData['courses'], // Store courses as an array
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
            $currentUserData = $currentUserDocument->data();
            $userFaculty = $currentUserData['faculty'] ?? 'default_faculty';

            // Query for all lecturers
            $lecturerQuery = $database->collection('Users')->where('role', '=', 'lecturer');
            $lecturerSnapshot = $lecturerQuery->documents();

            $lecturersByFaculty = [];

            foreach ($lecturerSnapshot as $lecturer) {
                $lecturerData = $lecturer->data();
                $lecturerFaculties = $lecturerData['faculties'] ?? []; // Get faculties array

                // Check if the lecturer is part of the user's faculty
                if (in_array($userFaculty, $lecturerFaculties)) {
                    $lecturersByFaculty[$userFaculty][] = [
                        'id' => $lecturer->id(),
                        'firstName' => $lecturerData['firstName'] ?? 'N/A',
                        'lastName' => $lecturerData['lastName'] ?? 'N/A',
                        'email' => $lecturerData['email'] ?? 'N/A',
                    ];
                }
            }

            \Log::info('Lecturers fetched by faculty.');
            return view('admin.lecturer-list', ['lecturersByFaculty' => $lecturersByFaculty]);
        } catch (\Exception $e) {
            \Log::error('Error in lecturerList: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to fetch lecturers.']);
        }
    }





public function editLecturer($id)
{
    try {
        $firestore = app('firebase.firestore')->database();

        // Fetch logged-in user's faculty
        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
        $usersRef = $firestore->collection('Users');
        $userQuery = $usersRef->where('email', '==', $currentUserEmail);
        $currentUserSnapshots = $userQuery->documents();

        if ($currentUserSnapshots->isEmpty()) {
            \Log::error("❌ Firestore user not found with email: $currentUserEmail");
            return back()->withErrors(['error' => 'Current user not found in Firestore.']);
        }

        $currentUserData = iterator_to_array($currentUserSnapshots)[0]->data();
        $adminFaculty = $currentUserData['faculty'] ?? null;

        if (!$adminFaculty) {
            \Log::error("❌ Current user has no assigned faculty.");
            return back()->withErrors(['error' => 'Faculty information missing for current user.']);
        }

        // Fetch lecturer data by ID
        $lecturerRef = $firestore->collection('Users')->document($id);
        $lecturerSnapshot = $lecturerRef->snapshot();

        if (!$lecturerSnapshot->exists()) {
            return back()->withErrors(['error' => 'Lecturer not found']);
        }

        // Prepare lecturer data
        $lecturerData = [
            'id' => $lecturerSnapshot->id(),
            'firstName' => $lecturerSnapshot->data()['firstName'] ?? 'N/A',
            'lastName' => $lecturerSnapshot->data()['lastName'] ?? 'N/A',
            'email' => $lecturerSnapshot->data()['email'] ?? 'N/A',
            'faculties' => $lecturerSnapshot->data()['faculties'] ?? [], // Fetch faculties array
            'courses' => $lecturerSnapshot->data()['courses'] ?? [], // Fetch courses array
        ];

        // Define available faculties (Static List)
        $availableFaculties = ['FST', 'FBM', 'FOE', 'FOL', 'HEC'];

        // Fetch only courses from the logged-in user's faculty
        $coursesRef = $firestore->collection('Courses');
        $coursesQuery = $coursesRef->where('faculty', '==', $adminFaculty);
        $coursesSnapshot = $coursesQuery->documents();

        $courseNames = [];
        foreach ($coursesSnapshot as $course) {
            if ($course->exists()) {
                $courseData = $course->data();
                $courseNames[] = [
                    'name' => $courseData['name'], // Course name
                    'id' => $course->id(), // Document ID
                ];
            }
        }

        return view('admin.edit-lecturer', [
            'lecturer' => $lecturerData,
            'availableFaculties' => $availableFaculties, // ✅ FIX: Ensure it's passed to the view
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
                'courses' => 'required|array|min:1', // Ensure at least one course is selected
            ]);

            $firestore = app('firebase.firestore')->database();
            $auth = app('firebase.auth');

            // Reference to Firestore document
            $lecturerRef = $firestore->collection('Users')->document($id);

            // Update lecturer data
            $lecturerRef->update([
                ['path' => 'firstName', 'value' => $validatedData['firstName']],
                ['path' => 'lastName', 'value' => $validatedData['lastName']],
                ['path' => 'email', 'value' => $validatedData['email']], // Update email
                ['path' => 'faculties', 'value' => $validatedData['faculties']], // Update faculties
                ['path' => 'courses', 'value' => $validatedData['courses']], // Update courses
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
            $firestore = app('firebase.firestore')->database();
            $auth = app('firebase.auth');
            $storage = app('firebase.storage')->getBucket();

            // Get the Firestore document reference
            $lecturerRef = $firestore->collection('Users')->document($id);

            // Delete the Firestore document
            $lecturerRef->delete();

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

           
   