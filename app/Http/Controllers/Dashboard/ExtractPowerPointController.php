<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Shape\AutoShape;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Shape\AutoShapeType;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Slide\Slide;
use PhpOffice\PhpPresentation\Style\Alignment;


use Illuminate\Support\Facades\Response;
use ZipArchive;
use DOMDocument;
use Illuminate\Support\Facades\File;
///////////////////////////////////////////////////
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class ExtractPowerPointController extends ApiController{
    public function extractPowerPoint(){
        if (ob_get_length()) ob_end_clean();
        $ppt = new PhpPresentation();
        
        // Remove default slide
        $ppt->removeSlideByIndex(0);

        // Slide 1 - Title Slide
        $slide1 = $ppt->createSlide();
        $title1 = $slide1->createRichTextShape()
            ->setHeight(100)
            ->setWidth(600)
            ->setOffsetX(50)
            ->setOffsetY(50);
        $title1->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun1 = $title1->createTextRun('Welcome to Laravel PowerPoint');
        $textRun1->getFont()->setBold(true)->setSize(28)->setColor(new Color('FF0000'));

        // Slide 2 - Content Slide
        $slide2 = $ppt->createSlide();
        $title2 = $slide2->createRichTextShape()
            ->setHeight(50)
            ->setWidth(600)
            ->setOffsetX(50)
            ->setOffsetY(50);
        $title2->createTextRun('Key Features')->getFont()->setBold(true)->setSize(24);
        
        $content = $slide2->createRichTextShape()
            ->setHeight(300)
            ->setWidth(600)
            ->setOffsetX(50)
            ->setOffsetY(100);
        $content->createTextRun("• Easy to use\n• Flexible template system\n• Laravel integration\n• Export to PPTX/PDF");

        // Slide 3 - Image Slide
        $slide3 = $ppt->createSlide();
        $title3 = $slide3->createRichTextShape()
            ->setHeight(50)
            ->setWidth(600)
            ->setOffsetX(50)
            ->setOffsetY(50);
        $title3->createTextRun('Laravel Logo')->getFont()->setBold(true)->setSize(24);
        
        // Add an image (make sure the path is correct)
        $imagePath = public_path('images/laravel-logo.png');
        if (file_exists($imagePath)) {
            $shape = $slide3->createDrawingShape();
            $shape->setName('Laravel Logo')
                  ->setPath($imagePath)
                  ->setWidth(200)
                  ->setOffsetX(200)
                  ->setOffsetY(100);
        }

        // Save to file
        $fileName = 'presentation_' . time() . '.pptx';
        $filePath = public_path('temp/' . $fileName);
        if (!file_exists(public_path('temp'))) {
            mkdir(public_path('temp'), 0777, true);
        }

        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $writer->save($filePath);

        // Return the file as a download response
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function extractPowerPoint2()
    {
        // if (ob_get_level() > 0) { // Check if output buffering is active
        //     while (ob_get_level()) {
        //         ob_end_clean();
        //     }
        // }

        $presentation = new PhpPresentation();

    // Remove the default slide and create a clean one
    $slides = $presentation->getAllSlides();
    foreach ($slides as $slide) {
        $presentation->removeSlideByIndex(0);
    }

    // Add a clean slide
    $presentation->createSlide();

    // File path
    $fileName = 'empty_presentation.pptx';
    $filePath = public_path("temp/{$fileName}");

    // Ensure the temp directory exists
    if (!file_exists(public_path('temp'))) {
        mkdir(public_path('temp'), 0777, true);
    }

    // Save as PowerPoint 2007 format
    $writer = IOFactory::createWriter($presentation, 'PowerPoint2007');
    $writer->save($filePath);
        // 4. Write presentation
        // try {
        //     $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        //     $writer->save($filePath);
        // } catch (\Exception $e) {
        //     dd("d");
        //     // Handle error during PPTX generation/saving
        //     // Log $e->getMessage()
        //     return response('Error generating PowerPoint file: ' . $e->getMessage(), 500);
        // }

        // // 5. Check if file was actually created
        // if (!file_exists($filePath)) {
        //     dd('dd');
        //     return response('Error: PowerPoint file was not created on server.', 500);
        // }

        // 6. Prepare headers for download
        // $headers = [
        //     'Content-Type'        => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        //     'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0', // More standard for downloads
        //     'Pragma'              => 'public', // Often used with Cache-Control for IE compatibility
        //     'Expires'             => '0', // Prevents caching
            
        // ];

       
        // The deleteFileAfterSend(true) will delete the file from the server after it's sent.
       // return response()->download($filePath, $fileName);
            // Important: no output before this!
    }
}