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
    /**
     * Process images in question HTML: upload base64 images to Firebase Storage and replace src with storage URL.
     */
    private function processQuestionImages($html, $courseUnit, $section, $index)
    {
        // Find all <img> tags
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches);
        $imageSources = $matches[1] ?? [];
        $storage = app('firebase.storage');
        $bucket = $storage->getBucket();

        foreach ($imageSources as $imgSrc) {
            if (strpos($imgSrc, 'data:image') === 0) {
                // Extract mime type and data
                if (preg_match('/data:image\/(.*?);base64,(.*)/', $imgSrc, $imgParts)) {
                    $extension = $imgParts[1] ?? 'png';
                    $data = $imgParts[2];
                    $imageData = base64_decode($data);
                    $filename = 'questions/' . $courseUnit . '_' . $section . '_' . $index . '_' . uniqid() . '.' . $extension;
                    // Upload to Firebase Storage
                    $object = $bucket->upload($imageData, [
                        'name' => $filename
                    ]);
                    // Get public URL (assuming bucket is public or use signedUrl if not)
                    $imageUrl = $object->signedUrl(new \DateTime('+1 year'));
                    // Replace src in HTML
                    $html = str_replace($imgSrc, $imageUrl, $html);
                }
            }
        }
        return $html;
    }

    public function uploadExam(Request $request)
    {
        // ✅ Ensure UTF-8 encoding for incoming data
        mb_internal_encoding('UTF-8');
        
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

            // Get current user information from session
            $uploadedBy = session()->get('user_email') ?? 'unknown@unknown.com';
            $uploadedByName = session()->get('user_firstName') ?? 'Unknown User';
            $uploadedByUid = session()->get('user') ?? 'unknown';

            Log::info("📝 Exam being uploaded by: {$uploadedBy} ({$uploadedByName})");

            // Prepare exam data for Firestore
            $examData = [
                'created_at' => new \DateTime(),
                'courseUnit' => $validatedData['courseUnit'],
                'faculty' => $validatedData['faculty'], // Faculty is now taken from the form
                'format' => $validatedData['format'],
                'sections' => [],
                'sectionA_instructions' => $validatedData['instructions'][1],
                'sectionB_instructions' => $validatedData['instructions'][2],
                'uploaded_by_email' => $uploadedBy,
                'uploaded_by_name' => $uploadedByName,
                'uploaded_by_uid' => $uploadedByUid,
            ];

            // Log before processing sections
            Log::info('🔍 Processing Sections: ', ['Section A' => $validatedData['sectionA'], 'Section B' => $validatedData['sectionB']]);

            // Process and upload images, then store HTML instead of base64
            foreach (['A', 'B'] as $section) {
                $content = $request->input("section$section");

                if (empty($content)) {
                    Log::warning("⚠ Section $section is empty.");
                    continue;
                }

                $processedContent = [];
                foreach ($content as $idx => $questionHtml) {
                    $processedContent[] = $this->processQuestionImages($questionHtml, $validatedData['courseUnit'], $section, $idx);
                }
                $examData['sections'][$section] = $processedContent;
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
                        // Use HTML as-is (no base64 decode)
                        $sections[$section][] = $content;
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
                $count = 3;
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
        $sections = session('sections');
        $facultyOf = $request->input('facultyOf');
        $examPeriod = $request->input('examPeriod');
        $date = $request->input('date');
        $time = $request->input('time');
        $generalInstructions = $request->input('generalInstructions');
        $faculty = session('faculty');
        $code = session('code');
        $program = session('program');
        $yearSem = session('year_sem');
        $sectionAInstructions = session('sectionA_instructions');
        $sectionBInstructions = session('sectionB_instructions');
        $sectionCInstructions = session('sectionC_instructions');
        \Log::info('📄 Generating PDF for Course Unit: ' . $courseUnit);
        // Ensure public/pdf_images/ exists and is clean
        $publicPath = public_path('pdf_images/');
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
            Log::info("📂 Created directory for storing PDF images: {$publicPath}");
        }
        File::cleanDirectory($publicPath);
        Log::info("🗑 Cleared old PDF images from public/pdf_images.");
        $processedSections = [];
        foreach ($sections as $sectionName => $questions) {
            foreach ($questions as $index => $questionContent) {
                $processedHtml = $questionContent;
                preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $processedHtml, $matches);
                $imageUrls = $matches[1] ?? [];
                Log::info("🔗 Found Image URLs:", $imageUrls);
                foreach ($imageUrls as $imageUrl) {
                    $decodedUrl = html_entity_decode($imageUrl);
                    $fileName = 'pdf_' . uniqid() . '.jpg';
                    $publicFilePath = $publicPath . $fileName;
                    $relativePath = 'pdf_images/' . $fileName;
                    try {
                        $imgContent = @file_get_contents($decodedUrl);
                        if ($imgContent !== false) {
                            file_put_contents($publicFilePath, $imgContent);
                            $processedHtml = str_replace($imageUrl, $relativePath, $processedHtml);
                            Log::info("✅ Image replaced: {$imageUrl} -> {$relativePath}");
                        } else {
                            Log::error("❌ Failed to download image: {$decodedUrl} (file_get_contents returned false)");
                        }
                    } catch (\Exception $e) {
                        Log::error("❌ Exception downloading image: {$decodedUrl}, Error: " . $e->getMessage());
                    }
                }
                $processedSections[$sectionName][$index] = $processedHtml;
            }
        }
        $pdf = Pdf::loadView('admin.exam-template', [
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
            'pdf' => true
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        // ✅ Enable UTF-8 support for special characters
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');
        
        \Log::info("📜 Processing completed. Rendering PDF...");
        \Log::info("✅ PDF generated successfully for Course Unit: {$courseUnit}");
        return $pdf->stream("Exam_{$courseUnit}.pdf");
    }



}