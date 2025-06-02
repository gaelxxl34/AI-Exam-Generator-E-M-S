<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CourseController;

class AuthController extends Controller
{
    protected $firebaseAuth;
    protected $firestore;
    
    public function __construct()
    {
        // Remove Firebase initialization from constructor to avoid recursion
        // Firebase services will be initialized lazily when needed
    }
    
    protected function getFirebaseAuth()
    {
        if (!$this->firebaseAuth) {
            $this->initializeFirebase();
        }
        return $this->firebaseAuth;
    }
    
    protected function getFirestore()
    {
        if (!$this->firestore) {
            $this->initializeFirebase();
        }
        return $this->firestore;
    }
    
    protected function initializeFirebase()
    {
        try {
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

            Log::info('Initializing Firebase services lazily');

            $firebaseFactory = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
            $this->firebaseAuth = $firebaseFactory->createAuth();
            
            // Force REST transport only - disable gRPC completely
            $this->firestore = $firebaseFactory->createFirestore([
                'transport' => 'rest',
                'requestTimeout' => 30.0,
                // Explicitly disable gRPC
                'transportConfig' => [
                    'rest' => [
                        'restClientConfigPath' => null,
                    ]
                ]
            ])->database();
            
            Log::info('Firebase Firestore initialized with REST transport only');
            Log::info('Firebase services initialized successfully');
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    



    /**
    * Show the login form.
    *
    * @return \Illuminate\Http\Response
    */
    public function showLoginForm()
    {
        return response(view('login'));
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
            Log::info('Authentication attempt for: ' . $credentials['email']);
            
            $signInResult = $this->getFirebaseAuth()->signInWithEmailAndPassword(
                $credentials['email'],
                $credentials['password']
            );
    
            $uid = $signInResult->firebaseUserId();
            Log::info('Firebase auth successful, UID: ' . $uid);
    
            try {
                // Simplified approach - no retry logic that might cause issues
                Log::info('Attempting to fetch user data from Firestore');
                
                $userSnapshot = $this->getFirestore()
                    ->collection('Users')
                    ->document($uid)
                    ->snapshot();
                
                if (!$userSnapshot->exists()) {
                    Log::warning('User document not found for UID: ' . $uid);
                    return back()->withErrors(['login_error' => 'Account not found in our database.']);
                }
                
                $userData = $userSnapshot->data();
                Log::info('User data retrieved successfully from Firestore');
                
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
        
                // Redirect based on role
                $role = $userData['role'] ?? '';
                Log::info('Redirecting user with role: ' . $role);
                
                return match ($role) {
                    'admin'      => redirect('/admin/dashboard'),
                    'lecturer'   => app(CourseController::class)->CoursesList(), // Call the method directly
                    'superadmin' => redirect('/superadmin/super-adm-dashboard'),
                    'genadmin'   => redirect('/genadmin/gen-dashboard'),
                    'dean'       => redirect('/deans/dean-dashboard'),
                    default      => throw new \Exception("No valid role assigned to user."),
                };
            } catch (\Exception $firestoreError) {
                Log::error('Firestore error: ' . $firestoreError->getMessage());
                return back()->withErrors(['login_error' => 'Error retrieving user data: ' . $firestoreError->getMessage()]);
            }
        } catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
            Log::warning('Invalid password for user: ' . $credentials['email']);
            return back()->withErrors(['login_error' => 'Invalid email or password.']);
    
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            Log::warning('User not found: ' . $credentials['email']);
            return back()->withErrors(['login_error' => 'Your account does not exist.']);
    
        } catch (\Throwable $e) {
            Log::error('Authentication error: ' . $e->getMessage());
            Log::error('Error class: ' . get_class($e));
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['login_error' => 'Authentication error: ' . $e->getMessage()]);
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
            // Use lazy-loaded Firebase Auth
            $user = $this->getFirebaseAuth()->getUserByEmail($request->email);
            $this->getFirebaseAuth()->sendPasswordResetLink($request->email);

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