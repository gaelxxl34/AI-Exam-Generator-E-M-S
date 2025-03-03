<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth for user session
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Fetch the current user's email and faculty
        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;

        // Fetch current user's data to get their faculty
        $userRef = $database->collection('Users')->where('email', '==', $currentUserEmail);
        $userSnapshot = $userRef->documents()->rows()[0] ?? null;
        if (!$userSnapshot) {
            throw new \Exception('User not found.');
        }
        $currentUserFaculty = $userSnapshot->data()['faculty'] ?? 'No faculty assigned';

        // Filter and count documents in Users where role is 'lecturer' and faculty matches
        $lecturersQuery = $database->collection('Users')->where('role', '==', 'lecturer')->where('faculty', '==', $currentUserFaculty);
        $lecturerCount = $lecturersQuery->documents()->size();

        // Filter and count all documents in pastExams that match the faculty
        $pastExamsQuery = $database->collection('pastExams')->where('faculty', '==', $currentUserFaculty);
        $pastExamsCount = $pastExamsQuery->documents()->size();

        // Filter and count all documents in Courses that match the faculty
        $coursesQuery = $database->collection('Courses')->where('faculty', '==', $currentUserFaculty);
        $coursesCount = $coursesQuery->documents()->size();

        // Pass the counts to the view
        return view('admin.dashboard', [
            'lecturerCount' => $lecturerCount,
            'pastExamsCount' => $pastExamsCount,
            'coursesCount' => $coursesCount,
            'faculty' => $currentUserFaculty  // Optional, to display on dashboard if needed
        ]);
    }

    public function genAdminDashboard()
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        // Fetch the current user's email and faculty
        $currentUserEmail = session()->get('user_email') ?? auth()->user()->email;

        // Fetch current user's data to get their faculty
        $userRef = $database->collection('Users')->where('email', '==', $currentUserEmail);
        $currentUserSnapshots = $userRef->documents();

        if ($currentUserSnapshots->isEmpty()) {
            \Log::error("User not found with email: $currentUserEmail");
            throw new \Exception('User not found.');
        }

        $currentUserDocument = iterator_to_array($currentUserSnapshots)[0];
        $currentUserFaculty = $currentUserDocument->data()['faculty'] ?? 'No faculty assigned';
        \Log::info("Current user faculty: $currentUserFaculty");

        $containsComma = strpos($currentUserFaculty, ',') !== false;
        \Log::info("Faculty field contains comma: " . ($containsComma ? 'Yes' : 'No'));

        if ($containsComma) {
            // If faculty field contains a comma, fetch counts without faculty filters
            $lecturerCount = $database->collection('Users')->where('role', '==', 'lecturer')->documents()->size();
            $pastExamsCount = $database->collection('pastExams')->documents()->size();
            $coursesCount = $database->collection('Courses')->documents()->size();
            \Log::info("Fetching counts for all faculties.");
        } else {
            // Filter and count documents based on a specific faculty
            $lecturerCount = $database->collection('Users')->where('role', '==', 'lecturer')->where('faculty', '==', $currentUserFaculty)->documents()->size();
            $pastExamsCount = $database->collection('pastExams')->where('faculty', '==', $currentUserFaculty)->documents()->size();
            $coursesCount = $database->collection('Courses')->where('faculty', '==', $currentUserFaculty)->documents()->size();
            \Log::info("Fetching counts for specific faculty: $currentUserFaculty");
        }

        \Log::info("Counts - Lecturers: $lecturerCount, Past Exams: $pastExamsCount, Courses: $coursesCount");

        // Pass the counts to the view
        return view('genadmin.gen-dashboard', [
            'lecturerCount' => $lecturerCount,
            'pastExamsCount' => $pastExamsCount,
            'coursesCount' => $coursesCount,
            'faculty' => $currentUserFaculty  // Optional, to display on dashboard if needed
        ]);
    }

    public function index()
    {
        try {
            $faculty = session('user_faculty'); // Get dean's faculty from session
            \Log::info("Fetching courses for faculty: {$faculty}");

            $firestore = app('firebase.firestore')->database();
            $examsRef = $firestore->collection('Exams');
            $query = $examsRef->where('faculty', '==', $faculty);
            $examsSnapshot = $query->documents();

            $courses = [];

            foreach ($examsSnapshot as $document) {
                if ($document->exists()) {
                    $examData = $document->data();
                    $examData['id'] = $document->id();
                    $examData['status'] = $examData['status'] ?? 'Pending Review'; // Default if no status field
                    $courses[] = $examData;
                }
            }

            \Log::info("Courses fetched successfully for faculty {$faculty}.", ['count' => count($courses)]);
            return view('deans.dean-dashboard', compact('courses'));

        } catch (\Exception $e) {
            \Log::error("Error fetching courses: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to fetch courses.']);
        }
    }


    public function updateStatus(Request $request, $id)
    {
        try {
            $firestore = app('firebase.firestore')->database();
            $examRef = $firestore->collection('Exams')->document($id);
            $examSnapshot = $examRef->snapshot();

            if (!$examSnapshot->exists()) {
                return response()->json(['error' => 'Exam not found'], 404);
            }

            $status = $request->input('status');
            $examRef->update([
                ['path' => 'status', 'value' => $status]
            ]);

            return response()->json(['success' => true, 'status' => $status]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update status'], 500);
        }
    }


    public function approve($id)
    {
        $firestore = app('firebase.firestore')->database();
        $courseRef = $firestore->collection('Exams')->document($id);

        $courseRef->update([
            ['path' => 'status', 'value' => 'Approved']
        ]);

        return back()->with('success', 'Course approved successfully.');
    }

    public function decline($id)
    {
        $firestore = app('firebase.firestore')->database();
        $courseRef = $firestore->collection('Exams')->document($id);

        $courseRef->update([
            ['path' => 'status', 'value' => 'Declined']
        ]);

        return back()->with('success', 'Course declined.');
    }

}
