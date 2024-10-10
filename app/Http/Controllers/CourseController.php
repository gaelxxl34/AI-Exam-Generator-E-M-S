<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;
class CourseController extends Controller

{
    public function CoursesList()
    {
        \Log::info('CoursesList method called');

        try {
            $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
            \Log::info('Current user email: ' . $currentUserEmail);

            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $usersRef = $database->collection('Users');

            $query = $usersRef->where('email', '==', $currentUserEmail);
            $currentUserSnapshots = $query->documents();

            if ($currentUserSnapshots->isEmpty()) {
                \Log::error("Firestore user not found with email: $currentUserEmail");
                throw new \Exception('Current user not found in Firestore.');
            }

            // Convert the snapshot to an array and get the first element
            $currentUserDataArray = iterator_to_array($currentUserSnapshots);
            $currentUserData = $currentUserDataArray[0]->data();

            $courses = $currentUserData['courses'] ?? [];
            if (empty($courses)) {
                \Log::info('No courses found for the current user: ' . $currentUserEmail);
                throw new \Exception('No courses found for the current user.');
            }

            \Log::info('Courses fetched for the current user: ' . json_encode($courses));

            // Pass the courses to the view
            return view('lecturer.l-upload-questions', ['courses' => $courses]);
        } catch (\Exception $e) {
            \Log::error('Error in lecturerList: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }

    public function fetchCourses()
    {
        \Log::info('Fetching courses for dashboard');

        try {
            $firestore = app('firebase.firestore')->database();
            $lecturerEmail = session()->get('user_email');
            \Log::info('Current user email: ' . $lecturerEmail);

            // Query the Users collection to find the lecturer's document by email
            $usersRef = $firestore->collection('Users');
            $query = $usersRef->where('email', '=', $lecturerEmail);
            $snapshot = $query->documents();

            $lecturerCourses = [];

            foreach ($snapshot as $doc) {
                if ($doc->exists() && $doc['email'] === $lecturerEmail) {
                    $lecturerCourses = $doc['courses'];
                    break; // Assuming one match, we can break the loop once found
                }
            }

            if (empty($lecturerCourses)) {
                \Log::error("Lecturer not found or no courses assigned");
                return back()->withErrors(['fetch_error' => 'Lecturer not found or no courses assigned.']);
            }

            $examsRef = $firestore->collection('Exams');
            $courses = [];

            foreach ($lecturerCourses as $courseUnit) {
                $courseExams = $examsRef->where('courseUnit', '=', $courseUnit)->documents();
                foreach ($courseExams as $document) {
                    if ($document->exists()) {
                        $data = $document->data();
                        if (!isset($courses[$courseUnit])) {
                            $courses[$courseUnit] = [];
                        }
                        $courses[$courseUnit][] = $data;
                    }
                }
            }

            return view('lecturer.l-dashboard', ['courses' => $courses]);

        } catch (\Throwable $e) {
            \Log::error("Error fetching courses: " . $e->getMessage());
            return back()->withErrors(['fetch_error' => 'Error fetching courses.'])->with('message', 'Error fetching courses: ' . $e->getMessage());
        }
    }




    public function courseDetails($courseUnit) // Display exam content based on course unit for lecturers to view questions
    {
        \Log::info("Fetching details for course unit: $courseUnit");

        try {
            $firestore = app('firebase.firestore')->database();
            $examsRef = $firestore->collection('Exams');
            $query = $examsRef->where('courseUnit', '==', $courseUnit);
            $examsSnapshot = $query->documents();

            $exams = [];

            foreach ($examsSnapshot as $document) {
                if ($document->exists()) {
                    $data = $document->data();

                    // Process each section's content by decoding Base64
                    foreach ($data['sections'] as $section => $contents) {
                        foreach ($contents as $index => $content) {
                            // Step 1: Base64 decode the content
                            $decodedContent = base64_decode($content);

                            // Step 2: Store the decoded content back
                            $data['sections'][$section][$index] = $decodedContent;
                        }
                    }

                    // Add the exam data to the exams array
                    $exams[] = $data;
                }
            }

            return view('lecturer.l-course-exams', ['exams' => $exams, 'courseUnit' => $courseUnit]);
        } catch (\Throwable $e) {
            \Log::error("Error fetching course details for unit: $courseUnit - " . $e->getMessage());
            return back()->withErrors(['fetch_error' => 'Error fetching course details.'])->with('message', 'Error fetching course details: ' . $e->getMessage());
        }
    }





    // --START OF the list of all the courses with their code and the program where they are given 

    public function uploadCourses(Request $request)
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Retrieve current user email from session or authentication
        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
        $usersRef = $database->collection('Users');
        $query = $usersRef->where('email', '==', $currentUserEmail);
        $currentUserSnapshots = $query->documents();

        if ($currentUserSnapshots->isEmpty()) {
            \Log::error("Firestore user not found with email: $currentUserEmail");
            throw new \Exception('Current user not found in Firestore.');
        }

        $currentUserDocument = iterator_to_array($currentUserSnapshots)[0];
        $currentUserData = $currentUserDocument->data();
        $faculty = $currentUserData['faculty'] ?? 'default_faculty';

        // Prepare course data from the form
        $courseData = [
            'name' => $request->input('courseUnit'),
            'code' => $request->input('courseCode'),
            'program' => $request->input('program'),
            'year_sem' => 'Year ' . $request->input('year') . '/Semester ' . $request->input('semester'),
            'faculty' => $faculty
        ];

        // Upload to Firestore
        $coursesRef = $database->collection('Courses');
        $coursesRef->add($courseData);

        // Optionally, fetch all courses for the view
        $allCourses = $coursesRef->documents();

        // Prepare data for the view
        $coursesList = [];
        foreach ($allCourses as $doc) {
            $coursesList[] = $doc->data();
        }

        // Redirect to the courses list view with all courses data
        return redirect()->intended('admin/courses-list')->with('success', 'Exam uploaded successfully!');
        
    }


    public function showCourses()
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Retrieve current user email from session or authentication
        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
        $usersRef = $database->collection('Users');
        $query = $usersRef->where('email', '==', $currentUserEmail);
        $currentUserSnapshots = $query->documents();

        if ($currentUserSnapshots->isEmpty()) {
            \Log::error("Firestore user not found with email: $currentUserEmail");
            throw new \Exception('Current user not found in Firestore.');
        }

        $currentUserDocument = iterator_to_array($currentUserSnapshots)[0];
        $currentUserData = $currentUserDocument->data();
        $currentFaculty = $currentUserData['faculty'] ?? 'default_faculty';

        // Fetch courses that match the current user's faculty
        $coursesRef = $database->collection('Courses');
        $courseQuery = $coursesRef->where('faculty', '==', $currentFaculty);
        $matchingCoursesSnapshots = $courseQuery->documents();

        $organizedCourses = [];
        foreach ($matchingCoursesSnapshots as $document) {
            if ($document->exists()) {
                $data = $document->data();
                $data['id'] = $document->id();
                $organizedCourses[$data['faculty']][$data['program']][] = $data;
            }
        }

        // Pass organized data to the view
        if (!empty($organizedCourses)) {
            return view('admin.courses-list', ['courses' => $organizedCourses]);
        } else {
            return view('admin.courses-list', ['courses' => []]); // Ensure an array is always passed, even empty.
        }
    }


    public function editCourse($id)
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();
        $courseRef = $database->collection('Courses')->document($id);
        $snapshot = $courseRef->snapshot();

        if ($snapshot->exists()) {
            $course = $snapshot->data();
            $course['id'] = $snapshot->id();
            return view('admin.edit-courses', ['course' => $course]);
        } else {
            return redirect()->route('admin.edit-courses')->with('error', 'Course not found.');
        }
    }

