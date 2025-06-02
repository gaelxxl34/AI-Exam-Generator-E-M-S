<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CourseController;
use Google\Cloud\Firestore\FirestoreClient;

class AuthController extends Controller
{
    protected $firebaseAuth;
    protected $firestore; // Changed to use Google Cloud Firestore directly
    
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
        
        // Use Google Cloud Firestore client directly
        try {
            if (env('FIREBASE_CREDENTIALS_BASE64')) {
                $this->firestore = new FirestoreClient([
                    'keyFile' => json_decode(base64_decode(env('FIREBASE_CREDENTIALS_BASE64')), true),
                    'projectId' => env('FIREBASE_PROJECT_ID')
                ]);
            } else {
                $this->firestore = new FirestoreClient([
                    'keyFilePath' => $firebaseCredentialsPath,
                    'projectId' => env('FIREBASE_PROJECT_ID')
                ]);
            }
            Log::info('Firestore client initialized successfully');
        } catch (\Exception $e) {
            Log::error('Failed to initialize Firestore client: ' . $e->getMessage());
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
            
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword(
                $credentials['email'],
                $credentials['password']
            );
    
            $uid = $signInResult->firebaseUserId();
            Log::info('Firebase auth successful, UID: ' . $uid);
    
            try {
                // Use Google Cloud Firestore client directly
                $userDoc = $this->firestore->collection('Users')->document($uid);
                $userSnapshot = $userDoc->snapshot();
                
                if (!$userSnapshot->exists()) {
                    Log::warning('User document not found for UID: ' . $uid);
                    return back()->withErrors(['login_error' => 'Account not found in our database.']);
                }
                
                $userData = $userSnapshot->data();
                Log::info('User data retrieved successfully');
                
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
                    'lecturer'   => redirect()->action([CourseController::class, 'CoursesList']),
                    'superadmin' => redirect('/superadmin/super-adm-dashboard'),
                    'genadmin'   => redirect('/genadmin/gen-dashboard'),
                    'dean'       => redirect('/deans/dean-dashboard'),
                    default      => throw new \Exception("No valid role assigned to user."),
                };
            } catch (\Exception $firestoreError) {
                Log::error('Firestore error: ' . $firestoreError->getMessage());
                Log::error('Firestore error trace: ' . $firestoreError->getTraceAsString());
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