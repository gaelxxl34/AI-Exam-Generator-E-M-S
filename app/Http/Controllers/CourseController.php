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
        // Get the current user's email from session or authentication
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
            // Instead of throwing error, pass empty courses array to view
            return view('lecturer.l-upload-questions', ['courses' => []]);
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
                    'code' => $data['code'] ?? 'No Code', // Now includes course code
                    'faculty' => $data['faculty'] ?? 'Unknown Faculty'
                ];
            }
        }

        \Log::info('Courses fetched for the lecturer: ' . json_encode($courses));

        // Pass the courses (with faculty and course code) to the view
        return view('lecturer.l-upload-questions', ['courses' => $courses]);

    } catch (\Exception $e) {
        \Log::error('Error in CoursesList: ' . $e->getMessage());
        // Return view with empty courses instead of showing error message
        return view('lecturer.l-upload-questions', ['courses' => [], 'error' => $e->getMessage()]);
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
                    $lecturerCourses = $doc['courses'] ?? [];
                    break; // Assuming one match, we can break the loop once found
                }
            }

            if (empty($lecturerCourses)) {
                \Log::info("Lecturer not found or no courses assigned for: $lecturerEmail");
                // Instead of returning error, pass empty courses array to view
                return view('lecturer.l-dashboard', ['courses' => []]);
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
            // Return view with empty courses instead of error
            return view('lecturer.l-dashboard', ['courses' => [], 'error' => $e->getMessage()]);
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
            $firebaseBaseUrl = env('FIREBASE_STORAGE_BASE_URL'); // e.g. https://firebasestorage.googleapis.com/v0/b/your-bucket/o/
            foreach ($examsSnapshot as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    // Ensure all image src are full URLs
                    foreach ($data['sections'] as $section => $contents) {
                        foreach ($contents as $index => $content) {
                            // Fix image src if needed
                            $fixedHtml = preg_replace_callback(
                                '/<img[^>]+src=["\']([^"\']+)["\']/i',
                                function ($matches) use ($firebaseBaseUrl) {
                                    $src = $matches[1];
                                    // If already a full URL, leave as is
                                    if (preg_match('/^https?:\/\//', $src)) {
                                        return $matches[0];
                                    }
                                    // Otherwise, prepend Firebase Storage base URL
                                    $src = rtrim($firebaseBaseUrl, '/') . '/' . ltrim($src, '/');
                                    return str_replace($matches[1], $src, $matches[0]);
                                },
                                $content
                            );
                            $data['sections'][$section][$index] = $fixedHtml;
                        }
                    }
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
                    $questionToRemove = $examData['sections'][$sectionName][$questionIndex]; // Now HTML, not base64
                    Log::info("📄 Question Content Before Deletion: " . $questionToRemove);

                    // Extract Image URLs from Question Content
                    preg_match_all('/<img.*?src=["\'](.*?)["\']/', $questionToRemove, $matches);
                    $imagesToDelete = $matches[1] ?? [];

                    Log::info("🖼 Images found for deletion: " . json_encode($imagesToDelete));

                    // Delete Images from Firebase Storage
                    foreach ($imagesToDelete as $imageUrl) {
                        // Try to extract the storage object path from the URL
                        $parsed = parse_url($imageUrl);
                        $path = $parsed['path'] ?? '';
                        $path = ltrim($path, '/');

                        // Only try to delete if path looks like a storage object
                        if ($path) {
                            $object = $storage->object($path);
                            if ($object->exists()) {
                                $object->delete();
                                Log::info("✅ Deleted image: " . $imageUrl);
                            } else {
                                Log::warning("⚠ Image not found in storage (already deleted?): " . $imageUrl);
                            }
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


    /**
     * Process images in question HTML: upload base64 images to Firebase Storage and replace src with storage URL.
     */
    private function processQuestionImages($html, $courseUnit, $section, $index)
    {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches);
        $imageSources = $matches[1] ?? [];
        $storage = app('firebase.storage');
        $bucket = $storage->getBucket();
        foreach ($imageSources as $imgSrc) {
            if (strpos($imgSrc, 'data:image') === 0) {
                if (preg_match('/data:image\/(.*?);base64,(.*)/', $imgSrc, $imgParts)) {
                    $extension = $imgParts[1] ?? 'png';
                    $data = $imgParts[2];
                    $imageData = base64_decode($data);
                    $filename = 'questions/' . $courseUnit . '_' . $section . '_' . $index . '_' . uniqid() . '.' . $extension;
                    $object = $bucket->upload($imageData, [
                        'name' => $filename
                    ]);
                    $imageUrl = $object->signedUrl(new \DateTime('+1 year'));
                    $html = str_replace($imgSrc, $imageUrl, $html);
                }
            }
        }
        return $html;
    }

    public function updateQuestion(Request $request, $courseUnit, $sectionName, $questionIndex)
    {
        Log::info("Updating question: Course Unit - {$courseUnit}, Section - {$sectionName}, Index - {$questionIndex}");

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

                if (!isset($examData['sections'][$sectionName])) {
                    Log::error("Section '{$sectionName}' not found.");
                    return back()->withErrors(['error' => "Section '{$sectionName}' not found."]);
                }

                // Process images and save as HTML
                $processedHtml = $this->processQuestionImages($request->question, $courseUnit, $sectionName, $questionIndex);
                $examData['sections'][$sectionName][$questionIndex] = $processedHtml;

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

                // Process images in the new question HTML and store as HTML (not base64)
                $processedQuestion = $this->processQuestionImages($request->newQuestion, $courseUnit, $request->section, count($examData['sections'][$request->section] ?? []));

                // Ensure Section C exists if faculty is FOL and it's selected
                if ($examData['faculty'] == 'FOL' && $request->section == 'C' && !isset($examData['sections']['C'])) {
                    $examData['sections']['C'] = [];
                }

                // Add the processed question HTML to the specified section
                $examData['sections'][$request->section][] = $processedQuestion;

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
        $firestore = app('firebase.firestore')->database();
        $examsRef = $firestore->collection('Exams');
        $query = $examsRef->where('courseUnit', '==', $courseUnit);
        $examsSnapshot = $query->documents();
        if ($examsSnapshot->isEmpty()) {
            return back()->withErrors(['error' => 'No exam found for this course unit.']);
        }
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
        // Ensure public/pdf_images/ exists and is clean
        $publicPath = public_path('pdf_images/');
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
            Log::info("📂 Created directory for storing preview images: {$publicPath}");
        }
        File::cleanDirectory($publicPath);
        Log::info("🗑 Cleared old preview images from public/pdf_images.");
        // Process each question to replace Firebase image URLs with local paths
        foreach ($examData['sections'] as $sectionName => $questions) {
            foreach ($questions as $index => $questionHtml) {
                $processedHtml = $questionHtml;
                preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $processedHtml, $matches);
                $imageUrls = $matches[1] ?? [];
                Log::info("🔗 Found Image URLs:", $imageUrls);
                foreach ($imageUrls as $imageUrl) {
                    $relativePath = $this->downloadFirebaseImage($imageUrl);
                    if ($relativePath) {
                        $processedHtml = str_replace($imageUrl, $relativePath, $processedHtml);
                        Log::info("✅ Image replaced: {$imageUrl} -> {$relativePath}");
                    } else {
                        Log::error("❌ Failed to download image: {$imageUrl}");
                    }
                }
                $examData['sections'][$sectionName][$index] = $processedHtml;
            }
        }
        $pdf = Pdf::loadView('lecturer.preview', [
            'courseUnit' => $examData['courseUnit'],
            'sections' => $examData['sections'],
            'sectionAInstructions' => $examData['sectionA_instructions'] ?? '',
            'sectionBInstructions' => $examData['sectionB_instructions'] ?? '',
        ]);
        Log::info("✅ PDF generated successfully for Course Unit: {$courseUnit}");
        return $pdf->stream("Preview_{$courseUnit}.pdf");
    }

    private function downloadFirebaseImage($imageUrl)
    {
        try {
            // Decode HTML entities (e.g., &amp; to &)
            $decodedUrl = html_entity_decode($imageUrl);
            $imageContent = @file_get_contents($decodedUrl);
            if (!$imageContent) {
                Log::error("⚠ Failed to download image from Firebase: {$decodedUrl}");
                return null;
            }
            $fileName = 'pdf_' . uniqid() . '.jpg';
            $publicFilePath = public_path('pdf_images/' . $fileName);
            file_put_contents($publicFilePath, $imageContent);
            // Return relative path for DomPDF
            return 'pdf_images/' . $fileName;
        } catch (\Exception $e) {
            Log::error("❌ Error downloading image from Firebase: " . $e->getMessage());
            return null;
        }
    }



}
