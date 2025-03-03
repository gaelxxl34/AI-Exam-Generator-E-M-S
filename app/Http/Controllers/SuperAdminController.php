<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

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
                // Initialize Firebase Storage
                $storage = app('firebase.storage');
                $bucket = $storage->getBucket();

                // Fetch profile picture URL
                $imageReference = $bucket->object($adminSnapshot->data()['profile_picture']);
                $profilePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : null;

                // Prepare admin data
                $adminData = [
                    'id' => $adminSnapshot->id(),
                    'firstName' => $adminSnapshot->data()['firstName'] ?? 'N/A',
                    'lastName' => $adminSnapshot->data()['lastName'] ?? 'N/A',
                    'email' => $adminSnapshot->data()['email'] ?? 'N/A',
                    'faculty' => $adminSnapshot->data()['faculty'] ?? 'N/A',
                    'profile_picture' => $profilePictureUrl,
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
            // Validation
            $validatedData = $request->validate([
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required|email',
                'role' => 'required',
                'faculty' => 'required',
                'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif', // Adjusted validation rule
            ]);

            // Initialize Firebase services
            $firestore = app('firebase.firestore')->database();
            $storage = app('firebase.storage')->getBucket();

            // Update Firestore Data
            $adminRef = $firestore->collection('Users')->document($id);
            $adminRef->update([
                ['path' => 'firstName', 'value' => $validatedData['firstName']],
                ['path' => 'lastName', 'value' => $validatedData['lastName']],
                ['path' => 'email', 'value' => $validatedData['email']],
                ['path' => 'role', 'value' => $validatedData['role']],
                ['path' => 'faculty', 'value' => $validatedData['faculty']],
            ]);

            // Update Profile Picture in Firebase Storage if provided
            if ($request->hasFile('profilePicture')) {
                $oldImageRef = $adminRef->snapshot()->data()['profile_picture'] ?? null;

                // Delete the old image from Firebase Storage, if it exists
                if ($oldImageRef) {
                    $storage->object($oldImageRef)->delete();
                }

                // Upload the new image
                $image = $request->file('profilePicture');
                $newImageName = 'admin_images/' . time() . '.' . $image->getClientOriginalExtension();
                $newImageRef = $storage->upload(
                    file_get_contents($image->getRealPath()),
                    ['name' => $newImageName]
                );

                // Update Firestore with new image path
                $adminRef->update([
                    ['path' => 'profile_picture', 'value' => $newImageName],
                ]);
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

}
