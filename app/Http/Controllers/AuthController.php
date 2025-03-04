<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class AuthController extends Controller
{
    protected $firebaseAuth;
    
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
    
        $this->firebaseAuth = $firebaseFactory->createAuth();
    }
    
    



    /**
    * Show the login form.
    *
    * @return \Illuminate\Http\Response
    */
    public function showLoginForm()
    {
        return view('login');
    }




    /**
    * Authenticate the user using Firebase.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword($credentials['email'], $credentials['password']);
            $uid = $signInResult->firebaseUserId();

            // Store user details in session
            session()->put('user_email', $credentials['email']);
            session()->put('user', $uid);

            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $userRef = $database->collection('Users')->document($uid);
            $userSnapshot = $userRef->snapshot();

            if (!$userSnapshot->exists()) {
                throw new \Exception("User with uid {$uid} does not exist.");
            }

            $userData = $userSnapshot->data();

            // Fetch faculty and store it
            $faculty = $userData['faculty'] ?? null;
            if (!$faculty) {
                throw new \Exception("Faculty information is missing for user: {$uid}");
            }

            // Store faculty in session
            session()->put('user_faculty', $faculty);

            // Fetch the profile picture URL from Firebase Storage
            $storage = app('firebase.storage');
            $bucket = $storage->getBucket();
            $imagePath = $userData['profile_picture'] ?? null;
            $profilePictureUrl = null;
            if ($imagePath) {
                $imageReference = $bucket->object($imagePath);
                $profilePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(new \DateTime('+5 minutes')) : null;
            }
            \Log::info('Profile picture URL: ' . session('profile_picture'));

            session()->put('user_firstName', $userData['firstName'] ?? 'Unknown');
            session()->put('user_role', $userData['role']);
            session()->put('profile_picture', $profilePictureUrl);
            \Log::info('firstName: ' . session('user_firstName'));
            \Log::info('faculty: ' . session('user_faculty')); // Log faculty

            // Check user role and redirect accordingly
            switch ($userData['role']) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'lecturer':
                    return redirect('/lecturer/lecturer.l-upload-questions');
                case 'superadmin':
                    return redirect('/superadmin/super-adm-dashboard');
                case 'genadmin':
                    return redirect('/genadmin/gen-dashboard');
                case 'dean':
                    return redirect('/deans/dean-dashboard');
                default:
                    return redirect('/login')->withErrors(['login_error' => 'No valid role assigned to this user.']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['login_error' => 'Invalid login credentials: ' . $e->getMessage()]);
        }
    }





    // Added showForgetPasswordForm method
    public function showForgetPasswordForm()
    {
        return view('forget-password'); // Ensure this matches your Blade template name
    }


    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            // Check if user exists in Firebase
            $user = $this->firebaseAuth->getUserByEmail($request->email);

            // If the user exists, send the password reset link
            $this->firebaseAuth->sendPasswordResetLink($request->email);

            return back()->with('status', 'Password reset link sent to your email.');
        } catch (UserNotFound $e) {
            // User not found in Firebase
            return back()->with('error', 'No user found with this email address.');
        } catch (\Throwable $e) {
            // Other errors
            return back()->with('error', 'Unable to send password reset link.');
        }
    }


    /**
     * 
     * 
     * Log out the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        session()->forget('user');
        // You can also sign out the Firebase user if needed using $this->firebaseAuth->signOut()
        
        return redirect('/login');
    }
}