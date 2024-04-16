<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PastExamController extends Controller
{
    // The function below is used to upload past exams into firestore 
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'courseUnit' => 'required',
            'year' => 'required',
            'fileUpload' => 'required|file|mimes:pdf'
        ]);

        $file = $request->file('fileUpload');
        $base64File = base64_encode(file_get_contents($file));

        $firestore = app('firebase.firestore');
        $database = $firestore->database();

        $data = [
            'courseUnit' => $validatedData['courseUnit'],
            'year' => $validatedData['year'],
            'file' => $base64File
        ];

        $database->collection('pastExams')->add($data);

        return redirect()->intended('admin.view-past-exams')->with('success', 'Exam uploaded successfully!');
        
    }


    public function fetchPastExams()
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();
        $pastExams = $database->collection('pastExams')->documents();

        $groupedData = [];
        foreach ($pastExams as $exam) {
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

        return view('admin.view-past-exams', ['examsData' => $groupedData]);
    }

}
