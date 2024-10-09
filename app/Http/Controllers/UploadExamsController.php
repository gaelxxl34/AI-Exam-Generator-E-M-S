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
    public function uploadExam(Request $request)
    {
        Log::info('uploadExam method called');

        // Validation for all fields, assuming format is always "AB"
        $validatedData = $request->validate([
            'courseUnit' => 'required|string',
            'format' => 'required|string',
            'sectionA' => 'required|array|min:1',
            'sectionA.*' => 'required|string',
            'sectionB' => 'required|array|min:1',
            'sectionB.*' => 'required|string',
            'instructions.1' => 'required|string', // Section A Instructions are mandatory
            'instructions.2' => 'required|string', // Section B Instructions are mandatory
        ]);

        try {
            // Firestore references
            $firestore = app('firebase.firestore')->database();
            $examsRef = $firestore->collection('Exams');

            // Fetch the current user's email
            $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
            Log::info("Current user email: $currentUserEmail");

            // Fetch user details from Firestore
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

            // Check if an exam with the same courseUnit already exists
            $existingExamQuery = $examsRef->where('courseUnit', '==', $validatedData['courseUnit']);
            $existingExamSnapshots = $existingExamQuery->documents();

            if (!$existingExamSnapshots->isEmpty()) {
                // If an exam with the same course unit exists, return back with an error message
                return back()->with('error', 'An exam with this course unit already exists. Please review the existing exam.');
            }

            // Prepare exam data for Firestore
            $examData = [
                'created_at' => new \DateTime(),
                'courseUnit' => $validatedData['courseUnit'],
                'format' => $validatedData['format'],
                'sections' => [],
                'faculty' => $facultyField,
                'sectionA_instructions' => $validatedData['instructions'][1],
                'sectionB_instructions' => $validatedData['instructions'][2],
            ];

            // Process sections and store them in examData
            foreach (['A', 'B'] as $section) {
                $content = $request->input("section$section");
                $examData['sections'][$section] = $content;
            }

            // Save exam data to Firestore
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

            // Initialize arrays to hold instructions
            // $generalInstructions = '';
            $sectionAInstructions = '';
            $sectionBInstructions = '';  // Initialize as empty string

            // Fetch exams based on the course code
            $examsQuery = $firestore->collection('Exams')->where('courseUnit', '==', $selectedCourse);
            $examsSnapshot = $examsQuery->documents();

            $sections = []; // Initialize as an empty array

            foreach ($examsSnapshot as $exam) {
                if ($exam->exists()) {
                    $data = $exam->data();

                    // $generalInstructions = $data['general_instructions'] ?? '';
                    $sectionAInstructions = $data['sectionA_instructions'] ?? '';
                    if (isset($data['sectionB_instructions'])) {
                        $sectionBInstructions = $data['sectionB_instructions'];
                    }
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
            session([
                'sections' => $sections,
                // 'general_instructions' => $generalInstructions,
                'sectionA_instructions' => $sectionAInstructions,
                'sectionB_instructions' => $sectionBInstructions,
                // ... existing session data ...
            ]);


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
        $generalInstructions = $request->input('generalInstructions');
        // $examInstructions = $request->input('examInstructions');


        // Retrieve additional session data
        $faculty = session('faculty');
        $code = session('code');
        $program = session('program');
        $yearSem = session('year_sem');
        $sectionAInstructions = session('sectionA_instructions');
        $sectionBInstructions = session('sectionB_instructions');

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
            'generalInstructions' => $generalInstructions,
            'sectionAInstructions' => $sectionAInstructions,
            'sectionBInstructions' => $sectionBInstructions,
            // 'examInstructions' => $examInstructions
        ]);

        // Set paper size to A4 and orientation to portrait
        $pdf->setPaper('A4', 'portrait');

        // Enable remote images to load
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

        // Stream the PDF to the browser where it can be printed or saved
        return $pdf->stream("Exam_{$courseUnit}.pdf");
    }















}