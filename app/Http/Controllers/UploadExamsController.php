<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use setasign\Fpdi\Fpdi;

class UploadExamsController extends Controller
{
    public function uploadExam(Request $request)
    {
        \Log::info('uploadExam method called');

        // Start with basic validation
        $validatedData = $request->validate([
            'faculty' => 'required|string',
            'courseUnit' => 'required|string', // Validate the course unit
            'sectionA' => 'required|array|min:1',
            'sectionB' => 'required|array|min:1',
            'sectionC' => 'sometimes|required|array|min:1',
            'sectionA.*' => 'required|string|max:255',
            'sectionB.*' => 'required|string|max:255',
            'sectionC.*' => 'sometimes|required|string|max:255',
        ]);

        try {
            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $examsRef = $database->collection('Exams');

            $examData = [
                'faculty' => $request->faculty,

                'courseUnit' => $request->courseUnit, // Include the course unit in the exam data
                'created_at' => new \DateTime(),
            ];

            if ($request->has('sectionA')) {
                $examData['sections']['A'] = array_combine(
                    range(1, count($request->sectionA)),
                    array_values($request->sectionA)
                );
            }

            if ($request->has('sectionB')) {
                $examData['sections']['B'] = array_combine(
                    range(1, count($request->sectionB)),
                    array_values($request->sectionB)
                );
            }

            if ($request->has('sectionC')) {
                $examData['sections']['C'] = array_combine(
                    range(1, count($request->sectionC)),
                    array_values($request->sectionC)
                );
            }

            $examsRef->add($examData);

            return redirect()->route('admin.dashboard')->with('success', 'Exam uploaded successfully.');
        } catch (\Throwable $e) {
            \Log::error($e->getMessage());
            return back()->withErrors(['upload_error' => 'Error uploading exam.'])->with('message', 'Error uploading exam: ' . $e->getMessage());
        }
    }




    private function paraphraseText($text, $apiKey)
    {
        \Log::info('API Key: ' . $apiKey);

        $paraphraseEndpoint = 'https://api.ai21.com/studio/v1/paraphrase';

        \Log::info('Calling Paraphrase API for text: ' . $text);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($paraphraseEndpoint, [
                    'text' => $text,
                    'numOfParaphrases' => 1,
                    'maxOutputLength' => 400 // Adjust as per your requirement
                ]);

        \Log::info('API Response: ' . $response->body());

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['suggestions'])) {
                // Randomly select one of the paraphrases
                $randomIndex = array_rand($data['suggestions']);
                return $data['suggestions'][$randomIndex]['text'];
            }
        }

        \Log::info('paraphraseText method completed with original text');
        return $text; // Return original text if paraphrase fails
    }




    public function getRandomQuestions(Request $request)
    {
        \Log::info('getRandomQuestions method started');

        // Assuming the selected course is passed as a request parameter
        $selectedCourse = $request->input('course');

        try {
            // Query Firestore to get exams for the selected course
            $examsQuery = app('firebase.firestore')->database()->collection('Exams')
                ->where('courseUnit', '==', $selectedCourse);
            $examsSnapshot = $examsQuery->documents();

            if (!$examsSnapshot->isEmpty()) {
                $sectionAQuestions = [];
                $sectionBQuestions = [];

                foreach ($examsSnapshot as $exam) {
                    $data = $exam->data();

                    if (isset($data['sections']['A'])) {
                        $sectionAQuestions = array_merge($sectionAQuestions, $data['sections']['A']);
                    }

                    if (isset($data['sections']['B'])) {
                        $sectionBQuestions = array_merge($sectionBQuestions, $data['sections']['B']);
                    }
                }

                shuffle($sectionAQuestions);
                shuffle($sectionBQuestions);

                $apiKey = env('AI21_API_KEY'); // Make sure to set this in your .env file

                // Paraphrase Section A Questions
                $randomAQuestions = array_slice($sectionAQuestions, 0, 10);
                $randomAQuestions = array_map(function ($question) use ($apiKey) {
                    return $this->paraphraseText($question, $apiKey);
                }, $randomAQuestions);

                // Paraphrase Section B Questions (if needed)
                $randomBQuestions = array_slice($sectionBQuestions, 0, 4);
                $randomBQuestions = array_map(function ($question) use ($apiKey) {
                    return $this->paraphraseText($question, $apiKey);
                }, $randomBQuestions);



                return View::make('admin.view-generated-exam', [
                    'courseUnit' => $selectedCourse,
                    'sectionAQuestions' => $randomAQuestions,
                    'sectionBQuestions' => $randomBQuestions,
                ]);
            } else {
                return 'No exams found for the selected course';
            }
        } catch (\Exception $e) {
            \Log::error('Error in getRandomQuestions: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }


    public function addQuestionsToPdf($sectionAQuestions, $sectionBQuestions, $courseUnit)
    {
        \Log::info('addQuestionsToPdf method started');

        $pdf = new Fpdi();

        // Load the PDF template
        $templatePath = public_path('template.pdf');
        $pdf->setSourceFile($templatePath);

        // Import and add the first page of the template
        $pdf->AddPage();
        $tplIdFirst = $pdf->importPage(1);
        $pdf->useTemplate($tplIdFirst);

        // Import and add the second page of the template for the content
        $pdf->AddPage();
        $tplIdSecond = $pdf->importPage(2);
        $pdf->useTemplate($tplIdSecond);

        // Title and questions settings
        $pdf->SetFont('Helvetica', 'B', 14);

        // Coordinates for Section A title and questions
        $x = 10;
        $y = 10;

        // Section A Title
        $pdf->SetXY($x, $y);
        $pdf->Cell(0, 10, 'SECTION A (40 MARKS)', 0, 1, 'C');
        $y += 15;

        $pdf->SetFont('Helvetica', '', 12);

        // Write Section A questions
        foreach ($sectionAQuestions as $index => $question) {
            if ($y > 270) {
                $pdf->AddPage(); // Add a new page if needed
                $y = 10; // Reset y-coordinate for the new page
            }

            $pdf->SetXY($x, $y);
            $pdf->Write(10, "Q" . ($index + 1) . ": " . $question);
            $y += 10;
        }

        // Add some space above Section B title
        $y += 5; // Increase space before Section B title

        // Check space for Section B
        if ($y > 260) {
            $pdf->AddPage();
            $y = 10;
        } else {
            // Ensure we have space for the title
            $y += 10;
        }

        // Section B Title
        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->SetXY($x, $y);
        $pdf->Cell(0, 10, 'SECTION B (60 MARKS)', 0, 1, 'C');
        $y += 15;

        $pdf->SetFont('Helvetica', '', 12);

        // Write Section B questions (up to 5 questions)
        foreach ($sectionBQuestions as $index => $question) {
            if ($index >= 5)
                break; // Ensure no more than 5 questions are processed

            $pdf->SetXY($x, $y);
            $pdf->Write(10, "Q" . ($index + 1) . ": " . $question);
            $y += 10;
        }

        // Output the PDF to the browser for download
        $pdf->Output('I', "Exam_$courseUnit.pdf");
    }






    public function generatePdf(Request $request)
    {
        $courseUnit = $request->input('courseUnit');
        $sectionAQuestions = json_decode($request->input('sectionAQuestions'), true);
        $sectionBQuestions = json_decode($request->input('sectionBQuestions'), true);

        return $this->addQuestionsToPdf($sectionAQuestions, $sectionBQuestions, $courseUnit);
    }




}