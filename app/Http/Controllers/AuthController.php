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
            
            // Skip Firestore SDK entirely - we'll use direct HTTP calls
            $this->firestore = null; // We'll handle this differently
            
            Log::info('Firebase Auth initialized successfully - using direct HTTP for Firestore');
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
                // Use direct HTTP request to Firestore REST API
                Log::info('Attempting to fetch user data via HTTP REST API');
                
                $userData = $this->getUserDataViaHTTP($uid);
                
                if (!$userData) {
                    Log::warning('User document not found for UID: ' . $uid);
                    return back()->withErrors(['login_error' => 'Account not found in our database.']);
                }
                
                Log::info('User data retrieved successfully via HTTP');
                
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
                    'lecturer'   => app(CourseController::class)->CoursesList(), // Call the method directly with updated REST service
                    'superadmin' => redirect('/superadmin/super-adm-dashboard'),
                    'genadmin'   => redirect('/genadmin/gen-dashboard'),
                    'dean'       => redirect('/deans/dean-dashboard'),
                    default      => throw new \Exception("No valid role assigned to user."),
                };
            } catch (\Exception $httpError) {
                Log::error('HTTP Firestore error: ' . $httpError->getMessage());
                return back()->withErrors(['login_error' => 'Error retrieving user data: ' . $httpError->getMessage()]);
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

    /**
     * Get user data via direct HTTP request to Firestore REST API
     */
    private function getUserDataViaHTTP($uid)
    {
        try {
            // Get access token
            $accessToken = $this->getAccessToken();
            
            $projectId = env('FIREBASE_PROJECT_ID');
            $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/Users/{$uid}";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 404) {
                return null; // Document not found
            }
            
            if ($httpCode !== 200) {
                throw new \Exception("HTTP request failed with code: {$httpCode}");
            }
            
            $data = json_decode($response, true);
            
            // Convert Firestore document format to simple array
            return $this->convertFirestoreDocument($data);
            
        } catch (\Exception $e) {
            Log::error('HTTP request to Firestore failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get access token for Google API
     */
    private function getAccessToken()
    {
        if (env('FIREBASE_CREDENTIALS_BASE64')) {
            $serviceAccount = json_decode(base64_decode(env('FIREBASE_CREDENTIALS_BASE64')), true);
        } else {
            $serviceAccount = json_decode(file_get_contents(env('FIREBASE_CREDENTIALS')), true);
        }
        
        $jwt = $this->createJWT($serviceAccount);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        return $data['access_token'];
    }

    /**
     * Create JWT for service account authentication
     */
    private function createJWT($serviceAccount)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
        $now = time();
        $payload = json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = '';
        openssl_sign($base64Header . '.' . $base64Payload, $signature, $serviceAccount['private_key'], 'SHA256');
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }

    /**
     * Convert Firestore document format to simple array
     */
    private function convertFirestoreDocument($firestoreDoc)
    {
        if (!isset($firestoreDoc['fields'])) {
            return null;
        }
        
        $result = [];
        foreach ($firestoreDoc['fields'] as $key => $value) {
            if (isset($value['stringValue'])) {
                $result[$key] = $value['stringValue'];
            } elseif (isset($value['arrayValue']['values'])) {
                $result[$key] = array_map(function($item) {
                    return $item['stringValue'] ?? $item;
                }, $value['arrayValue']['values']);
            } elseif (isset($value['booleanValue'])) {
                $result[$key] = $value['booleanValue'];
            }
            // Add more type conversions as needed
        }
        
        return $result;
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