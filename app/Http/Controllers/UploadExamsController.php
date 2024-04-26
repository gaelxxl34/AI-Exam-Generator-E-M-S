<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Storage;
// use Barryvdh\DomPDF\Facade\Pdf;
class UploadExamsController extends Controller
{
    public function uploadExam(Request $request, Storage $storage)
    {
        Log::info('uploadExam method called');

        // Enhanced validation to include practical exams and instruction fields
        $messages = [
            'fileUpload.max' => 'The file should not be greater than 2MB.',
        ];
        $validatedData = $request->validate([

            'courseUnit' => 'required|string',
            'format' => 'required|string',
            'sectionA' => 'required|array|min:1',
            'sectionB' => 'sometimes|required|array|min:1',
            'sectionC' => 'sometimes|required|array|min:1',
            'sectionA.*' => 'required|string',
            'sectionB.*' => 'sometimes|required|string',
            'sectionC.*' => 'sometimes|required|string',
            'fileUpload' => 'required|file|mimes:pdf|max:2048',
            'instructions.0' => 'required|string', // General Instructions
            'instructions.1' => 'required|string', // Section A Instructions
            'instructions.2' => 'sometimes|string', // Section B Instructions
        ], $messages);

        try {
            $file = $request->file('fileUpload');
            $base64File = base64_encode(file_get_contents($file));

            Log::info("This is : $base64File");

            $firestore = app('firebase.firestore')->database();
            $examsRef = $firestore->collection('Exams');

            $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
            Log::info("Current user email: $currentUserEmail");

            // Fetch the current user's faculty from Firestore
            $usersRef = $firestore->collection('Users');
            $query = $usersRef->where('email', '==', $currentUserEmail);
            $currentUserSnapshots = $query->documents();

            if ($currentUserSnapshots->isEmpty()) {
                Log::error("Firestore user not found with email: $currentUserEmail");
                throw new \Exception('Current user not found in Firestore.');
            }

            $currentUserDocument = iterator_to_array($currentUserSnapshots)[0];
            $currentUserData = $currentUserDocument->data();
            $facultyField = $currentUserData['faculty'] ?? 'default_faculty';
            Log::info("Faculty fetched: $facultyField");

            $examData = [
                'created_at' => new \DateTime(),
                'courseUnit' => $validatedData['courseUnit'],
                'format' => $validatedData['format'],
                'sections' => [],
                'marking_guide' => $base64File,
                'faculty' => $facultyField,  // Include the faculty field
                'general_instructions' => $validatedData['instructions'][0],
                'sectionA_instructions' => $validatedData['instructions'][1],
            ];

            if (isset($validatedData['instructions'][2])) {
                $examData['sectionB_instructions'] = $validatedData['instructions'][2];
            }

            // Process sections based on format
            foreach (['A', 'B', 'C'] as $section) {
                if ($request->has("section$section")) {
                    $content = $request->input("section$section");
                    $examData['sections'][$section] = $content;
                }
            }

            $examsRef->add($examData);

            return redirect()->route('lecturer.l-dashboard')->with('success', 'Exam uploaded successfully.');
        } catch (\Throwable $e) {
            Log::error("Error uploading exam: " . $e->getMessage());
            return back()->withErrors(['upload_error' => 'Error uploading exam.'])->with('message', 'Error uploading exam: ' . $e->getMessage());
        }
    }






    public function getRandomQuestions(Request $request)
    {
        \Log::info('getRandomQuestions method started');
        $selectedCourse = $request->input('course');

        try {
            $firestore = app('firebase.firestore')->database();

            // Fetch course information directly from the Courses collection
            $coursesRef = $firestore->collection('Courses');
            $query = $coursesRef->where('name', '==', $selectedCourse);
            $courseSnapshots = $query->documents();

            if ($courseSnapshots->isEmpty()) {
                \Log::error("No course found with the name: $selectedCourse");
                throw new \Exception("No course found with the specified name.");
            }

            $courseData = null;
            foreach ($courseSnapshots as $snapshot) {
                if ($snapshot->exists()) {
                    $courseData = $snapshot->data();
                    break; // Assuming only one course matches the name, so we take the first match.
                }
            }

            if ($courseData === null) {
                \Log::error("No existing course found with the name: $selectedCourse");
                throw new \Exception("No existing course found.");
            }

            $code = $courseData['code'] ?? 'default_code';
            $program = $courseData['program'] ?? 'default_program';
            $year_sem = $courseData['year_sem'] ?? 'default_year_sem';
            \Log::info("Course details fetched: Code: $code, Program: $program, Year/Sem: $year_sem");

            // Fetch exams based on the course code
            $examsQuery = $firestore->collection('Exams')->where('courseUnit', '==', $selectedCourse);
            $examsSnapshot = $examsQuery->documents();

            $sections = []; // Initialize as an empty array

            foreach ($examsSnapshot as $exam) {
                if ($exam->exists()) {
                    $data = $exam->data();

                    foreach ($data['sections'] as $section => $contents) {
                        if (!isset($sections[$section])) {
                            $sections[$section] = [];
                        }

                        foreach ($contents as $index => $content) {
                            $doc = new \DOMDocument();
                            @$doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                            $sections[$section][] = $doc->saveHTML();
                        }
                    }
                }
            }

            // Shuffle and slice questions for each section
            foreach ($sections as $section => $questions) {
                shuffle($questions);
                $count = ($section == 'A') ? 4 : 6; // Adjust based on your needs
                $sections[$section] = array_slice($questions, 0, $count);
            }

            // Store the sections data in the session
            session(['sections' => $sections]);

            session([
                'faculty' => $courseData['faculty'] ?? 'default_faculty',
                'code' => $code,
                'program' => $program,
                'year_sem' => $year_sem,
            ]);

            return view('genadmin.view-generated-exam', [
                'courseUnit' => $selectedCourse,
                'sections' => $sections
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getRandomQuestions: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }





    public function generatePdf(Request $request)
    {
        $courseUnit = $request->input('courseUnit');
        $sections = session('sections'); // Retrieve the sections data from the session
        $facultyOf = $request->input('facultyOf');
        $examPeriod = $request->input('examPeriod');
        $date = $request->input('date');
        $time = $request->input('time');
        $examInstructions = $request->input('examInstructions');


        // Retrieve additional session data
        $faculty = session('faculty');
        $code = session('code');
        $program = session('program');
        $yearSem = session('year_sem');

        \Log::info('PDF generation started with courseUnit: ' . $courseUnit);

        // Generate the PDF from the 'admin.exam-template' view and pass in the necessary data
        $pdf = PDF::loadView('admin.exam-template', [
            'sections' => $sections,
            'courseUnit' => $courseUnit,
            // Pass additional data to the view
            'faculty' => $faculty,
            'code' => $code,
            'program' => $program,
            'yearSem' => $yearSem,
            'facultyOf' => $facultyOf,
            'examPeriod' => $examPeriod,
            'date' => $date,
            'time' => $time,
            'examInstructions' => $examInstructions
        ]);

        // Set paper size to A4 and orientation to portrait
        $pdf->setPaper('A4', 'portrait');

        // Enable remote images to load
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

        // Stream the PDF to the browser where it can be printed or saved
        return $pdf->stream("Exam_{$courseUnit}.pdf");
    }















}