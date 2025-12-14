<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\AuditService;
use App\Services\DownloadLogService;


class PastExamController extends Controller
{
    // The function below is used to upload past exams into firestore 
    public function store(Request $request)
    {
        $messages = [
            'fileUpload.max' => 'The file should not be greater than 10MB.',
        ];

        $validatedData = $request->validate([
            'courseUnit' => 'required',
            'year' => 'required',
            'program' => 'required|string',
            'examPeriod' => 'required|string',
            'fileUpload' => 'required|file|mimes:pdf|max:10240', // Increased to 10MB since we're using Storage
        ], $messages);

        $file = $request->file('fileUpload');

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
        \Log::info("Current user email: $currentUserEmail");

        // Fetch current user's faculty from Firestore
        $usersRef = $database->collection('Users');
        $query = $usersRef->where('email', '==', $currentUserEmail);
        $currentUserSnapshots = $query->documents();

        if ($currentUserSnapshots->isEmpty()) {
            \Log::error("Firestore user not found with email: $currentUserEmail");
            throw new \Exception('Current user not found in Firestore.');
        }

        $currentUserDocument = iterator_to_array($currentUserSnapshots)[0];
        $currentUserData = $currentUserDocument->data();
        $facultyField = $currentUserData['faculty'] ?? 'default_faculty';
        \Log::info("Faculty fetched: $facultyField");

        // Upload PDF to Firebase Storage instead of storing as Base64
        try {
            $storage = app('firebase.storage');
            $bucket = $storage->getBucket();
            
            // Create a unique filename
            $filename = 'past_exams/' . $validatedData['program'] . '/' . $validatedData['year'] . '/' . 
                        str_replace(' ', '_', $validatedData['courseUnit']) . '_' . 
                        $validatedData['examPeriod'] . '_' . uniqid() . '.pdf';
            
            // Upload file to Firebase Storage
            $bucket->upload(
                file_get_contents($file->getRealPath()),
                ['name' => $filename]
            );
            
            // Generate a signed URL (valid for 10 years)
            $object = $bucket->object($filename);
            $signedUrl = $object->signedUrl(new \DateTime('+10 years'));
            
            \Log::info("File uploaded to Firebase Storage: $filename");
            
        } catch (\Exception $e) {
            \Log::error("Firebase Storage upload failed: " . $e->getMessage());
            return back()->withErrors(['upload_error' => 'Failed to upload file. Please try again.']);
        }

        // Store metadata in Firestore (no Base64, just the storage path)
        $data = [
            'courseUnit' => $validatedData['courseUnit'],
            'year' => $validatedData['year'],
            'program' => $validatedData['program'],
            'examPeriod' => $validatedData['examPeriod'],
            'file_path' => $filename,           // Store the storage path
            'file_url' => $signedUrl,           // Store the signed URL
            'created_at' => now()->toDateTimeString(),
            'faculty' => $facultyField,
            'uploaded_by' => $currentUserEmail,
            'download_count' => 0,
        ];

        $docRef = $database->collection('pastExams')->add($data);

        // Log the upload action
        app(AuditService::class)->logPastExamUploaded(
            $docRef->id(),
            $validatedData['courseUnit'],
            $validatedData['program'],
            $validatedData['year']
        );

        return redirect()->intended('admin.view-past-exams')->with('success', 'Exam uploaded successfully!');
    }


    public function fetchPastExams()
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;

        $usersRef = $database->collection('Users');
        $currentUserQuery = $usersRef->where('email', '==', $currentUserEmail);
        $currentUserSnapshots = $currentUserQuery->documents();

        if ($currentUserSnapshots->isEmpty()) {
            throw new \Exception('Current user not found in Firestore.');
        }

        $currentUserDocument = iterator_to_array($currentUserSnapshots)[0];
        $currentUserData = $currentUserDocument->data();
        $facultyField = $currentUserData['faculty'] ?? 'default_faculty';

        $pastExamsQuery = $database->collection('pastExams')->where('faculty', '==', $facultyField);
        $pastExams = $pastExamsQuery->documents();

