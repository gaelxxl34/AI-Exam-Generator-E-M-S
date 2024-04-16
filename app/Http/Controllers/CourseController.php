<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function fetchCourses() // Method used to display the courses which the exam has been uploaded 
    {
        \Log::info('Fetching courses for dashboard');

        try {
            $firestore = app('firebase.firestore')->database();
            $examsRef = $firestore->collection('Exams');
            $examsSnapshot = $examsRef->documents();

            $courses = [];

            foreach ($examsSnapshot as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    $courseUnit = $data['courseUnit'] ?? 'Unknown Course';

                    // Aggregate exams under the same course unit
                    if (!isset($courses[$courseUnit])) {
                        $courses[$courseUnit] = [];
                    }

                    $courses[$courseUnit][] = $data;
                }
            }

            return view('lecturer.l-dashboard', ['courses' => $courses]);
        } catch (\Throwable $e) {
            \Log::error("Error fetching courses: " . $e->getMessage());
            return back()->withErrors(['fetch_error' => 'Error fetching courses.'])->with('message', 'Error fetching courses: ' . $e->getMessage());
        }
    }

    public function courseDetails($courseUnit)
    {
        \Log::info("Fetching details for course unit: $courseUnit");

        try {
            $firestore = app('firebase.firestore')->database();
            $examsRef = $firestore->collection('Exams');
            $query = $examsRef->where('courseUnit', '==', $courseUnit);
            $examsSnapshot = $query->documents();

            $storage = app('firebase.storage')->getBucket();
            $exams = [];

            foreach ($examsSnapshot as $document) {
                if ($document->exists()) {
                    $data = $document->data();

                    // Process each section's content to update image URLs
                    foreach ($data['sections'] as $section => $contents) {
                        foreach ($contents as $index => $content) {
                            $doc = new \DOMDocument();
                            @$doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                            $images = $doc->getElementsByTagName('img');
                            foreach ($images as $img) {
                                $src = $img->getAttribute('src');
                                // Check if the image src is from summernote_images directory
                                if (strpos($src, 'summernote_images/') === 0) {
                                    // Generate a signed URL for the image
                                    $imagePath = $src;
                                    $signedUrl = $storage->object($imagePath)->signedUrl(new \DateTime('+1 hour'));
                                    $img->setAttribute('src', $signedUrl);
                                }
                            }

                            // Update the content with new image URLs
                            $data['sections'][$section][$index] = $doc->saveHTML();
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

        // Fetch all courses
        $coursesRef = $database->collection('Courses');
        $coursesSnapshots = $coursesRef->documents();

        $courses = [];
        foreach ($coursesSnapshots as $document) {
            if ($document->exists()) {
                $data = $document->data();
                $courses[] = [
                    'id' => $document->id(),
                    'name' => $data['name'] ?? 'Unknown Course' // Ensuring there is a default name
                ];
            }
        }

        // Pass the courses to the view
        return view('superadmin/add-lecturer', ['courses' => $courses]);
    }





}
