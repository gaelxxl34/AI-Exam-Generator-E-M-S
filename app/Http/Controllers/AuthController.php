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
    protected $firebaseFirestore;
    
    public function __construct()
    {
        try {
            // Try to get credentials from base64 env variable
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
                // Try to load from file path
                $firebaseCredentialsPath = env('FIREBASE_CREDENTIALS');
                
                // If path not in env, try the default location
                if (empty($firebaseCredentialsPath)) {
                    $firebaseCredentialsPath = base_path('firebase-credentials.json');
                    Log::info('Using default credentials path: ' . $firebaseCredentialsPath);
                }
                
                if (!file_exists($firebaseCredentialsPath)) {
                    throw new \Exception('Firebase credentials file not found at: ' . $firebaseCredentialsPath);
                }
                
                $serviceAccount = $firebaseCredentialsPath;
            }
            
            $firebaseFactory = (new Factory)->withServiceAccount($serviceAccount);
            
            // Only add database URI if it's set
            if (env('FIREBASE_DATABASE_URL')) {
                $firebaseFactory = $firebaseFactory->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
            }
        
            $this->firebaseAuth = $firebaseFactory->createAuth();
            
            // Fix: Get the correct Firestore client instance
            $this->firebaseFirestore = $firebaseFactory->createFirestore()->database();
            
        } catch (\Throwable $e) {
            Log::error('Firebase initialization error: ' . $e->getMessage());
            throw $e; // Re-throw to preserve the original error
        }
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
            Log::info('Authentication attempt for: ' . $credentials['email']);
            
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword(
                $credentials['email'],
                $credentials['password']
            );
            
            Log::info('Firebase sign-in successful');
            $uid = $signInResult->firebaseUserId();
            
            try {
                // Access the Firestore collection properly
                $userSnapshot = $this->firebaseFirestore
                    ->collection('Users')
                    ->document($uid)
                    ->snapshot();
                
                Log::info('User document snapshot retrieved');
                
                if (!$userSnapshot->exists()) {
                    Log::warning('User document not found in Firestore for UID: ' . $uid);
                    return back()->withErrors(['login_error' => 'Account not found in our database.']);
                }
                
                $userData = $userSnapshot->data();
                Log::info('User data retrieved from Firestore: ' . json_encode(array_keys($userData)));
            
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
                    'admin'      => redirect('/admin/dashboard'),
                    'lecturer'   => redirect('/lecturer/lecturer.l-upload-questions'),
                    'superadmin' => redirect('/superadmin/super-adm-dashboard'),
                    'genadmin'   => redirect('/genadmin/gen-dashboard'),
                    'dean'       => redirect('/deans/dean-dashboard'),
                    default      => throw new \Exception("No valid role assigned to user."),
                };
    
            } catch (\Exception $firestoreException) {
                Log::error('Firestore error: ' . $firestoreException->getMessage());
                Log::error('Firestore error trace: ' . $firestoreException->getTraceAsString());
                return back()->withErrors(['login_error' => 'Error accessing user data: ' . $firestoreException->getMessage()]);
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