        $groupedData = [];
        foreach ($pastExams as $exam) {
            $data = $exam->data();
            $year = $data['year'] ?? 'Unknown';
            $examPeriod = $data['examPeriod'] ?? 'Unknown'; // April, August, December
            $program = $data['program'] ?? 'Unknown';
            $courseUnit = $data['courseUnit'] ?? 'Unknown';

            if (!isset($groupedData[$year])) {
                $groupedData[$year] = [
                    'April' => [],
                    'August' => [],
                    'December' => [],
                ];
            }

            if (!isset($groupedData[$year][$examPeriod])) {
                $groupedData[$year][$examPeriod] = [];
            }

            $groupedData[$year][$examPeriod][] = [
                'id' => $exam->id(),
                'program' => $program,
                'courseUnit' => $courseUnit,
                'file' => $data['file'],
            ];
        }

        // Sort by Year (Descending) for Most Recent First
        krsort($groupedData);

        return view('admin.view-past-exams', ['examsData' => $groupedData]);
    }





    public function delete($id)
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        try {
            // Get the document first to retrieve file path for deletion from Storage
            $document = $database->collection('pastExams')->document($id)->snapshot();
            
            if ($document->exists()) {
                $data = $document->data();
                $courseUnit = $data['courseUnit'] ?? 'Unknown';
                
                // Delete from Firebase Storage if file_path exists
                if (isset($data['file_path'])) {
                    try {
                        $storage = app('firebase.storage');
                        $bucket = $storage->getBucket();
                        $bucket->object($data['file_path'])->delete();
                        \Log::info("Deleted file from Firebase Storage: " . $data['file_path']);
                    } catch (\Exception $e) {
                        \Log::warning("Could not delete file from Storage: " . $e->getMessage());
                    }
                }
                
                // Log the deletion
                app(AuditService::class)->logPastExamDeleted($id, $courseUnit);
            }
            
            $database->collection('pastExams')->document($id)->delete();
            return back()->with('success', 'Exam deleted successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors('Error deleting the exam: ' . $e->getMessage());
        }
    }

    public function fetchMITExams()
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Query to fetch past exams for 'MIT' - only load metadata
        $pastExamsQuery = $database->collection('pastExams')
            ->where('program', '==', 'MIT')
            ->select(['courseUnit', 'year']);
        $pastExams = $pastExamsQuery->documents();

        $groupedData = [];
        foreach ($pastExams as $exam) {
            if ($exam->exists()) {
                $data = $exam->data();
                $courseUnit = $data['courseUnit'];
                if (!isset($groupedData[$courseUnit])) {
                    $groupedData[$courseUnit] = [];
                }
                $groupedData[$courseUnit][] = [
                    'id' => $exam->id(), // Store document ID for lazy loading
                    'year' => $data['year'],
                ];
            }
        }

        // Pass the organized data to the view
        return view('fst.fstmaster', ['examsData' => $groupedData]);
    }

    public function fetchBachelorExams()
    {
        Log::info('Starting to fetch bachelor exams');

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        $programs = ['BIT', 'BSCS', 'BSSE', 'BSEM'];
        $groupedData = [];

        foreach ($programs as $program) {
            Log::info("Querying past exams for program: {$program}");
            $query = $database->collection('pastExams')
                ->where('program', '==', $program)
                ->select(['courseUnit', 'year']); // Only fetch minimal fields
            $exams = $query->documents();

            foreach ($exams as $exam) {
                if ($exam->exists()) {
                    $data = $exam->data();
                    $courseUnit = $data['courseUnit'];

                    if (!isset($groupedData[$program])) {
                        $groupedData[$program] = [];
                    }

                    if (!isset($groupedData[$program][$courseUnit])) {
                        $groupedData[$program][$courseUnit] = [];
                    }

                    $groupedData[$program][$courseUnit][] = [
                        'id' => $exam->id(), // Store document ID for lazy loading
                        'year' => $data['year'],
                    ];
                }
            }
        }

        Log::info('Completed fetching bachelor exams', ['groupedDataCount' => count($groupedData)]);

        return view('fst/fstbachelor', ['examsData' => $groupedData]);
    }

    public function fetchDiplomaDCSExams()
    {
        Log::info('Starting to fetch DCS diploma exams');

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Define the DCS program identifier
        $program = 'DCS';
        $query = $database->collection('pastExams')
            ->where('program', '==', $program)
            ->select(['courseUnit', 'year']);
        $exams = $query->documents();

        $groupedData = [];
        foreach ($exams as $exam) {
            if ($exam->exists()) {
                $data = $exam->data();
                $courseUnit = $data['courseUnit'];

                if (!isset($groupedData[$courseUnit])) {
                    $groupedData[$courseUnit] = [];
                }
                $groupedData[$courseUnit][] = [
                    'id' => $exam->id(),
                    'year' => $data['year'],
                ];
            }
        }

        Log::info('Completed fetching DCS diploma exams', ['groupedDataCount' => count($groupedData)]);

        return view('fst/fstdiploma', ['examsData' => $groupedData]);
    }


    public function fetchFBMBachelorExams()
    {
        Log::info('Starting to fetch FBM bachelor exams');

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Define the bachelor programs for FBM to search for
        $programs = ['BBA', 'BHRM', 'BPA', 'BPALM', 'BTHM'];
        $groupedData = [];

        foreach ($programs as $program) {
            Log::info("Querying past exams for FBM program: {$program}");
            $query = $database->collection('pastExams')
                ->where('program', '==', $program)
                ->select(['courseUnit', 'year']);
            $exams = $query->documents();

            foreach ($exams as $exam) {
                if ($exam->exists()) {
                    $data = $exam->data();
                    $courseUnit = $data['courseUnit'];

                    if (!isset($groupedData[$program])) {
                        $groupedData[$program] = [];
                    }

                    if (!isset($groupedData[$program][$courseUnit])) {
                        $groupedData[$program][$courseUnit] = [];
                    }

                    $groupedData[$program][$courseUnit][] = [
                        'id' => $exam->id(),
                        'year' => $data['year'],
                    ];
                }
            }
        }

        Log::info('Completed fetching FBM bachelor exams', ['groupedDataCount' => count($groupedData)]);

        return view('fbm.fbmbachelor', ['examsData' => $groupedData]);
    }


    public function fetchDiplomaBusinessAndPublicExams()
    {
        Log::info('Starting to fetch diploma exams for DBA and DPA programs');

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Define the diploma programs to search for
        $programs = ['DBA', 'DPA'];
        $groupedData = [];

        foreach ($programs as $program) {
            Log::info("Querying past exams for program: {$program}");
            $query = $database->collection('pastExams')
                ->where('program', '==', $program)
                ->select(['courseUnit', 'year']);
            $exams = $query->documents();

            foreach ($exams as $exam) {
                if ($exam->exists()) {
                    $data = $exam->data();
                    $courseUnit = $data['courseUnit'];

                    if (!isset($groupedData[$program])) {
                        $groupedData[$program] = [];
                    }

                    if (!isset($groupedData[$program][$courseUnit])) {
                        $groupedData[$program][$courseUnit] = [];
                    }

                    $groupedData[$program][$courseUnit][] = [
                        'id' => $exam->id(),
                        'year' => $data['year'],
                    ];
                }
            }
        }

        Log::info('Completed fetching diploma exams for DBA and DPA', ['groupedDataCount' => count($groupedData)]);

        return view('fbm.fbmdiploma', ['examsData' => $groupedData]);
    }

    public function fetchFOEBachelorExams()
    {
        Log::info('Starting to fetch FOE bachelor exams');

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Define the bachelor programs for FOE to search for
        $programs = ['BSPE', 'BARC', 'BSCE', 'BSEE'];
        $groupedData = [];

        foreach ($programs as $program) {
            Log::info("Querying past exams for FOE program: {$program}");
            $query = $database->collection('pastExams')
                ->where('program', '==', $program)
                ->select(['courseUnit', 'year']);
            $exams = $query->documents();

            foreach ($exams as $exam) {
                if ($exam->exists()) {
                    $data = $exam->data();
                    $courseUnit = $data['courseUnit'];

                    if (!isset($groupedData[$program])) {
                        $groupedData[$program] = [];
                    }

                    if (!isset($groupedData[$program][$courseUnit])) {
                        $groupedData[$program][$courseUnit] = [];
                    }

                    $groupedData[$program][$courseUnit][] = [
                        'id' => $exam->id(),
                        'year' => $data['year'],
                    ];
                }
            }
        }

        Log::info('Completed fetching FOE bachelor exams', ['groupedDataCount' => count($groupedData)]);

        return view('foe.foebachelor', ['examsData' => $groupedData]);
    }


    public function fetchFOEDiplomaExams()
    {
        Log::info('Starting to fetch FOE diploma exams');

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Define the diploma programs for FOE to search for
        $programs = ['DCE', 'DEE', 'DARC'];
        $groupedData = [];

        foreach ($programs as $program) {
            Log::info("Querying past exams for FOE diploma program: {$program}");
            $query = $database->collection('pastExams')
                ->where('program', '==', $program)
                ->select(['courseUnit', 'year']);
            $exams = $query->documents();

            foreach ($exams as $exam) {
                if ($exam->exists()) {
                    $data = $exam->data();
                    $courseUnit = $data['courseUnit'];

                    if (!isset($groupedData[$program])) {
                        $groupedData[$program] = [];
                    }

                    if (!isset($groupedData[$program][$courseUnit])) {
                        $groupedData[$program][$courseUnit] = [];
                    }

                    $groupedData[$program][$courseUnit][] = [
                        'id' => $exam->id(),
                        'year' => $data['year'],
                    ];
                }
            }
        }

        Log::info('Completed fetching FOE diploma exams', ['groupedDataCount' => count($groupedData)]);

        return view('foe.foediploma', ['examsData' => $groupedData]);
    }


    public function fetchLawBachelorExams()
    {
        Log::info('Starting to fetch LLB exams for the Faculty of Law');

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // The LLB program identifier
        $program = 'LLB';
        $query = $database->collection('pastExams')
            ->where('program', '==', $program)
            ->select(['courseUnit', 'year']);
        $exams = $query->documents();

        $groupedData = [];
        foreach ($exams as $exam) {
            if ($exam->exists()) {
                $data = $exam->data();
                $courseUnit = $data['courseUnit'];

                if (!isset($groupedData[$courseUnit])) {
                    $groupedData[$courseUnit] = [];
                }

                $groupedData[$courseUnit][] = [
                    'id' => $exam->id(),
                    'year' => $data['year'],
                ];
            }
        }

        Log::info('Completed fetching LLB exams', ['groupedDataCount' => count($groupedData)]);

        return view('fol.folbachelor', ['examsData' => $groupedData]);
    }


    public function fetchHECExams()
    {
        Log::info('Starting to fetch HEC exams');

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // The HEC program identifier
        $program = 'HEC';
        $query = $database->collection('pastExams')
            ->where('program', '==', $program)
            ->select(['courseUnit', 'year']);
        $exams = $query->documents();

        $groupedData = [];
        foreach ($exams as $exam) {
            if ($exam->exists()) {
                $data = $exam->data();
                $courseUnit = $data['courseUnit'];

                if (!isset($groupedData[$courseUnit])) {
                    $groupedData[$courseUnit] = [];
                }

                $groupedData[$courseUnit][] = [
                    'id' => $exam->id(),
                    'year' => $data['year'],
                ];
            }
        }

        Log::info('Completed fetching HEC exams', ['groupedDataCount' => count($groupedData)]);

        return view('fbm.hec', ['examsData' => $groupedData]);
    }

    // New method to fetch a single PDF file on demand
    public function fetchPdfFile($id)
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        try {
            $document = $database->collection('pastExams')->document($id)->snapshot();
            
            if ($document->exists()) {
                $data = $document->data();
                $courseUnit = $data['courseUnit'] ?? 'exam';
                $program = $data['program'] ?? '';
                $year = $data['year'] ?? '';
                
                // Log the download
                app(DownloadLogService::class)->logPastExamDownload($id, $courseUnit, $program, $year);
                
                // Check if we have a file_url (new Storage-based files)
                if (isset($data['file_url'])) {
                    // Redirect to the signed URL
                    return redirect($data['file_url']);
                }
                
                // Fallback for old Base64 files (backward compatibility)
                if (isset($data['file'])) {
                    $pdfContent = base64_decode($data['file']);
                    
                    return response($pdfContent)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'inline; filename="' . $courseUnit . '.pdf"');
                }
            }
            
            return response()->json(['error' => 'PDF not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading PDF'], 500);
        }
    }

    /**
     * Unified method to fetch exams dynamically based on faculty and degree
     * 
     * @param string $faculty - Faculty code (fst, fbm, foe, fol)
     * @param string $degree - Degree level (master, bachelor, diploma, hec)
     * @return \Illuminate\View\View
     */
    public function fetchProgramExams($faculty, $degree)
    {
        Log::info("Starting to fetch {$degree} exams for {$faculty}");

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Configuration mapping for each faculty and degree combination
        $config = [
            'fst' => [
                'name' => 'Faculty of Science and Technology',
                'color' => 'blue',
                'icon' => 'laptop-code',
                'master' => [
                    'title' => 'FST Master\'s Degree',
                    'programs' => ['MIT']
                ],
                'bachelor' => [
                    'title' => 'FST Bachelor\'s Degree',
                    'programs' => ['BIT', 'BSCS', 'BSSE', 'BSEM']
                ],
                'diploma' => [
                    'title' => 'FST Diploma',
                    'programs' => ['DCS']
                ]
            ],
            'fbm' => [
                'name' => 'Faculty of Business & Management',
                'color' => 'amber',
                'icon' => 'briefcase',
                'master' => [
                    'title' => 'FBM Master\'s Degree',
                    'programs' => ['MBA']
                ],
                'bachelor' => [
                    'title' => 'FBM Bachelor\'s Degree',
                    'programs' => ['BBA', 'BHRM', 'BPA', 'BPALM', 'BTHM']
                ],
                'diploma' => [
                    'title' => 'FBM Diploma',
                    'programs' => ['DBA', 'DPA']
                ],
                'hec' => [
                    'title' => 'Higher Education Certificate',
                    'programs' => ['HEC']
                ]
            ],
            'foe' => [
                'name' => 'Faculty of Engineering',
                'color' => 'orange',
                'icon' => 'cogs',
                'bachelor' => [
                    'title' => 'FOE Bachelor\'s Degree',
                    'programs' => ['BSPE', 'BARC', 'BSCE', 'BSEE']
                ],
                'diploma' => [
                    'title' => 'FOE Diploma',
                    'programs' => ['DCE', 'DEE', 'DARC']
                ]
            ],
            'fol' => [
                'name' => 'Faculty of Law',
                'color' => 'red',
                'icon' => 'balance-scale',
                'bachelor' => [
                    'title' => 'Faculty of Law - LLB',
                    'programs' => ['LLB']
                ]
            ]
        ];

        // Validate faculty and degree
        if (!isset($config[$faculty]) || !isset($config[$faculty][$degree])) {
            abort(404, 'Program not found');
        }

        $facultyConfig = $config[$faculty];
        $degreeConfig = $facultyConfig[$degree];
        $programs = $degreeConfig['programs'];

        $groupedData = [];

        // Fetch exams for each program
        foreach ($programs as $program) {
            Log::info("Querying past exams for program: {$program}");
            $query = $database->collection('pastExams')
                ->where('program', '==', $program)
                ->select(['courseUnit', 'year']);
            $exams = $query->documents();

            foreach ($exams as $exam) {
                if ($exam->exists()) {
                    $data = $exam->data();
                    $courseUnit = $data['courseUnit'];

                    if (!isset($groupedData[$program])) {
                        $groupedData[$program] = [];
                    }

                    if (!isset($groupedData[$program][$courseUnit])) {
                        $groupedData[$program][$courseUnit] = [];
                    }

                    $groupedData[$program][$courseUnit][] = [
                        'id' => $exam->id(),
                        'year' => $data['year'],
                    ];
                }
            }
        }

        Log::info("Completed fetching {$degree} exams for {$faculty}", ['programCount' => count($groupedData)]);

        // Return unified view with dynamic data
        return view('exams.programs', [
            'examsData' => $groupedData,
            'pageTitle' => $degreeConfig['title'],
            'facultyName' => $facultyConfig['name'],
            'facultyColor' => $facultyConfig['color'],
            'facultyIcon' => $facultyConfig['icon']
        ]);
    }


}
