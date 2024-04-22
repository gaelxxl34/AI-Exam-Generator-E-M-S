<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

}
