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

        $validatedData = $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'faculty' => 'required|string', // Ensure faculty is a required input
            'courses' => 'required|array', // Validate that courses are provided and is an array
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
                'faculty' => $validatedData['faculty'], // Use the faculty from the validated data
                'courses' => $validatedData['courses'],
            ]);
            \Log::info('Lecturer data added to Firestore with courses');

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
            $facultyField = $currentUserData['faculty'] ?? 'default_faculty';

            // Query for lecturers only from the current user's faculty
            $lecturerQuery = $database->collection('Users')->where('role', '=', 'lecturer')->where('faculty', '=', $facultyField);
            $lecturerSnapshot = $lecturerQuery->documents();

            $lecturersByFaculty = [];

            foreach ($lecturerSnapshot as $lecturer) {
                $lecturerData = $lecturer->data();
                $faculty = $lecturerData['faculty'] ?? 'Other';

            
                $lecturersByFaculty[$faculty][] = [
                    'id' => $lecturer->id(),
                    'firstName' => $lecturerData['firstName'] ?? 'N/A',
                    'lastName' => $lecturerData['lastName'] ?? 'N/A',
                    'email' => $lecturerData['email'] ?? 'N/A',
                ];
            }

            \Log::info('Lecturers fetched by faculty with image URLs.');
            return view('admin.lecturer-list', ['lecturersByFaculty' => $lecturersByFaculty]);
        } catch (\Exception $e) {
            \Log::error('Error in lecturerList: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }





    public function editLecturer($id)
    {
        try {
            $firestore = app('firebase.firestore')->database();

            // Fetch lecturer data by ID
            $lecturerRef = $firestore->collection('Users')->document($id);
            $lecturerSnapshot = $lecturerRef->snapshot();

            if (!$lecturerSnapshot->exists()) {
                return 'Lecturer not found';
            }

            // Prepare lecturer data
            $lecturerData = [
                'id' => $lecturerSnapshot->id(),
                'firstName' => $lecturerSnapshot->data()['firstName'] ?? 'N/A',
                'lastName' => $lecturerSnapshot->data()['lastName'] ?? 'N/A',
                'email' => $lecturerSnapshot->data()['email'] ?? 'N/A',
                'faculty' => $lecturerSnapshot->data()['faculty'] ?? 'N/A',
                'courses' => $lecturerSnapshot->data()['courses'] ?? [], // Fetch the courses array
            ];

            // Fetch available courses based on lecturer's faculty
            $faculty = $lecturerSnapshot->data()['faculty'];
            $coursesRef = $firestore->collection('Courses')->where('faculty', '==', $faculty);
            $coursesSnapshot = $coursesRef->documents();

            $courseNames = [];
            foreach ($coursesSnapshot as $course) {
                if ($course->exists()) {
                    $courseNames[] = [
                        'name' => $course->data()['name'], // Assuming course name is stored in 'name'
                        'id' => $course->id() // Document ID
                    ];
                }
            }

            // Pass lecturer data and course names to the view
            return view('admin.edit-lecturer', [
                'lecturer' => $lecturerData,
                'courseNames' => $courseNames // All available courses for the faculty
            ]);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
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
                'courses' => 'required|array', // Validate that courses are passed as an array
            ]);

            // Initialize Firebase services
            $firestore = app('firebase.firestore')->database();
            $auth = app('firebase.auth');

            // Update Firestore Data for the lecturer
            $lecturerRef = $firestore->collection('Users')->document($id);

            // Update the necessary fields (First Name, Last Name, Email, and Courses)
            $lecturerRef->update([
                ['path' => 'firstName', 'value' => $validatedData['firstName']],
                ['path' => 'lastName', 'value' => $validatedData['lastName']],
                ['path' => 'email', 'value' => $validatedData['email']], // Update email in Firestore
                ['path' => 'courses', 'value' => $validatedData['courses']], // Update the courses field
            ]);

            // Update Firebase Authentication Email
            $user = $auth->getUser($id);
            if ($validatedData['email'] != $user->email) {
                $auth->changeUserEmail($id, $validatedData['email']);
            }

            return back()->with('success', 'Lecturer updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating lecturer: ' . $e->getMessage());
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

           
   