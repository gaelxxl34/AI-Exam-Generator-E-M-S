<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class PastExamController extends Controller
{
    // The function below is used to upload past exams into firestore 
    public function store(Request $request)
    {

        $messages = [
            'fileUpload.max' => 'The file should not be greater than 2MB.',
        ];
        
        $validatedData = $request->validate([
            'courseUnit' => 'required',
            'year' => 'required',
            'program' => 'required|string',  // Validate the new field
            'fileUpload' => 'required|file|mimes:pdf|max:2048',
            'created_at' => new \DateTime(),
        ], $messages);

        $file = $request->file('fileUpload');
        $base64File = base64_encode(file_get_contents($file));

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
        \Log::info("Current user email: $currentUserEmail");

        // Fetch the current user's data from Firestore
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

        $data = [
            'courseUnit' => $validatedData['courseUnit'],
            'year' => $validatedData['year'],
            'program' => $validatedData['program'],  // Include the program field
            'file' => $base64File,
            'created_at' => new \DateTime(),
            'faculty' => $facultyField  // Include the faculty field
        ];

        $database->collection('pastExams')->add($data);

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
            $program = $data['program'] ?? 'Unknown'; // Ensure there is a default for program
            if (!isset($groupedData[$program])) {
                $groupedData[$program] = [];
            }
            if (!isset($groupedData[$program][$data['courseUnit']])) {
                $groupedData[$program][$data['courseUnit']] = [];
            }
            $groupedData[$program][$data['courseUnit']][] = [
                'id' => $exam->id(),  // Include the document ID
                'year' => $data['year'],
                'file' => $data['file'], // Base64 encoded PDF
            ];
        }

        return view('admin.view-past-exams', ['examsData' => $groupedData]);
    }

    public function delete($id)
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        try {
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

        // Query to fetch past exams for 'MIT'
        $pastExamsQuery = $database->collection('pastExams')->where('program', '==', 'MIT');
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
                    'year' => $data['year'],
                    'file' => $data['file'], // Base64 encoded PDF
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
            $query = $database->collection('pastExams')->where('program', '==', $program);
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
                        'year' => $data['year'],
                        'file' => $data['file'], // Base64 encoded PDF
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
        $query = $database->collection('pastExams')->where('program', '==', $program);
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
                    'year' => $data['year'],
                    'file' => $data['file'], // Base64 encoded PDF
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
            $query = $database->collection('pastExams')->where('program', '==', $program);
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
                        'year' => $data['year'],
                        'file' => $data['file'], // Base64 encoded PDF
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
            $query = $database->collection('pastExams')->where('program', '==', $program);
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
                        'year' => $data['year'],
                        'file' => $data['file'], // Base64 encoded PDF
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
            $query = $database->collection('pastExams')->where('program', '==', $program);
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
                        'year' => $data['year'],
                        'file' => $data['file'], // Base64 encoded PDF
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
            $query = $database->collection('pastExams')->where('program', '==', $program);
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
                        'year' => $data['year'],
                        'file' => $data['file'], // Base64 encoded PDF
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
        $query = $database->collection('pastExams')->where('program', '==', $program);
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
                    'year' => $data['year'],
                    'file' => $data['file'], // Base64 encoded PDF
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
        $query = $database->collection('pastExams')->where('program', '==', $program);
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
                    'year' => $data['year'],
                    'file' => $data['file'], // Base64 encoded PDF
                ];
            }
        }

        Log::info('Completed fetching HEC exams', ['groupedDataCount' => count($groupedData)]);

        return view('fbm.hec', ['examsData' => $groupedData]);
    }


}
