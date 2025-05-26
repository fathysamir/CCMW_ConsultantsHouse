<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
// use PhpOffice\PhpPresentation\Shape\Drawing\Line;
use PhpOffice\PhpPresentation\Shape\AutoShape;
use PhpOffice\PhpPresentation\Shape\AutoShape\Type;
use PhpOffice\PhpPresentation\Shape\Line;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Color;
// use PhpOffice\PhpPresentation\Shape\AutoShape\ShapeType;

use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Outline;

// /////////////////////////////////////////////////

class ExtractPowerPointController extends ApiController
{
    public function uuu()
    {

        error_reporting(0);
        ob_start();
        ob_clean();
        if (ob_get_length()) {
            ob_end_clean();
        }
        $ppt = new PhpPresentation;
        $slide = $ppt->getActiveSlide();

        // Define timeline months (simplified)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        $startX = 10;
        $startY = 10;
        $cellWidth = 80;

        // Draw yellow month cells
        foreach ($months as $index => $month) {
            $shape = $slide->createRichTextShape()
                ->setHeight(40)
                ->setWidth($cellWidth)
                ->setOffsetX($startX + $index * $cellWidth)
                ->setOffsetY($startY);
            $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
            $shape->getBorder()->setLineWidth(1)->setColor(new Color(Color::COLOR_BLACK));
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $shape->createTextRun($month)->getFont()->setBold(true);
            // dd($shape);
        }
        $rect = new AutoShape;
        $rect->setType(AutoShape::TYPE_DIAMOND);
        $rect->setOffsetX(100)->setOffsetY(100)->setWidth(200)->setHeight(100);
        $rect->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFFF00'));
        $outline = $rect->getOutline();
        $outline->setWidth(3); // Border thickness
        $outline->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('000000')); // Black border
        $slide->addShape($rect);

