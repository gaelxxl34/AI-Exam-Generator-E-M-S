<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
class CourseController extends Controller

{
    public function CoursesList()
    {
        \Log::info('CoursesList method called');

        try {
            $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;
            \Log::info('Current user email: ' . $currentUserEmail);

            $firestore = app('firebase.firestore')->database();
            $usersRef = $firestore->collection('Users');

            // Fetch the current user from Firestore
            $query = $usersRef->where('email', '==', $currentUserEmail);
            $currentUserSnapshots = $query->documents();

            if ($currentUserSnapshots->isEmpty()) {
                \Log::error("Firestore user not found with email: $currentUserEmail");
                throw new \Exception('Current user not found in Firestore.');
            }

            // Get user data
            $currentUserDataArray = iterator_to_array($currentUserSnapshots);
            $currentUserData = $currentUserDataArray[0]->data();

            // Get the courses assigned to this lecturer
            $lecturerCourses = $currentUserData['courses'] ?? [];

            if (empty($lecturerCourses)) {
                \Log::info('No courses found for the current user: ' . $currentUserEmail);
                throw new \Exception('No courses found for the current user.');
            }

            // Fetch only the courses that belong to this lecturer from Firestore "Courses" collection
            $coursesRef = $firestore->collection('Courses');
            $coursesSnapshots = $coursesRef->where('name', 'in', $lecturerCourses)->documents();

            $courses = [];
            foreach ($coursesSnapshots as $course) {
                if ($course->exists()) {
                    $data = $course->data();
                    $courses[] = [
                        'name' => $data['name'] ?? 'Unknown Course',
                        'faculty' => $data['faculty'] ?? 'Unknown Faculty'
                    ];
                }
            }

            \Log::info('Courses fetched for the lecturer: ' . json_encode($courses));

            // Pass the courses (with faculty) to the view
            return view('lecturer.l-upload-questions', ['courses' => $courses]);

        } catch (\Exception $e) {
            \Log::error('Error in CoursesList: ' . $e->getMessage());
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

        // Extract form inputs
        $courseUnit = $request->input('courseUnit');
        $courseCode = $request->input('courseCode');
        $program = $request->input('program');
        $yearSem = 'Year ' . $request->input('year') . '/Semester ' . $request->input('semester');

        // Check if the course code already exists in the Courses collection
        $coursesRef = $database->collection('Courses');
        $query = $coursesRef->where('code', '==', $courseCode);
        $existingCourses = $query->documents();

        if (!$existingCourses->isEmpty()) {
            foreach ($existingCourses as $doc) {
                $existingCourseData = $doc->data();
                $existingCourseName = $existingCourseData['name'] ?? 'Unknown Course';

                \Log::warning("Course code '$courseCode' already exists for '$existingCourseName'.");
                return back()->withErrors(["error" => "The course code '$courseCode' is already assigned to '$existingCourseName'. Please use a unique course code."]);
            }
        }

        // Prepare course data for Firestore
        $courseData = [
            'name' => $courseUnit,
            'code' => $courseCode,
            'program' => $program,
            'year_sem' => $yearSem,
            'faculty' => $faculty
        ];

        // Upload to Firestore
        $coursesRef->add($courseData);

        \Log::info("New course '$courseUnit' added successfully with code '$courseCode'.");

        // Redirect to courses list with success message
        return redirect()->intended('admin/courses-list')->with('success', 'Course uploaded successfully!');
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

    public function deleteCourse($id)
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();
        $courseRef = $database->collection('Courses')->document($id);

        try {
            // Delete the course document
            $courseRef->delete();

            return redirect()->intended('admin/courses-list')->with('success', ' Deleted successfully!');;
        } catch (\Throwable $e) {
            return back()->with('error', 'Error deleting course: ' . $e->getMessage());
        }
    }

    // -- END of the list 




    // COURSES BY FACULTY
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
                        'id' => $document->id(),  // Document ID
                        'name' => $data['name'] ?? 'Unknown Course',  // Course name
                        'code' => $data['code'] ?? 'N/A'  // Course code (ensure key exists)
                    ];
                    \Log::info("Course fetched: " . $data['name'] . " (Code: " . ($data['code'] ?? 'N/A') . ")");
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

            // 1️⃣ **Fetch all approved exams first**
            $approvedExamsRef = $database->collection('Exams')->where('status', '==', 'Approved');
            $approvedExamsSnapshots = $approvedExamsRef->documents();

