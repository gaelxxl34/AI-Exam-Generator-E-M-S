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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
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

            $storage = app('firebase.storage')->getBucket();
            $imagePath = 'lecturer_images/' . uniqid() . '_' . $request->file('image')->getClientOriginalName();
            $uploadedFile = fopen($request->file('image')->path(), 'r');
            $storage->upload($uploadedFile, ['name' => $imagePath]);
            \Log::info('Image uploaded to Firebase Storage: ' . $imagePath);

            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $usersRef = $database->collection('Users');

            $usersRef->document($createdUser->uid)->set([
                'firstName' => $validatedData['firstName'],
                'lastName' => $validatedData['lastName'],
                'email' => $validatedData['email'],
                'profile_picture' => $imagePath,
                'role' => 'lecturer',
                'faculty' => $validatedData['faculty'], // Use the faculty from the validated data
                'courses' => $validatedData['courses'],
            ]);
            \Log::info('Lecturer data added to Firestore with courses');

            return redirect()->intended('/superadmin/lecturer-list')->with('success', 'Lecturer registered successfully.');
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
            $usersRef = $database->collection('Users');

            // Query for all lecturers regardless of faculty
            $lecturerQuery = $usersRef->where('role', '=', 'lecturer');
            $lecturerSnapshot = $lecturerQuery->documents();

            $lecturersByFaculty = [];
            $storage = app('firebase.storage');
            $bucket = $storage->getBucket();

            foreach ($lecturerSnapshot as $lecturer) {
                $lecturerData = $lecturer->data();
                $faculty = $lecturerData['faculty'] ?? 'Other';
                $imageReference = $bucket->object($lecturerData['profile_picture']);

                // Generate a signed URL for the image
                $profilePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(new \DateTime('+5 minutes')) : null;

                $lecturersByFaculty[$faculty][] = [
                    'id' => $lecturer->id(),
                    'firstName' => $lecturerData['firstName'] ?? 'N/A',
                    'lastName' => $lecturerData['lastName'] ?? 'N/A',
                    'email' => $lecturerData['email'] ?? 'N/A',
                    'profile_picture' => $profilePictureUrl,
                ];
            }

            \Log::info('Lecturers fetched by faculty with image URLs.');
            return view('superadmin.lecturer-list', ['lecturersByFaculty' => $lecturersByFaculty]);
        } catch (\Exception $e) {
            \Log::error('Error in lecturerList: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }




    public function editLecturer($id)
    {
        try {
            // Get a reference to the Firestore database
            $database = app('firebase.firestore')->database();

            // Query Firestore to get the lecturer by ID
            $lecturerRef = $database->collection('Users')->document($id);
            $lecturerSnapshot = $lecturerRef->snapshot();

            if ($lecturerSnapshot->exists()) {
                // Initialize Firebase Storage
                $storage = app('firebase.storage');
                $bucket = $storage->getBucket();

                // Fetch profile picture URL
                $imageReference = $bucket->object($lecturerSnapshot->data()['profile_picture']);
                $profilePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : null;

                // Prepare lecturer data
                $lecturerData = [
                    'id' => $lecturerSnapshot->id(),
                    'firstName' => $lecturerSnapshot->data()['firstName'] ?? 'N/A',
                    'lastName' => $lecturerSnapshot->data()['lastName'] ?? 'N/A',
                    'email' => $lecturerSnapshot->data()['email'] ?? 'N/A',
                    'profile_picture' => $profilePictureUrl,
                ];

                return view('superadmin.edit-lecturer', ['lecturer' => $lecturerData]);
            } else {
                return 'lecturer not found';
            }
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
                'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif', // Adjusted validation rule
            ]);

            // Initialize Firebase services
            $firestore = app('firebase.firestore')->database();
            $auth = app('firebase.auth');
            $storage = app('firebase.storage')->getBucket();

            // Update Firestore Data
            $lecturerRef = $firestore->collection('Users')->document($id);
            $lecturerRef->update([
                ['path' => 'firstName', 'value' => $validatedData['firstName']],
                ['path' => 'lastName', 'value' => $validatedData['lastName']],
                ['path' => 'email', 'value' => $validatedData['email']], // Update email in Firestore
                // ... other non-image fields ...
            ]);

            // Update Firestore Data
            $lecturerRef = $firestore->collection('Users')->document($id);
            $lecturerSnapshot = $lecturerRef->snapshot();

            // Update Profile Picture in Firebase Storage if provided
            if ($request->hasFile('profilePicture')) { // Make sure the field name matches
                // Retrieve the old image path from Firestore
                $oldImagePath = $lecturerSnapshot->data()['profile_picture']; // Corrected access to Firestore snapshot data

                // Delete the old image from Firebase Storage, if it exists
                if ($oldImagePath) {
                    $storage->object($oldImagePath)->delete();
                }

                // Upload the new image
                $image = $request->file('profilePicture'); // Adjusted field name
                $newImageName = 'lecturer_images/' . time() . '.' . $image->getClientOriginalExtension();
                $storage->upload(
                    file_get_contents($image->getRealPath()),
                    ['name' => $newImageName]
                );

                // Update Firestore with new image path
                $lecturerRef->update([
                    ['path' => 'profile_picture', 'value' => $newImageName],
                ]);
            }

           
            // Update Firebase Authentication Email
            $user = $auth->getUser($id);
            if ($validatedData['email'] != $user->email) {
                $auth->changeUserEmail($id, $validatedData['email']);
            }

            return back()->with('success', 'lecturer updated successfully.');

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
            return redirect()->route('admin.lecturer-list')->with('success', 'Lecturer deleted successfully.');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return back()->with('error', 'Error deleting lecturer: ' . $e->getMessage());
        }
    }

}

           
   