        // Shape 2
        $frame = new AutoShape;
        $frame->setType(AutoShape::TYPE_RECTANGLE);
        $frame->setOffsetX(100)->setOffsetY(250)->setWidth(200)->setHeight(100); // moved down
        $frame->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF0000')); // red
        $outline = $frame->getOutline();
        $outline->getFill() // The outline's fill defines its color
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('00F200')); // Black border
        $outline->setWidth(2); // Border width in pixels (e.g., 2px)
        $slide->addShape($frame);

        $dd = new AutoShape;
        $dd->setType(AutoShape::TYPE_ROUNDED_RECTANGLE);
        $dd->setOffsetX(100)->setOffsetY(400)->setWidth(200)->setHeight(100); // moved down
        $dd->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF1499')); // red
        // $dd->getOutline()->setWidth(3)->setColor(new Color('000000'));; // black
        $slide->addShape($dd);

        $line = new \PhpOffice\PhpPresentation\Shape\Line(150, 100, 150, 200); // from (x1, y1) to (x2, y2)
        $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
        $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
        $line->getBorder()->setLineWidth(1);
        $line->getBorder()->setColor(new Color('FF0000')); // red
        $slide->addShape($line);

        $slide2 = $ppt->createSlide();
        $line2 = new \PhpOffice\PhpPresentation\Shape\Line(150, 100, 150, 200);

        // Set line style
        $line2->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
        $line2->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_SOLID); // Solid line (optional)
        $line2->getBorder()->setLineWidth(1); // Line thickness
        $line2->getBorder()->setColor(new Color('FF0000')); // red

        // Add an arrow at the end (head)
        $line2->getHeadArrow()->setLength(\PhpOffice\PhpPresentation\Style\Arrow::LENGTH_MEDIUM);
        $line2->getHeadArrow()->setWidth(\PhpOffice\PhpPresentation\Style\Arrow::WIDTH_MEDIUM);
        $line2->getHeadArrow()->setStyle(\PhpOffice\PhpPresentation\Style\Arrow::STYLE_TRIANGLE);
        $slide2->addShape($line2);

        $fileName = 'presentation_'.time().'.pptx';
        $filePath = public_path('temp/'.$fileName);
        if (! file_exists(public_path('temp'))) {
            mkdir(public_path('temp'), 0777, true);
        }
        ob_clean();
        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $writer->save($filePath);
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0,no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        // Return the file as a download response
        return response()->download($filePath, null, $headers)->deleteFileAfterSend(false);
    }

    // public function extractPowerPoint(){
    //     if (ob_get_length()) ob_end_clean();
    //     $ppt = new PhpPresentation();
    //     $ppt->getDocumentProperties()->setCreator('YourAppName')
    //                                     ->setLastModifiedBy('YourAppName')
    //                                     ->setTitle('My Presentation')
    //                                     ->setSubject('Presentation Subject')
    //                                     ->setDescription('Presentation generated by YourAppName.')
    //                                     ->setKeywords('office phppresentation php')
    //                                     ->setCategory('Presentations');

    //     // Remove default slide
    //     $ppt->removeSlideByIndex(0);

    //     // Slide 1 - Title Slide
    //     $slide1 = $ppt->createSlide();
    //     $title1 = $slide1->createRichTextShape()
    //         ->setHeight(100)
    //         ->setWidth(600)
    //         ->setOffsetX(50)
    //         ->setOffsetY(50);
    //     $title1->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $textRun1 = $title1->createTextRun('Welcome to Laravel PowerPoint');
    //     $textRun1->getFont()->setBold(true)->setSize(28)->setColor(new Color('FF0000'));

    //     // Slide 2 - Content Slide
    //     $slide2 = $ppt->createSlide();
    //     $title2 = $slide2->createRichTextShape()
    //         ->setHeight(50)
    //         ->setWidth(600)
    //         ->setOffsetX(50)
    //         ->setOffsetY(50);
    //     $title2->createTextRun('Key Features')->getFont()->setBold(true)->setSize(24);

    //     $content = $slide2->createRichTextShape()
    //         ->setHeight(300)
    //         ->setWidth(600)
    //         ->setOffsetX(50)
    //         ->setOffsetY(100);
    //     $content->createTextRun("• Easy to use\n• Flexible template system\n• Laravel integration\n• Export to PPTX/PDF");

    //     // Slide 3 - Image Slide
    //     $slide3 = $ppt->createSlide();
    //     $title3 = $slide3->createRichTextShape()
    //         ->setHeight(50)
    //         ->setWidth(600)
    //         ->setOffsetX(50)
    //         ->setOffsetY(50);
    //     $title3->createTextRun('Laravel Logo')->getFont()->setBold(true)->setSize(24);

    //     // Save to file
    //     $fileName = 'presentation_' . time() . '.pptx';
    //     $filePath = public_path('temp/' . $fileName);
    //     if (!file_exists(public_path('temp'))) {
    //         mkdir(public_path('temp'), 0777, true);
    //     }

    //     $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
    //     $writer->save($filePath);
    //     $headers = [
    //                     'Content-Type'        => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    //                     'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    //                     'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
    //                     'Pragma'              => 'public',
    //                     'Expires'             => '0',
    //                 ];
    //     // Return the file as a download response
    //     return response()->download($filePath)->deleteFileAfterSend(true);
    // }

    // public function extractPowerPoint2()
    // {
    //     // Clear output buffers
    //     if (ob_get_length()) ob_end_clean();

    //     // Create new presentation
    //     $ppt = new \PhpOffice\PhpPresentation\PhpPresentation();

    //     // Set document properties
    //     $ppt->getDocumentProperties()
    //         ->setCreator('YourAppName')
    //         ->setLastModifiedBy('YourAppName')
    //         ->setTitle('My Presentation')
    //         ->setSubject('Presentation Subject')
    //         ->setDescription('Presentation generated by YourAppName.')
    //         ->setKeywords('office phppresentation php')
    //         ->setCategory('Presentations');

    //     // Remove default slide (index 0)
    //     $ppt->removeSlideByIndex(0);

    //     // Slide 1 - Title Slide
    //     $slide1 = $ppt->createSlide();
    //     $shape1 = $slide1->createRichTextShape()
    //         ->setHeight(100)
    //         ->setWidth(600)
    //         ->setOffsetX(50)
    //         ->setOffsetY(50);

    //     // Add text and style it
    //     $textRun1 = $shape1->createTextRun('Welcome to Laravel PowerPoint');
    //     $textRun1->getFont()
    //         ->setBold(true)
    //         ->setSize(28)
    //         ->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF0000'));

    //     // Center align the text
    //     $shape1->getActiveParagraph()->getAlignment()
    //         ->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);

    //     // Slide 2 - Content Slide
    //     $slide2 = $ppt->createSlide();
    //     $shape2 = $slide2->createRichTextShape()
    //         ->setHeight(50)
    //         ->setWidth(600)
    //         ->setOffsetX(50)
    //         ->setOffsetY(50);

    //     $textRun2 = $shape2->createTextRun('Key Features');
    //     $textRun2->getFont()
    //         ->setBold(true)
    //         ->setSize(24);

    //     $contentShape = $slide2->createRichTextShape()
    //         ->setHeight(300)
    //         ->setWidth(600)
    //         ->setOffsetX(50)
    //         ->setOffsetY(100);

    //     $contentText = $contentShape->createTextRun("• Easy to use\n• Flexible template system\n• Laravel integration\n• Export to PPTX/PDF");

    //     // Ensure temp directory exists
    //     $tempDir = public_path('temp');
    //     if (!file_exists($tempDir)) {
    //         mkdir($tempDir, 0777, true);
    //     }

    //     // Generate filename and path
    //     $fileName = 'presentation_' . time() . '.pptx';
    //     $filePath = $tempDir . DIRECTORY_SEPARATOR . $fileName;

    //     // Save the presentation
    //     $writer = \PhpOffice\PhpPresentation\IOFactory::createWriter($ppt, 'PowerPoint2007');
    //     $writer->save($filePath);

    //     // Verify file was created
    //     if (!file_exists($filePath) || filesize($filePath) === 0) {
    //         throw new \Exception("Failed to generate PowerPoint file");
    //     }

    //     // Return download response
    //     return response()->download(
    //         $filePath,
    //         $fileName,
    //         [
    //             'Content-Type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    //             'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    //             'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
    //             'Pragma' => 'public',
    //             'Expires' => '0',
    //         ]
    //     )->deleteFileAfterSend(true);
    // }
}