            if ($approvedExamsSnapshots->isEmpty()) {
                \Log::info("No approved exams found.");
                return view('genadmin.ai-exam-generator', ['courses' => []]);
            }

            // 2️⃣ **Extract course names from approved exams**
            $approvedCourseNames = [];
            foreach ($approvedExamsSnapshots as $exam) {
                $examData = $exam->data();
                if (!empty($examData['courseUnit'])) {
                    $approvedCourseNames[] = $examData['courseUnit'];
                }
            }

            $approvedCourseNames = array_unique($approvedCourseNames); // Remove duplicates
            \Log::info("Approved course names: " . implode(", ", $approvedCourseNames));

            // 3️⃣ **Fetch only the courses that match these names**
            $coursesRef = $database->collection('Courses');
            $coursesSnapshots = $coursesRef->documents();

            $filteredCourses = [];
            foreach ($coursesSnapshots as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    if (in_array($data['name'], $approvedCourseNames)) { // Match with approved courses
                        $filteredCourses[] = [
                            'id' => $document->id(),
                            'name' => $data['name'] ?? 'Unknown Course',
                            'code' => $data['code'] ?? 'N/A' // Ensure we get the course code
                        ];
                    }
                }
            }

            \Log::info("Number of approved courses fetched: " . count($filteredCourses));

            // Pass the filtered courses with course codes to the view
            return view('genadmin.ai-exam-generator', ['courses' => $filteredCourses]);

        } catch (\Exception $e) {
            \Log::error("Error fetching courses: " . $e->getMessage());
            return view('genadmin.ai-exam-generator', ['courses' => [], 'error' => 'Failed to fetch courses.']);
        }
    }







    public function deleteQuestion($courseUnit, $sectionName, $questionIndex)
    {
        Log::info("🗑 Entering deleteQuestion with parameters: Course Unit - {$courseUnit}, Section Name - {$sectionName}, Question Index - {$questionIndex}");

        $firestore = app('firebase.firestore')->database();
        $storage = app('firebase.storage')->getBucket();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examRef = $document->reference();
                $examData = $document->data();

                if (isset($examData['sections'][$sectionName][$questionIndex])) {
                    $questionToRemove = base64_decode($examData['sections'][$sectionName][$questionIndex]); // Decode question content
                    Log::info("📄 Question Content Before Deletion: " . $questionToRemove);

                    // Extract Image URLs from Question Content
                    preg_match_all('/<img.*?src=["\'](.*?)["\']/', $questionToRemove, $matches);
                    $imagesToDelete = $matches[1] ?? [];

                    Log::info("🖼 Images found for deletion: " . json_encode($imagesToDelete));

                    // Delete Images from Firebase Storage
                    foreach ($imagesToDelete as $imageUrl) {
                        $path = urldecode(parse_url($imageUrl, PHP_URL_PATH));
                        $path = str_replace('/v0/b/' . env('FIREBASE_STORAGE_BUCKET') . '/o/', '', $path);
                        $path = explode('?alt=media', $path)[0];

                        $object = $storage->object($path);
                        if ($object->exists()) {
                            $object->delete();
                            Log::info("✅ Deleted image: " . $imageUrl);
                        } else {
                            Log::warning("⚠ Image not found in storage (already deleted?): " . $imageUrl);
                        }
                    }

                    // Remove Question from Firestore
                    array_splice($examData['sections'][$sectionName], $questionIndex, 1);
                    $examRef->update([
                        ['path' => 'sections.' . $sectionName, 'value' => $examData['sections'][$sectionName]]
                    ]);

                    Log::info("✅ Successfully deleted question. Course Unit: {$courseUnit}, Section: {$sectionName}, Index: {$questionIndex}");
                    return back()->with('success', 'Question deleted successfully.');
                } else {
                    Log::warning("❌ Question not found for deletion. Course Unit: {$courseUnit}, Section: {$sectionName}, Index: {$questionIndex}");
                }
            }
        }

        Log::error("❌ Exam not found for deletion. Course Unit: {$courseUnit}");
        return back()->withErrors(['error' => 'Exam or question not found.']);
    }


    public function updateQuestion(Request $request, $courseUnit, $sectionName, $questionIndex)
    {
        Log::info("Updating question: Course Unit - {$courseUnit}, Section - {$sectionName}, Index - {$questionIndex}");

        // Validate that the question field is provided
        $request->validate([
            'question' => 'required|string',
        ]);

        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        if ($examsSnapshot->isEmpty()) {
            Log::error("No exam found for Course Unit: {$courseUnit}");
            return back()->withErrors(['error' => 'No exam found.']);
        }

        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examRef = $document->reference();
                $examData = $document->data();

                // Ensure section and index exist before updating
                if (!isset($examData['sections'][$sectionName])) {
                    Log::error("Section '{$sectionName}' not found.");
                    return back()->withErrors(['error' => "Section '{$sectionName}' not found."]);
                }

                // Convert question content to Base64 before saving
                $encodedQuestion = base64_encode($request->question);

                // Store the Base64-encoded question in Firestore
                $examData['sections'][$sectionName][$questionIndex] = $encodedQuestion;

                // Update Firestore
                try {
                    $examRef->update([
                        ['path' => "sections.{$sectionName}", 'value' => $examData['sections'][$sectionName]]
                    ]);
                    Log::info("Question updated successfully.");
                    return back()->with('success', 'Question updated successfully.');
                } catch (\Exception $e) {
                    Log::error("Firestore update failed: " . $e->getMessage());
                    return back()->withErrors(['error' => 'Failed to update question.']);
                }
            }
        }

        Log::error("Failed to update question.");
        return back()->withErrors(['error' => 'Failed to update question.']);
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

                // Ensure Section C exists if faculty is FOL and it's selected
                if ($examData['faculty'] == 'FOL' && $request->section == 'C' && !isset($examData['sections']['C'])) {
                    $examData['sections']['C'] = [];
                }

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
            'sectionC_instructions' => 'nullable|string' // Optional field
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

                // Check if Section C instructions are provided and update them
                if ($request->filled('sectionC_instructions')) {
                    $examData['sectionC_instructions'] = $request->sectionC_instructions;
                    $updateData = [
                        ['path' => 'sectionA_instructions', 'value' => $examData['sectionA_instructions']],
                        ['path' => 'sectionB_instructions', 'value' => $examData['sectionB_instructions']],
                        ['path' => 'sectionC_instructions', 'value' => $examData['sectionC_instructions']],
                    ];
                } else {
                    $updateData = [
                        ['path' => 'sectionA_instructions', 'value' => $examData['sectionA_instructions']],
                        ['path' => 'sectionB_instructions', 'value' => $examData['sectionB_instructions']]
                    ];
                }

                // Update the Firestore document
                $examRef->update($updateData);

                return back()->with('success', 'Instructions updated successfully.');
            }
        }

        return back()->withErrors(['error' => 'Exam not found.']);
    }


    public function uploadFile(Request $request, $courseUnit)
    {
        // Validate the file input
        $request->validate([
            'attached_file' => 'required|mimes:pdf,doc,docx,xls,xlsx|max:3072', // Max size 3MB (3072 KB)
        ]);

        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examRef = $document->reference();
                $examData = $document->data();

                // Handle file upload and conversion to base64
                $file = $request->file('attached_file');
                $fileContents = file_get_contents($file->getRealPath());
                $base64File = base64_encode($fileContents); // Convert file to base64

                // Get the file's original extension to store its type
                $fileType = $file->getClientOriginalExtension();

                // Update Firestore document with the base64-encoded file and its type
                $examRef->update([
                    ['path' => 'marking_guide', 'value' => $base64File],
                    ['path' => 'attached_file_type', 'value' => $fileType],
                ]);

                return back()->with('success_file', 'File uploaded and saved successfully.');
            }
        }

        return back()->withErrors(['error_file' => 'Exam not found.']);
    }


    public function downloadMarkingGuide($courseUnit)
    {

        \Log::info("Download Marking Guide method hit for course: " . $courseUnit);

        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examData = $document->data();
                // Make sure to check for the correct field name: 'marking_guide'
                if (isset($examData['marking_guide']) && isset($examData['attached_file_type'])) {
                    // Retrieve the base64 file and its type
                    $base64File = $examData['marking_guide'];
                    $fileType = $examData['attached_file_type'];

                    // Decode the base64 file
                    $fileContents = base64_decode($base64File);

                    // Manually set the MIME type based on the file extension
                    $mimeType = '';
                    switch (strtolower($fileType)) {
                        case 'pdf':
                            $mimeType = 'application/pdf';
                            break;
                        case 'doc':
                            $mimeType = 'application/msword';
                            break;
                        case 'docx':
                            $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                            break;
                        case 'xls':
                            $mimeType = 'application/vnd.ms-excel';
                            break;
                        case 'xlsx':
                            $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                            break;
                        default:
                            return back()->withErrors(['error_file' => 'Unsupported file type.']);
                    }

                    // Set file name based on the file type (e.g., marking_guide.pdf)
                    $fileName = "marking_guide." . $fileType;

                    // Return the file as a download
                    return response($fileContents)
                        ->header('Content-Type', $mimeType)
                        ->header('Content-Disposition', 'attachment; filename=' . $fileName);
                } else {
                    return back()->withErrors(['error_file' => 'No marking guide file found.']);
                }
            }
        }

        return back()->withErrors(['error_file' => 'Exam not found.']);
    }


    public function previewPdf($courseUnit)
    {
        Log::info("📝 Generating PDF preview for Course Unit: {$courseUnit}");

        // 🔹 Fetch the exam details
        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();

        if ($examsSnapshot->isEmpty()) {
            return back()->withErrors(['error' => 'No exam found for this course unit.']);
        }

        // 🔹 Get the first matching document
        $examData = null;
        foreach ($examsSnapshot as $document) {
            if ($document->exists()) {
                $examData = $document->data();
                break;
            }
        }

        if (!$examData) {
            return back()->withErrors(['error' => 'No exam data found for this course unit.']);
        }

        // 🔹 Define Storage Path (`storage/app/pdf_images/`)
        $storagePath = storage_path('app/pdf_images/');

        // 🔹 Ensure the directory exists (Create if not)
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
            Log::info("📂 Created directory for storing preview images: {$storagePath}");
        }

        // 🔹 Delete previous preview images before generating new ones
        File::cleanDirectory($storagePath); // Remove all old preview images
        Log::info("🗑 Cleared old preview images from storage.");

        // 🔹 Process each question to replace Firebase image URLs
        foreach ($examData['sections'] as $sectionName => $questions) {
            foreach ($questions as $index => $question) {
                $decodedQuestion = base64_decode($question);

                // 🔍 Extract image URLs from the question content
                preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/', $decodedQuestion, $matches);
                $imageUrls = $matches[1] ?? [];

                Log::info("🔗 Found Image URLs:", $imageUrls);

                // 🔹 Replace image URLs with local paths
                foreach ($imageUrls as $imageUrl) {
                    // Generate a unique filename for local storage
                    $fileName = 'pdf_' . uniqid() . '.jpg';
                    $localPath = $storagePath . $fileName;

                    // 🔻 Download and save image locally in `storage/app/pdf_images/`
                    try {
                        file_put_contents($localPath, file_get_contents($imageUrl));

                        // 🔹 Convert storage path to Laravel storage URL
                        $imageServePath = storage_path("app/pdf_images/{$fileName}");
                        $decodedQuestion = str_replace($imageUrl, $imageServePath, $decodedQuestion);

                        Log::info("✅ Image replaced: {$imageUrl} -> {$imageServePath}");
                    } catch (\Exception $e) {
                        Log::error("❌ Failed to download image: {$imageUrl}, Error: " . $e->getMessage());
                    }
                }

                // 🔹 Update the exam data with new image paths
                $examData['sections'][$sectionName][$index] = $decodedQuestion;
            }
        }

        // 🔹 Generate the PDF with updated content
        $pdf = Pdf::loadView('lecturer.preview', [
            'courseUnit' => $examData['courseUnit'],
            'sections' => $examData['sections'],
            'sectionAInstructions' => $examData['sectionA_instructions'] ?? '',
            'sectionBInstructions' => $examData['sectionB_instructions'] ?? '',
        ]);

        Log::info("✅ PDF generated successfully for Course Unit: {$courseUnit}");

        // Stream the PDF to the browser
        return $pdf->stream("Preview_{$courseUnit}.pdf");
    }


    private function downloadFirebaseImage($imageUrl)
    {
        try {
            // Get Image Contents
            $imageContent = file_get_contents($imageUrl);
            if (!$imageContent) {
                Log::error("⚠ Failed to download image from Firebase: {$imageUrl}");
                return null;
            }

            // Generate a unique filename
            $imageName = 'pdf_images/' . uniqid() . '.jpg';

            // Save Image Locally
            Storage::disk('public')->put($imageName, $imageContent);

            // Return Local Path for PDF
            return public_path("storage/{$imageName}");
        } catch (\Exception $e) {
            Log::error("❌ Error downloading image from Firebase: " . $e->getMessage());
            return null;
        }
    }



}