    public function updateCourse(Request $request, $id)
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();
        $courseRef = $database->collection('Courses')->document($id);

        try {
            $courseRef->update([
                ['path' => 'name', 'value' => $request->courseUnit],
                ['path' => 'code', 'value' => $request->courseCode],
                ['path' => 'year_sem', 'value' => $request->year_sem],
            ]);

            return back()->with('success', 'Course updated successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error updating course: ' . $e->getMessage());
        }
    }


    // -- END of the list 




    // TRIAL 
    public function fetchCoursesForFaculty()
    {
        \Log::info('Entering fetchCoursesForFaculty method');

        try {
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
            \Log::info("Faculty fetched on fetchCoursesForFaculty: $facultyField");

            // Fetch courses from 'Courses' collection that match the current user's faculty
            $coursesRef = $database->collection('Courses');
            $coursesQuery = $coursesRef->where('faculty', '==', $facultyField);
            $courseDocuments = $coursesQuery->documents();

            $courseDetails = [];
            foreach ($courseDocuments as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    $courseDetails[] = [
                        'name' => $data['name'], // Assuming 'name' holds the course name
                        'id' => $document->id() // Include document ID in case it's needed
                    ];
                    \Log::info("Course fetched: " . $data['name']);
                }
            }

            \Log::info('Courses fetched successfully', ['courseDetails' => $courseDetails]);

            return $courseDetails;

        } catch (\Throwable $e) {
            \Log::error("Error in fetchCoursesForFaculty: " . $e->getMessage());
            return [];
        }
    }


    public function AllCourses()
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        try {
            // Fetch the current user's email and faculty
            $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
            $usersRef = $database->collection('Users');
            $userQuery = $usersRef->where('email', '==', $currentUserEmail);
            $currentUserSnapshots = $userQuery->documents();

            if ($currentUserSnapshots->isEmpty()) {
                \Log::error("Firestore user not found with email: $currentUserEmail");
                throw new \Exception('Current user not found in Firestore.');
            }

            $currentUserDocument = iterator_to_array($currentUserSnapshots)[0];
            $currentUserData = $currentUserDocument->data();
            $userFaculty = $currentUserData['faculty'] ?? 'default_faculty';
            \Log::info("Current user faculty: $userFaculty");

            $containsComma = strpos($userFaculty, ',') !== false;
            \Log::info("Faculty field contains comma: " . ($containsComma ? 'Yes' : 'No'));

            $courses = [];
            $coursesRef = $database->collection('Courses');

            // If faculty contains a comma, fetch all courses; otherwise, fetch by specific faculty
            if ($containsComma) {
                $coursesQuery = $coursesRef; // Fetch all courses
                \Log::info("Fetching all courses due to multiple faculty entries");
            } else {
                $coursesQuery = $coursesRef->where('faculty', '==', $userFaculty);
                \Log::info("Fetching courses for specific faculty: $userFaculty");
            }

            $coursesSnapshots = $coursesQuery->documents();

            foreach ($coursesSnapshots as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    $courses[] = [
                        'id' => $document->id(),
                        'name' => $data['name'] ?? 'Unknown Course'
                    ];
                }
            }

            \Log::info("Number of courses fetched: " . count($courses));

            // Pass the courses to the view
            return view('genadmin.ai-exam-generator', ['courses' => $courses]);
        } catch (\Exception $e) {
            \Log::error("Error fetching courses: " . $e->getMessage());
            return view('genadmin.ai-exam-generator', ['courses' => [], 'error' => 'Failed to fetch courses.']);
        }
    }






    public function deleteQuestion($courseUnit, $sectionName, $questionIndex)
    {
        Log::info("Entering deleteQuestion with parameters: Course Unit - {$courseUnit}, Section Name - {$sectionName}, Question Index - {$questionIndex}");

        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examRef = $document->reference();
                $examData = $document->data();

                if (isset($examData['sections'][$sectionName][$questionIndex])) {
                    $questionToRemove = $examData['sections'][$sectionName][$questionIndex]; // Capture question for logging
                    array_splice($examData['sections'][$sectionName], $questionIndex, 1);

                    // Update the Firestore document
                    $examRef->update([
                        ['path' => 'sections.' . $sectionName, 'value' => $examData['sections'][$sectionName]]
                    ]);

                    Log::info("Successfully deleted question. Course Unit: {$courseUnit}, Section: {$sectionName}, Index: {$questionIndex}, Removed Question: {$questionToRemove}");
                    return back()->with('success', 'Question deleted successfully.');
                } else {
                    Log::warning("Question not found for deletion. Course Unit: {$courseUnit}, Section: {$sectionName}, Index: {$questionIndex}");
                }
            }
        }

        Log::error("Exam not found for deletion. Course Unit: {$courseUnit}");
        return back()->withErrors(['error' => 'Exam or question not found.']);
    }

    public function updateQuestion(Request $request, $courseUnit, $sectionName, $questionIndex)
    {
        Log::info("Entering updateQuestion with parameters: Course Unit - {$courseUnit}, Section Name - {$sectionName}, Question Index - {$questionIndex}");

        $request->validate([
            'question' => 'required|string',
        ]);

        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        if (count($examsSnapshot->rows()) == 0) {
            Log::error("No documents found for Course Unit: {$courseUnit}");
            return back()->withErrors(['error' => 'No exams found.']);
        }

        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examRef = $document->reference();
                $examData = $document->data();
                Log::info("Document found, processing update...", ['Exam Data' => $examData]);

                if (isset($examData['sections'][$sectionName][$questionIndex])) {
                    $oldQuestion = $examData['sections'][$sectionName][$questionIndex]; // Capture old question for logging

                    // Base64 encode the new question before saving it
                    $encodedQuestion = base64_encode($request->question);
                    $examData['sections'][$sectionName][$questionIndex] = $encodedQuestion;

                    // Update the Firestore document
                    $examRef->update([
                        ['path' => 'sections.' . $sectionName, 'value' => $examData['sections'][$sectionName]]
                    ]);

                    Log::info("Successfully updated question. Course Unit: {$courseUnit}, Section: {$sectionName}, Index: {$questionIndex}, Old Question: {$oldQuestion}, New Question: {$request->question}");
                    return back()->with('success', 'Question updated successfully.');
                } else {
                    Log::warning("Question index $questionIndex not found in section $sectionName");
                }
            } else {
                Log::error("Document does not exist for the specified ID.");
            }
        }

        Log::error("Failed to update question for Course Unit: {$courseUnit}");
        return back()->withErrors(['error' => 'Question update failed.']);
    }

    public function addQuestion(Request $request, $courseUnit)
    {
        $request->validate([
            'section' => 'required|string',
            'newQuestion' => 'required|string',
        ]);

        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examRef = $document->reference();
                $examData = $document->data();

                // Base64 encode the new question before adding it
                $encodedQuestion = base64_encode($request->newQuestion);

                // Add the new encoded question to the specified section
                $examData['sections'][$request->section][] = $encodedQuestion;

                // Update the Firestore document
                $examRef->update([
                    ['path' => 'sections.' . $request->section, 'value' => $examData['sections'][$request->section]]
                ]);

                Log::info("Added new question to section: {$request->section} of course unit: {$courseUnit}");
                return back()->with('success', 'New question added successfully.');
            }
        }

        Log::error("Exam not found for course unit: {$courseUnit}");
        return back()->withErrors(['error' => 'Exam not found.']);
    }

    public function updateInstruction(Request $request, $courseUnit)
    {
        $request->validate([
            'sectionA_instructions' => 'required|string',
            'sectionB_instructions' => 'required|string',
        ]);

        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examRef = $document->reference();
                $examData = $document->data();

                // Update instructions for Section A and Section B
                $examData['sectionA_instructions'] = $request->sectionA_instructions;
                $examData['sectionB_instructions'] = $request->sectionB_instructions;

                // Update the Firestore document
                $examRef->update([
                    ['path' => 'sectionA_instructions', 'value' => $examData['sectionA_instructions']],
                    ['path' => 'sectionB_instructions', 'value' => $examData['sectionB_instructions']]
                ]);

                return back()->with('success', 'Instructions updated successfully.');
            }
        }

        return back()->withErrors(['error' => 'Exam not found.']);
    }


    public function previewPdf($courseUnit)
    {
        // Fetch the exam details based on the course unit
        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        if ($examsSnapshot->isEmpty()) {
            return back()->withErrors(['error' => 'No exam found for this course unit.']);
        }

        // Get the first document (since Firestore can return multiple documents)
        $examData = null;
        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examData = $document->data(); // Fetch the exam data
                break; // Exit the loop after getting the first document
            }
        }

        if (!$examData) {
            return back()->withErrors(['error' => 'No exam data found for this course unit.']);
        }

        // Base64 decode only the questions
        foreach ($examData['sections'] as $sectionName => $questions) {
            foreach ($questions as $index => $question) {
                // Base64 decode each question before passing it to the template
                $examData['sections'][$sectionName][$index] = base64_decode($question);
            }
        }

        // Generate the PDF using the available data (course unit, sections, instructions, and questions)
        $pdf = PDF::loadView('lecturer.preview', [
            'courseUnit' => $examData['courseUnit'],
            'sections' => $examData['sections'],
            'sectionAInstructions' => $examData['sectionA_instructions'], // Pass as is
            'sectionBInstructions' => $examData['sectionB_instructions'], // Pass as is
        ]);

        // Set paper size to A4 and orientation to portrait
        $pdf->setPaper('A4', 'portrait');

        // Stream the PDF to the browser where it can be printed or saved
        return $pdf->stream("Preview_{$courseUnit}.pdf");
    }



}
