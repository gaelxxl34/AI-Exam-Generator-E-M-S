<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    protected $firebaseAuth;
    protected $firebaseFirestore; // Add this property
    
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
        // Fix: Get the correct Firestore database instance
        $this->firebaseFirestore = $firebaseFactory->createFirestore()->database();
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
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword(
                $credentials['email'],
                $credentials['password']
            );
    
            $uid = $signInResult->firebaseUserId();
    
            $userSnapshot = $this->firebaseFirestore
                ->collection('Users')
                ->document($uid)
                ->snapshot();
    
            if (!$userSnapshot->exists()) {
                return back()->withErrors(['login_error' => 'Account not found in our database.']);
            }
    
            $userData = $userSnapshot->data();
    
            if (!empty($userData['disabled'])) {
                return back()->withErrors(['login_error' => 'Your account is disabled.']);
            }
    
            $faculty = [];
            if (!empty($userData['faculties']) && is_array($userData['faculties'])) {
                $faculty = $userData['faculties'];
            } elseif (!empty($userData['faculty'])) {
                $faculty = [(string) $userData['faculty']];
            }
    
            if (empty($faculty)) {
                return back()->withErrors(['login_error' => 'Faculty info missing.']);
            }
    
            session()->put([
                'user_email'     => $credentials['email'],
                'user'           => $uid,
                'user_faculty'   => $faculty,
                'user_firstName' => $userData['firstName'] ?? 'Unknown',
                'user_role'      => $userData['role'] ?? 'unknown',
            ]);
    
            return match ($userData['role'] ?? '') {
                'admin'      => redirect()->route('admin.dashboard'),
                'lecturer'   => redirect()->route('lecturer.l-upload-questions'),
                'superadmin' => redirect()->route('superadmin.super-admin-dashboard'),
                'genadmin'   => redirect()->route('genadmin.gen-dashboard'),
                'dean'       => redirect()->route('dean.dashboard'),
                default      => throw new \Exception("No valid role assigned to user."),
            };
    
        } catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
            Log::warning('Invalid password attempt for: ' . $credentials['email']);
            return redirect()->route('login')->withErrors(['login_error' => 'Invalid email or password.']);
    
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            Log::warning('User not found: ' . $credentials['email']);
            return redirect()->route('login')->withErrors(['login_error' => 'Your account does not exist.']);
    
        } catch (\Throwable $e) {
            // Enhanced logging for debugging
            Log::error('===== AUTHENTICATION ERROR =====');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Error code: ' . $e->getCode());
            Log::error('Error class: ' . get_class($e));
            Log::error('Error file: ' . $e->getFile() . ' (line ' . $e->getLine() . ')');
            Log::error('Error trace: ' . $e->getTraceAsString());
            Log::error('Request IP: ' . $request->ip());
            Log::error('Session ID: ' . session()->getId());
            Log::error('Email attempting login: ' . ($credentials['email'] ?? 'not provided'));
            Log::error('================================');
            
            // Show a more descriptive error message in development
            if (config('app.debug')) {
                return redirect()->route('login')->withErrors(['login_error' => 'Error: ' . $e->getMessage()]);
            }
            
            // Generic message for production
            return redirect()->route('login')->withErrors(['login_error' => 'Authentication failed. Please try again later.']);
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