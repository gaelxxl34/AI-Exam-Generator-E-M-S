<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
class UploadExamsController extends Controller
{
    public function uploadExam(Request $request)
    {
        Log::info('uploadExam method called');

        // Log incoming request data (excluding sensitive fields)
        Log::info('Incoming Request Data:', $request->except('_token'));

        // Validation for all fields
        try {
            $validatedData = $request->validate([
                'courseUnit' => 'required|string',
                'faculty' => 'required|string', // Faculty must be submitted in the form
                'format' => 'required|string',
                'sectionA' => 'required|array|min:1',
                'sectionA.*' => 'required|string',
                'sectionB' => 'required|array|min:1',
                'sectionB.*' => 'required|string',
                'instructions.1' => 'required|string',
                'instructions.2' => 'required|string',
            ]);

            Log::info('✅ Validation passed.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation failed:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            $firestore = app('firebase.firestore')->database();
            $examsRef = $firestore->collection('Exams');

            // Check if an exam with the same courseUnit already exists
            $existingExamQuery = $examsRef->where('courseUnit', '==', $validatedData['courseUnit']);
            $existingExamSnapshots = $existingExamQuery->documents();

            if (!$existingExamSnapshots->isEmpty()) {
                Log::warning('⚠ Exam already exists for course unit: ' . $validatedData['courseUnit']);
                return back()->with('error', 'An exam with this course unit already exists. Please review the existing exam.');
            }

            Log::info('🆕 Creating new exam entry.');

            // Prepare exam data for Firestore
            $examData = [
                'created_at' => new \DateTime(),
                'courseUnit' => $validatedData['courseUnit'],
                'faculty' => $validatedData['faculty'], // Faculty is now taken from the form
                'format' => $validatedData['format'],
                'sections' => [],
                'sectionA_instructions' => $validatedData['instructions'][1],
                'sectionB_instructions' => $validatedData['instructions'][2],
            ];

            // Log before processing sections
            Log::info('🔍 Processing Sections: ', ['Section A' => $validatedData['sectionA'], 'Section B' => $validatedData['sectionB']]);

            // Base64 encode sections before storing
            foreach (['A', 'B'] as $section) {
                $content = $request->input("section$section");

                if (empty($content)) {
                    Log::warning("⚠ Section $section is empty.");
                    continue;
                }

                $encodedContent = array_map(function ($question) {
                    return base64_encode($question);
                }, $content);

                $examData['sections'][$section] = $encodedContent;
            }

            Log::info('✅ Section content processed.');

            // Save exam data to Firestore
            $examsRef->add($examData);

            Log::info('🎉 Exam successfully uploaded.');

            return redirect()->route('lecturer.l-dashboard')->with('success', 'Exam uploaded successfully.');
        } catch (\Throwable $e) {
            Log::error("❌ Error uploading exam: " . $e->getMessage());
            return back()->withErrors(['upload_error' => 'Error uploading exam.'])->with('message', 'Error uploading exam: ' . $e->getMessage());
        }
    }


// add comment and also add statistics for reports 
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
                break;
            }
        }

        if ($courseData === null) {
            \Log::error("No existing course found with the name: $selectedCourse");
            throw new \Exception("No existing course found.");
        }

        $code = $courseData['code'] ?? 'default_code';
        $program = $courseData['program'] ?? 'default_program';
        $year_sem = $courseData['year_sem'] ?? 'default_year_sem';
        $faculty = $courseData['faculty'] ?? 'default_faculty';

        \Log::info("Course details fetched: Code: $code, Program: $program, Year/Sem: $year_sem, Faculty: $faculty");

        $sectionAInstructions = '';
        $sectionBInstructions = '';

        $examsQuery = $firestore->collection('Exams')->where('courseUnit', '==', $selectedCourse);
        $examsSnapshot = $examsQuery->documents();

        $sections = [];

        foreach ($examsSnapshot as $exam) {
            if ($exam->exists()) {
                $data = $exam->data();

                $sectionAInstructions = $data['sectionA_instructions'] ?? '';
                $sectionBInstructions = $data['sectionB_instructions'] ?? '';

                foreach ($data['sections'] as $section => $contents) {
                    if (!isset($sections[$section])) {
                        $sections[$section] = [];
                    }

                    foreach ($contents as $index => $content) {
                        // Decode base64 content and convert to HTML entities
                        $decodedContent = mb_convert_encoding(base64_decode($content), 'HTML-ENTITIES', 'UTF-8');

                        // Load content into DOMDocument with UTF-8 encoding declaration
                        libxml_use_internal_errors(true);
                        $doc = new \DOMDocument();
                        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $decodedContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                        libxml_clear_errors();

                        // Save cleaned HTML
                        $sections[$section][] = $doc->saveHTML();
                    }
                }
            }
        }

        // Shuffle and trim questions by faculty rules
        foreach ($sections as $section => $questions) {
            shuffle($questions);

            if ($faculty == 'FST' || $faculty == 'FBM') {
                $count = ($section == 'A') ? 1 : 6;
            } elseif ($faculty == 'FOE') {
                $count = 4;
            } elseif ($faculty == 'HEC') {
                $count = ($section == 'A') ? 10 : 6;
            } elseif ($faculty == 'FOL') {
                if ($section == 'A') {
                    $count = 1;
                } elseif ($section == 'B') {
                    $count = 2;
                } else {
                    $count = 4;
                }
            } else {
                $count = ($section == 'A') ? 4 : 6;
            }

            $sections[$section] = array_slice($questions, 0, $count);
        }

        // Save to session
        session([
            'sections' => $sections,
            'sectionA_instructions' => $sectionAInstructions,
            'sectionB_instructions' => $sectionBInstructions,
            'sectionC_instructions' => $data['sectionC_instructions'] ?? '',
            'faculty' => $faculty,
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

        // Retrieve additional session data
        $faculty = session('faculty');
        $code = session('code');
        $program = session('program');
        $yearSem = session('year_sem');
        $sectionAInstructions = session('sectionA_instructions');
        $sectionBInstructions = session('sectionB_instructions');
        $sectionCInstructions = session('sectionC_instructions');

        \Log::info('📄 Generating PDF for Course Unit: ' . $courseUnit);

        // Process image replacement
        $processedSections = [];
        $storedImages = [];

        foreach ($sections as $sectionName => $questions) {
            foreach ($questions as $index => $questionContent) {
                // // Log question content
                // \Log::info("🔍 Processing Question Content - Section: {$sectionName}, Index: {$index}");
                // \Log::info($questionContent);

                // Extract images from HTML content
                preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $questionContent, $matches);
                $imageUrls = $matches[1] ?? [];

                \Log::info("🔗 Found Image URLs: " . json_encode($imageUrls));

                foreach ($imageUrls as $imageUrl) {
                    // Generate local file path
                    $imageExtension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                    $imageFileName = uniqid() . '.' . $imageExtension;
                    $localPath = storage_path("app/pdf_images/{$imageFileName}");

                    // Check if the image is already downloaded
                    if (!isset($storedImages[$imageUrl])) {
                        try {
                            \Log::info("📥 Downloading image: {$imageUrl}");
                            $imageContents = file_get_contents($imageUrl);
                            file_put_contents($localPath, $imageContents);
                            $storedImages[$imageUrl] = $localPath;
                        } catch (\Exception $e) {
                            \Log::error("❌ Failed to download image: {$imageUrl}, Error: " . $e->getMessage());
                        }
                    }

                    // Replace Firebase URL with local path
                    if (isset($storedImages[$imageUrl])) {
                        $questionContent = str_replace($imageUrl, $storedImages[$imageUrl], $questionContent);
                        \Log::info("✅ Image replaced: {$imageUrl} -> {$storedImages[$imageUrl]}");
                    }
                }

                // Store updated question content
                $processedSections[$sectionName][$index] = $questionContent;
            }
        }

        // Generate PDF
        $pdf = PDF::loadView('admin.exam-template', [
            'sections' => $processedSections,
            'courseUnit' => $courseUnit,
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
            'sectionCInstructions' => $sectionCInstructions,
            'pdf' => true // ✅ Ensure this flag is passed!
        ]);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->set_option('isPhpEnabled', true); // ✅ enables PHP in scripts
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);

        // Enable remote images
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

        \Log::info("📜 Processing completed. Rendering PDF...");

        \Log::info("✅ PDF generated successfully for Course Unit: {$courseUnit}");

        return $pdf->stream("Exam_{$courseUnit}.pdf");
    }



}