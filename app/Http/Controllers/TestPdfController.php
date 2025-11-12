<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class TestPdfController extends Controller
{
    public function showTestPage()
    {
        return view('test-pdf-generator');
    }

    public function generatePdf(Request $request)
    {
        $pdfType = $request->input('pdfType');
        $courseUnit = $request->input('courseUnit');
        
        // Decode sections from JSON
        $sectionAQuestions = json_decode($request->input('sectionA'), true) ?? [];
        $sectionBQuestions = json_decode($request->input('sectionB'), true) ?? [];
        $sectionCQuestions = json_decode($request->input('sectionC'), true) ?? [];
        
        // Build sections array
        $sections = [];
        if (!empty($sectionAQuestions)) {
            $sections['A'] = $sectionAQuestions;
        }
        if (!empty($sectionBQuestions)) {
            $sections['B'] = $sectionBQuestions;
        }
        if (!empty($sectionCQuestions)) {
            $sections['C'] = $sectionCQuestions;
        }
        
        $sectionAInstructions = $request->input('sectionAInstructions');
        $sectionBInstructions = $request->input('sectionBInstructions');
        $sectionCInstructions = $request->input('sectionCInstructions');
        
        Log::info("📄 Generating {$pdfType} PDF for Test Page");
        
        // Ensure public/pdf_images/ exists and is clean
        $publicPath = public_path('pdf_images/');
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
            Log::info("📂 Created directory for storing PDF images: {$publicPath}");
        }
        File::cleanDirectory($publicPath);
        Log::info("🗑 Cleared old PDF images from public/pdf_images.");
        
        // Process images in questions
        $processedSections = $this->processImagesInSections($sections, $publicPath);
        
        if ($pdfType === 'preview') {
            // Generate Preview PDF
            $pdf = Pdf::loadView('lecturer.preview', [
                'courseUnit' => $courseUnit,
                'sections' => $processedSections,
                'sectionAInstructions' => $sectionAInstructions,
                'sectionBInstructions' => $sectionBInstructions,
            ]);
            
            $fileName = "Preview_Test_{$courseUnit}.pdf";
        } else {
            // Generate Exam Template PDF
            $facultyOf = $request->input('facultyOf');
            $examPeriod = $request->input('examPeriod');
            $program = $request->input('program');
            $yearSem = $request->input('yearSem');
            $code = $request->input('code');
            $date = $request->input('date');
            $time = $request->input('time');
            $generalInstructions = $request->input('generalInstructions');
            
            $pdf = Pdf::loadView('admin.exam-template', [
                'sections' => $processedSections,
                'courseUnit' => $courseUnit,
                'facultyOf' => $facultyOf,
                'examPeriod' => $examPeriod,
                'program' => $program,
                'yearSem' => $yearSem,
                'code' => $code,
                'date' => $date,
                'time' => $time,
                'generalInstructions' => $generalInstructions,
                'sectionAInstructions' => $sectionAInstructions,
                'sectionBInstructions' => $sectionBInstructions,
                'sectionCInstructions' => $sectionCInstructions,
                'faculty' => $facultyOf, // For compatibility
                'pdf' => true
            ]);
            
            $fileName = "Exam_Test_{$courseUnit}.pdf";
        }
        
        $pdf->setPaper('A4', 'portrait');
        
        // Enable UTF-8 support for special characters
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');
        
        Log::info("✅ PDF generated successfully: {$fileName}");
        
        return $pdf->stream($fileName);
    }
    
    private function processImagesInSections($sections, $publicPath)
    {
        $processedSections = [];
        
        foreach ($sections as $sectionName => $questions) {
            foreach ($questions as $index => $questionContent) {
                $processedHtml = $questionContent;
                
                // Find all image URLs in the question
                preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $processedHtml, $matches);
                $imageUrls = $matches[1] ?? [];
                
                Log::info("🔗 Found Image URLs in Section {$sectionName}, Question {$index}:", $imageUrls);
                
                foreach ($imageUrls as $imageUrl) {
                    // Skip if it's already a local path
                    if (strpos($imageUrl, 'http') !== 0) {
                        continue;
                    }
                    
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
                            Log::error("❌ Failed to download image: {$decodedUrl}");
                        }
                    } catch (\Exception $e) {
                        Log::error("❌ Exception downloading image: {$decodedUrl}, Error: " . $e->getMessage());
                    }
                }
                
                $processedSections[$sectionName][$index] = $processedHtml;
            }
        }
        
        return $processedSections;
    }
}
