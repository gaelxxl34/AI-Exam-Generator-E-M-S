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
        // Attempt to sign in the user with Firebase Auth
        $signInResult = $this->firebaseAuth->signInWithEmailAndPassword($credentials['email'], $credentials['password']);
        $uid = $signInResult->firebaseUserId();

        $firestore = app('firebase.firestore')->database();
        $userRef = $firestore->collection('Users')->document($uid);
        $userSnapshot = $userRef->snapshot();

        // ❌ If user does not exist in Firestore
        if (!$userSnapshot->exists()) {
            \Log::warning("❌ Login failed: User with UID {$uid} does not exist in Firestore.");
            return back()->withErrors(['login_error' => 'Your account does not exist in our system.']);
        }

        $userData = $userSnapshot->data();

        // 🔒 **Check if the user is disabled**
        if (!empty($userData['disabled']) && $userData['disabled'] === true) {
            \Log::warning("⛔ Disabled user {$uid} attempted to log in.");
            return back()->withErrors(['login_error' => 'Your account has been disabled. Please contact the Dean of your faculty to request reactivation.']);
        }

        // 🔹 **Handle Faculty (Array or String)**
        $faculty = [];
        if (!empty($userData['faculties']) && is_array($userData['faculties'])) {
            $faculty = $userData['faculties'];
        } elseif (!empty($userData['faculty']) && is_string($userData['faculty'])) {
            $faculty = [$userData['faculty']];
        }

        // ❗ **If faculty information is missing**
        if (empty($faculty)) {
            \Log::error("⚠ Faculty information missing for user: {$uid}");
            return back()->withErrors(['login_error' => 'Your faculty information is missing. Please contact the administrator.']);
        }

        // Store user details in session
        session()->put('user_email', $credentials['email']);
        session()->put('user', $uid);
        session()->put('user_faculty', $faculty);
        session()->put('user_firstName', $userData['firstName'] ?? 'Unknown');
        session()->put('user_role', $userData['role']);

        \Log::info("✅ User authenticated: {$credentials['email']} | Role: {$userData['role']} | Faculty: " . json_encode($faculty));

        // **Redirect based on user role**
        return match ($userData['role'] ?? '') {
            'admin' => redirect('/admin/dashboard'),
            'lecturer' => redirect('/lecturer/lecturer.l-upload-questions'),
            'superadmin' => redirect('/superadmin/super-adm-dashboard'),
            'genadmin' => redirect('/genadmin/gen-dashboard'),
            'dean' => redirect('/deans/dean-dashboard'),
            default => throw new \Exception("No valid role assigned to user {$uid}")
        };

    } catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
        \Log::warning("❌ Login failed: Incorrect password for {$credentials['email']}");
        return back()->withErrors(['login_error' => 'Invalid email or password.']);
    
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        \Log::warning("❌ Login failed: User not found in Firebase Auth - " . $credentials['email']);
        return back()->withErrors(['login_error' => 'Your account does not exist in our system.']);
    
    } catch (\Exception $e) {
        \Log::error("❌ Authentication error: " . $e->getMessage());
        return back()->withErrors(['login_error' => $e->getMessage()]);
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