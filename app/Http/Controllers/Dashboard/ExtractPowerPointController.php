<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\FileDocument;
use App\Models\GanttChartDocData;
use App\Models\ProjectFile;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
// use PhpOffice\PhpPresentation\Shape\Drawing\Line;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\AutoShape;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
// use PhpOffice\PhpPresentation\Shape\AutoShape\ShapeType;
use PhpOffice\PhpPresentation\Style\Fill;

// /////////////////////////////////////////////////

class ExtractPowerPointController extends ApiController
{
    public function uuu(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }

        $request->validate([
            'start_date' => 'nullable|date_format:Y-m',
            'end_date'   => 'nullable|date_format:Y-m|after_or_equal:start_date',
        ]);

        error_reporting(0);
        ob_start();
        ob_clean();
        if (ob_get_length()) {
            ob_end_clean();
        }
        $file = ProjectFile::where('slug', $request->file_slug)->first();
        if ($request->timeframe == 'fixed_dates') {
            $startTimeScale = $request->start_date;
            $endTimeScale   = $request->end_date;
            $lastDay        = Carbon::createFromFormat('Y-m', $endTimeScale)->endOfMonth()->day;
            $start          = Carbon::createFromFormat('Y-m-d', $startTimeScale . '-01');
            $end            = Carbon::createFromFormat('Y-m-d', $endTimeScale . '-' . $lastDay);
        } elseif ($request->timeframe == 'auto') {
            $docs = FileDocument::where('file_id', $file->id)
                ->where('forChart', '1')
                ->has('gantt_chart')
                ->get()
                ->sortBy([
                    fn($a, $b) => ($a->document->start_date ?? $a->note->start_date ?? '9999-12-31')
                    <=> ($b->document->start_date ?? $b->note->start_date ?? '9999-12-31'),
                    fn($a, $b) => $a->sn <=> $b->sn,
                ])
                ->values();

            // Get all start and end dates
            $startDates = $docs->map(function ($item) {
                if ($item->document) {
                    return $item->document->start_date;
                } elseif ($item->note) {
                    return $item->note->start_date;
                }
                return null;
            })->filter();

            $endDates = $docs->map(function ($item) {
                if ($item->document) {
                    return $item->document->end_date ?? $item->document->start_date;
                } elseif ($item->note) {
                    return $item->note->end_date ?? $item->note->start_date;
                }
                return null;
            })->filter();

            // Find min and max
            $minStartDate = $startDates->min();
            $minStartDate = date('Y-m', strtotime($minStartDate));

            $maxEndDate = $endDates->max();
            $maxEndDate = date('Y-m', strtotime($maxEndDate));

            $lastDay        = Carbon::createFromFormat('Y-m', $maxEndDate)->endOfMonth()->day;
            $start          = Carbon::createFromFormat('Y-m-d', $minStartDate . '-01');
            $end            = Carbon::createFromFormat('Y-m-d', $maxEndDate . '-' . $lastDay);
            $monthsDiff     = $start->diffInMonths($end);
            $extraMonths    = (int) ceil($monthsDiff * 0.3);
            $start          = $start->copy()->subMonths($extraMonths);
            $end            = $end->copy()->addMonths($extraMonths);
            $startTimeScale = $start->format('Y-m');
            $endTimeScale   = $end->format('Y-m');

        }

        $array_years  = [];
        $array_months = []; // Will store all months in order
        $count_days   = 0;

        $pointer = $start->copy();

        while ($pointer <= $end) {

            $year         = $pointer->format('Y');
            $month        = $pointer->format('M');   // e.g., Jan, Feb, Mar...
            $monthYearKey = $pointer->format('M Y'); // e.g., "Oct 2016"

            $daysInMonth = $pointer->daysInMonth;

            // Add month to $array_months (in order)
            $array_months[$monthYearKey] = $daysInMonth;

            // Add days to year count
            if (! isset($array_years[$year])) {
                $array_years[$year] = 0;
            }
            $array_years[$year] += $daysInMonth;

            // Add to total days count
            $count_days += $daysInMonth;
            $pointer->addMonth();

        }

        $base_day_width = 1; // pts per day
        $padding        = 40;

        $customWidth = ($count_days * $base_day_width) + $padding < 1470 ? 1470 : ($count_days * $base_day_width) + $padding;
        $day_width   = ($customWidth - $padding) / $count_days;
        $day_width   = round($day_width, 5);

        $ppt = new PhpPresentation;
        $ppt->getLayout()->setCX((int) $customWidth, DocumentLayout::UNIT_PIXEL);
        $ppt->getLayout()->setCY(900, DocumentLayout::UNIT_PIXEL);
        $slide = $ppt->getActiveSlide();
        if ($request->title_status == 'up') {
            $shape2 = $slide->createRichTextShape()
                ->setHeight(30)
                ->setWidth($customWidth - $padding)
                ->setOffsetX(20)
                ->setOffsetY(10);
            $shape2->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_NONE);
            $shape2->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
            $shape2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape2->createTextRun($file->name)->getFont()->setSize(intval($request->title_font_size))->setBold(true)->setName($request->font_type);

            $currentY = 40;
            $currentX = 20; // float for precision

            foreach ($array_years as $year => $countDays) {
                $w = $day_width * $countDays;

                $shape = $slide->createRichTextShape()
                    ->setHeight(20)
                    ->setWidth($w)
                    ->setOffsetX($currentX)
                    ->setOffsetY($currentY);

                $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $shape->createTextRun($year)->getFont()->setSize(9)->setBold(false)->setName($request->font_type);

                $currentX += $w; // Keep float accuracy here
            }
            $currentY   = $currentY + 20.7;
            $currentX   = 20;
            $first_line = false;

            foreach ($array_months as $month => $daysInMonth) {
                $w = $day_width * $daysInMonth;

                // Add left line only once at start
                if (! $first_line) {
                    $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX, 2), (int) $currentY + 16, (int) round($currentX, 2), 890);
                    $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                    $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                    $line->getBorder()->setLineWidth(0.75);
                    $line->getBorder()->setColor(new Color('FFa9a9a9'));
                    $slide->addShape($line);
                    $first_line = true;
                }

                // Draw month box
                $shape = $slide->createRichTextShape()
                    ->setHeight(16)
                    ->setWidth(round($w, 2))
                    ->setOffsetX(round($currentX, 2))
                    ->setOffsetY($currentY);
                $shape->setInsetRight(0.0);
                $shape->setInsetLeft(0.0);
                $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $shape->createTextRun(substr($month, 0, 3))->getFont()->setSize(4)->setBold(false)->setName($request->font_type);

                // Right line for this month
                $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX + $w, 2), (int) $currentY + 16, (int) round($currentX + $w, 2), 890);
                $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                $line->getBorder()->setLineWidth(0.75);
                $line->getBorder()->setColor(new Color('FFa9a9a9'));
                $slide->addShape($line);

                $currentX += $w; // float to avoid error accumulation
            }

            $currentY = 87;
        } elseif ($request->title_status == 'down') {

            $currentY = 10;
            $currentX = 20; // float for precision
            foreach ($array_years as $year => $countDays) {
                $w = $day_width * $countDays;

                $shape = $slide->createRichTextShape()
                    ->setHeight(20)
                    ->setWidth($w)
                    ->setOffsetX($currentX)
                    ->setOffsetY($currentY);

                $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $shape->createTextRun($year)->getFont()->setSize(9)->setBold(false)->setName($request->font_type);

                $currentX += $w; // Keep float accuracy here
            }
            $currentY   = $currentY + 20.7;
            $currentX   = 20;
            $first_line = false;

            foreach ($array_months as $month => $daysInMonth) {
                $w = $day_width * $daysInMonth;

                // Add left line only once at start
                if (! $first_line) {
                    $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX, 2), (int) $currentY + 16, (int) round($currentX, 2), 890);
                    $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                    $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                    $line->getBorder()->setLineWidth(0.75);
                    $line->getBorder()->setColor(new Color('FFa9a9a9'));
                    $slide->addShape($line);
                    $first_line = true;
                }

                // Draw month box
                $shape = $slide->createRichTextShape()
                    ->setHeight(16)
                    ->setWidth(round($w, 2))
                    ->setOffsetX(round($currentX, 2))
                    ->setOffsetY($currentY);
                $shape->setInsetRight(0.0);
                $shape->setInsetLeft(0.0);
                $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $shape->createTextRun(substr($month, 0, 3))->getFont()->setSize(4)->setBold(false)->setName($request->font_type);

                // Right line for this month
                $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX + $w, 2), (int) $currentY + 16, (int) round($currentX + $w, 2), 890);
                $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                $line->getBorder()->setLineWidth(0.75);
                $line->getBorder()->setColor(new Color('FFa9a9a9'));
                $slide->addShape($line);

                $currentX += $w; // float to avoid error accumulation
            }

            $shape2 = $slide->createRichTextShape()
                ->setHeight(30)
                ->setWidth(($customWidth - $padding) - 3)
                ->setOffsetX(22)
                ->setOffsetY(47);
            $shape2->setInsetRight(0.0);
            $shape2->setInsetLeft(0.0);
            $shape2->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_NONE);
            $shape2->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
            $shape2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape2->createTextRun($file->name)->getFont()->setSize(intval($request->title_font_size))->setBold(true)->setName($request->font_type);

            $currentY = 87;
        } elseif ($request->title_status == 'non') {
            $currentY = 10;
            $currentX = 20; // float for precision

            foreach ($array_years as $year => $countDays) {
                $w = $day_width * $countDays;

                $shape = $slide->createRichTextShape()
                    ->setHeight(20)
                    ->setWidth($w)
                    ->setOffsetX($currentX)
                    ->setOffsetY($currentY);

                $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $shape->createTextRun($year)->getFont()->setSize(9)->setBold(false)->setName($request->font_type);

                $currentX += $w; // Keep float accuracy here
            }
            $currentY   = $currentY + 20.7;
            $currentX   = 20;
            $first_line = false;

            foreach ($array_months as $month => $daysInMonth) {
                $w = $day_width * $daysInMonth;

                // Add left line only once at start
                if (! $first_line) {
                    $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX, 2), (int) $currentY + 16, (int) round($currentX, 2), 890);
                    $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                    $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                    $line->getBorder()->setLineWidth(0.75);
                    $line->getBorder()->setColor(new Color('FFa9a9a9'));
                    $slide->addShape($line);
                    $first_line = true;
                }

                // Draw month box
                $shape = $slide->createRichTextShape()
                    ->setHeight(16)
                    ->setWidth(round($w, 2))
                    ->setOffsetX(round($currentX, 2))
                    ->setOffsetY($currentY);
                $shape->setInsetRight(0.0);
                $shape->setInsetLeft(0.0);
                $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $shape->createTextRun(substr($month, 0, 3))->getFont()->setSize(4)->setBold(false)->setName($request->font_type);

                // Right line for this month
                $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX + $w, 2), (int) $currentY + 16, (int) round($currentX + $w, 2), 890);
                $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                $line->getBorder()->setLineWidth(0.75);
                $line->getBorder()->setColor(new Color('FFa9a9a9'));
                $slide->addShape($line);

                $currentX += $w; // float to avoid error accumulation
            }
            $currentY = 57;
        }

        $currentX = 20;

        $docs = FileDocument::where('file_id', $file->id)->where('forChart', '1')
            ->get()
            ->sortBy([
                fn($a, $b) => ($a->document->start_date ?? $a->note->start_date ?? '9999-12-31')
                <=> ($b->document->start_date ?? $b->note->start_date ?? '9999-12-31'),
                fn($a, $b) => $a->sn <=> $b->sn,
            ])
            ->values();
        foreach ($docs as $doc) {
            if ($currentY + 36 > 900) {
                $slide = $ppt->createSlide();
                if ($request->title_status == 'up') {
                    $shape2 = $slide->createRichTextShape()
                        ->setHeight(30)
                        ->setWidth($customWidth - $padding)
                        ->setOffsetX(20)
                        ->setOffsetY(10);
                    $shape2->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_NONE);
                    $shape2->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                    $shape2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $shape2->createTextRun($file->name)->getFont()->setSize(intval($request->title_font_size))->setBold(true)->setName($request->font_type);

                    $currentY = 40;
                    $currentX = 20; // float for precision

                    foreach ($array_years as $year => $countDays) {
                        $w = $day_width * $countDays;

                        $shape = $slide->createRichTextShape()
                            ->setHeight(20)
                            ->setWidth($w)
                            ->setOffsetX($currentX)
                            ->setOffsetY($currentY);

                        $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                        $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $shape->createTextRun($year)->getFont()->setSize(9)->setBold(false)->setName($request->font_type);

                        $currentX += $w; // Keep float accuracy here
                    }
                    $currentY   = $currentY + 20.7;
                    $currentX   = 20;
                    $first_line = false;

                    foreach ($array_months as $month => $daysInMonth) {
                        $w = $day_width * $daysInMonth;

                        // Add left line only once at start
                        if (! $first_line) {
                            $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX, 2), (int) $currentY + 16, (int) round($currentX, 2), 890);
                            $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                            $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                            $line->getBorder()->setLineWidth(0.75);
                            $line->getBorder()->setColor(new Color('FFa9a9a9'));
                            $slide->addShape($line);
                            $first_line = true;
                        }

                        // Draw month box
                        $shape = $slide->createRichTextShape()
                            ->setHeight(16)
                            ->setWidth(round($w, 2))
                            ->setOffsetX(round($currentX, 2))
                            ->setOffsetY($currentY);
                        $shape->setInsetRight(0.0);
                        $shape->setInsetLeft(0.0);
                        $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                        $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $shape->createTextRun(substr($month, 0, 3))->getFont()->setSize(4)->setBold(false)->setName($request->font_type);

                        // Right line for this month
                        $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX + $w, 2), (int) $currentY + 16, (int) round($currentX + $w, 2), 890);
                        $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                        $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                        $line->getBorder()->setLineWidth(0.75);
                        $line->getBorder()->setColor(new Color('FFa9a9a9'));
                        $slide->addShape($line);

                        $currentX += $w; // float to avoid error accumulation
                    }

                    $currentY = 87;
                } elseif ($request->title_status == 'down') {

                    $currentY = 10;
                    $currentX = 20; // float for precision
                    foreach ($array_years as $year => $countDays) {
                        $w = $day_width * $countDays;

                        $shape = $slide->createRichTextShape()
                            ->setHeight(20)
                            ->setWidth($w)
                            ->setOffsetX($currentX)
                            ->setOffsetY($currentY);

                        $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                        $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $shape->createTextRun($year)->getFont()->setSize(9)->setBold(false)->setName($request->font_type);

                        $currentX += $w; // Keep float accuracy here
                    }
                    $currentY   = $currentY + 20.7;
                    $currentX   = 20;
                    $first_line = false;

                    foreach ($array_months as $month => $daysInMonth) {
                        $w = $day_width * $daysInMonth;

                        // Add left line only once at start
                        if (! $first_line) {
                            $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX, 2), (int) $currentY + 16, (int) round($currentX, 2), 890);
                            $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                            $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                            $line->getBorder()->setLineWidth(0.75);
                            $line->getBorder()->setColor(new Color('FFa9a9a9'));
                            $slide->addShape($line);
                            $first_line = true;
                        }

                        // Draw month box
                        $shape = $slide->createRichTextShape()
                            ->setHeight(16)
                            ->setWidth(round($w, 2))
                            ->setOffsetX(round($currentX, 2))
                            ->setOffsetY($currentY);
                        $shape->setInsetRight(0.0);
                        $shape->setInsetLeft(0.0);
                        $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                        $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $shape->createTextRun(substr($month, 0, 3))->getFont()->setSize(4)->setBold(false)->setName($request->font_type);

                        // Right line for this month
                        $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX + $w, 2), (int) $currentY + 16, (int) round($currentX + $w, 2), 890);
                        $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                        $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                        $line->getBorder()->setLineWidth(0.75);
                        $line->getBorder()->setColor(new Color('FFa9a9a9'));
                        $slide->addShape($line);

                        $currentX += $w; // float to avoid error accumulation
                    }

                    $shape2 = $slide->createRichTextShape()
                        ->setHeight(30)
                        ->setWidth(($customWidth - $padding) - 3)
                        ->setOffsetX(22)
                        ->setOffsetY(47);
                    $shape2->setInsetRight(0.0);
                    $shape2->setInsetLeft(0.0);
                    $shape2->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_NONE);
                    $shape2->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                    $shape2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $shape2->createTextRun($file->name)->getFont()->setSize(intval($request->title_font_size))->setBold(true)->setName($request->font_type);

                    $currentY = 87;
                } elseif ($request->title_status == 'non') {
                    $currentY = 10;
                    $currentX = 20; // float for precision

                    foreach ($array_years as $year => $countDays) {
                        $w = $day_width * $countDays;

                        $shape = $slide->createRichTextShape()
                            ->setHeight(20)
                            ->setWidth($w)
                            ->setOffsetX($currentX)
                            ->setOffsetY($currentY);

                        $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                        $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $shape->createTextRun($year)->getFont()->setSize(9)->setBold(false)->setName($request->font_type);

                        $currentX += $w; // Keep float accuracy here
                    }
                    $currentY   = $currentY + 20.7;
                    $currentX   = 20;
                    $first_line = false;

                    foreach ($array_months as $month => $daysInMonth) {
                        $w = $day_width * $daysInMonth;

                        // Add left line only once at start
                        if (! $first_line) {
                            $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX, 2), (int) $currentY + 16, (int) round($currentX, 2), 890);
                            $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                            $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                            $line->getBorder()->setLineWidth(0.75);
                            $line->getBorder()->setColor(new Color('FFa9a9a9'));
                            $slide->addShape($line);
                            $first_line = true;
                        }

                        // Draw month box
                        $shape = $slide->createRichTextShape()
                            ->setHeight(16)
                            ->setWidth(round($w, 2))
                            ->setOffsetX(round($currentX, 2))
                            ->setOffsetY($currentY);
                        $shape->setInsetRight(0.0);
                        $shape->setInsetLeft(0.0);
                        $shape->getFill()->setFillType('solid')->setStartColor(new Color('FFFF00'));
                        $shape->getBorder()->setLineWidth(1.5)->setColor(new Color(Color::COLOR_BLACK));
                        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $shape->createTextRun(substr($month, 0, 3))->getFont()->setSize(4)->setBold(false)->setName($request->font_type);

                        // Right line for this month
                        $line = new \PhpOffice\PhpPresentation\Shape\Line((int) round($currentX + $w, 2), (int) $currentY + 16, (int) round($currentX + $w, 2), 890);
                        $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                        $line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                        $line->getBorder()->setLineWidth(0.75);
                        $line->getBorder()->setColor(new Color('FFa9a9a9'));
                        $slide->addShape($line);

                        $currentX += $w; // float to avoid error accumulation
                    }
                    $currentY = 57;
                }
                $currentX = 20;
            }
            $doc_gantt_chart = GanttChartDocData::where('file_document_id', $doc->id)->first();
            if ($doc_gantt_chart) {

                if ($doc_gantt_chart->show_cur == '1') {
                    $sections = json_decode($doc_gantt_chart->cur_sections, true);
                    if ($doc_gantt_chart->cur_type == 'SB' || $doc_gantt_chart->cur_type == 'DA' || $doc_gantt_chart->cur_type == 'MS') {

                        $cur_left_caption = $doc_gantt_chart->cur_left_caption ? $doc_gantt_chart->cur_left_caption : '';
                        if ($doc_gantt_chart->cur_show_ref == 'l') {
                            $cur_left_caption .= $cur_left_caption == '' ? '' : ' - ';
                            $cur_left_caption .= $doc->document->reference;
                        }
                        if ($doc_gantt_chart->cur_show_sd == '1') {
                            $cur_left_caption .= $cur_left_caption == '' ? '' : ' - ';
                            $cur_left_caption .= date('d.M.y', strtotime($sections[0]['sd']));

                        }

                        $cur_right_caption = '';
                        if ($doc_gantt_chart->cur_show_fd == '1') {
                            $cur_right_caption = date('d.M.y', strtotime($sections[count($sections) - 1]['fd']));
                        }
                        if ($doc_gantt_chart->cur_show_ref == 'r') {
                            $cur_right_caption .= $cur_right_caption == '' ? '' : ' - ';
                            $cur_right_caption .= $doc->document->reference;
                        }
                        $cur_right_caption .= $cur_right_caption == '' || $doc_gantt_chart->cur_right_caption==null ? '' : ' - ';
                        $cur_right_caption .= $doc_gantt_chart->cur_right_caption ? $doc_gantt_chart->cur_right_caption : '';
                        $yearMonth1 = date('Y-m', strtotime($sections[0]['sd']));
                        $yearMonth2 = date('Y-m', strtotime($sections[count($sections) - 1]['fd']));
                        if ($yearMonth1 >= $startTimeScale && $yearMonth1 <= $endTimeScale && $yearMonth2 >= $startTimeScale && $yearMonth2 <= $endTimeScale) {

                            $startX = $currentX + ($day_width * $this->calc_days($startTimeScale . '-01', $sections[0]['sd']));
                            $wid    = 0;

                            foreach ($sections as $key => $section) {
                                dd($section);
                                $cur_width = $day_width * $this->calc_days($section['sd'], $section['fd']);
                                $dd        = new AutoShape;
                                if ($doc_gantt_chart->cur_type == 'SB') {
                                    $dd->setType(AutoShape::TYPE_ROUNDED_RECTANGLE);
                                    $dd->setOffsetX($startX + $wid)->setOffsetY($currentY + 5)->setWidth(round($cur_width, 2))->setHeight(17); // moved down
                                } elseif ($doc_gantt_chart->cur_type == 'DA') {
                                    $dd->setType(AutoShape::TYPE_LEFT_RIGHT_ARROW);
                                    $dd->setOffsetX($startX + $wid)->setOffsetY($currentY + 5)->setWidth(round($cur_width, 2))->setHeight(17); // moved down
                                } elseif ($doc_gantt_chart->cur_type == 'MS' && ($key == 0 || $key == count($sections) - 1)) {

                                    if ($key == 0) {
                                        $dd->setType(AutoShape::TYPE_ROUNDED_RECTANGLE);
                                        $dd->setOffsetX($startX + $wid)->setOffsetY($currentY + 5)->setWidth(round($cur_width, 2) + 1.5)->setHeight(17); // moved down

                                    } else {
                                        $dd->setType(AutoShape::TYPE_ROUND_2_SAME_RECTANGLE);
                                        $dd->setOffsetX($startX + $wid + ((round($cur_width - 17, 2) / 2)))->setOffsetY($currentY + 5 - ((round($cur_width - 18, 2) / 2)))->setWidth(17)->setHeight(round($cur_width, 2)); // moved down

                                        $dd->setRotation(90);
                                    }

                                } elseif ($doc_gantt_chart->cur_type == 'MS' && ($key != 0 && $key != count($sections) - 1)) {
                                    $dd->setType(AutoShape::TYPE_RECTANGLE);
                                    $dd->setOffsetX($startX + $wid)->setOffsetY($currentY + 5)->setWidth(round($cur_width, 2))->setHeight(17); // moved down
                                }
                                $dd->getFill()->setFillType('solid')->setStartColor(new Color('FF' . $section['color'])); // red
                                $outline = $dd->getOutline();
                                $outline->getFill() // The outline's fill defines its color
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->setStartColor(new Color(Color::COLOR_BLACK)); // Black border
                                $outline->setWidth(1);
                                $slide->addShape($dd);
                                $wid = $wid + $cur_width;
                            }

                            if ($cur_left_caption != '') {
                                $unit_pixel_cur_left_caption = 2 + $this->calc_pixels($cur_left_caption, intval($request->cur_font_size));
                                if ($unit_pixel_cur_left_caption > (($startX - 1) - $currentX)) {
                                    $unit_pixel_cur_left_caption = ($startX - 1) - $currentX;
                                    $space_y_cur_left_caption    = -2;
                                } else {
                                    $space_y_cur_left_caption = 5;
                                }
                                $startXX = ($startX - 1) - $unit_pixel_cur_left_caption;
                                $shapeXX = $slide->createRichTextShape()
                                    ->setHeight(17)
                                    ->setWidth($unit_pixel_cur_left_caption + 3)
                                    ->setOffsetX(round($startXX, 2) - 4)
                                    ->setOffsetY($currentY + $space_y_cur_left_caption);
                                $shapeXX->setInsetRight(0.0);
                                $shapeXX->setInsetLeft(0.0);
                                $shapeXX->setInsetBottom(0.0);
                                $shapeXX->setInsetTop(0.0);
                                $shapeXX->getFill()->setFillType('solid')->setStartColor(new Color('FFFFFFFF'));
                                $shapeXX->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                                $shapeXX->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                                $shapeXX->createTextRun($cur_left_caption)->getFont()->setSize(intval($request->cur_font_size))->setBold(false)->setName($request->font_type);
                            }
                            if ($cur_right_caption != '') {

                                $unit_pixel_cur_right_caption = 2 + $this->calc_pixels($cur_right_caption, intval($request->cur_font_size));
                                if ($unit_pixel_cur_right_caption > (($day_width * $this->calc_days($sections[count($sections) - 1]['fd'], $endTimeScale . '-30')) - 2)) {

                                    $unit_pixel_cur_right_caption = ($day_width * $this->calc_days($sections[count($sections) - 1]['fd'], $endTimeScale . '-30')) - 2;
                                    $space_y_cur_right_caption    = -2;
                                } else {
                                    $space_y_cur_right_caption = 5;
                                }
                                $startXXX = $startX + $wid + 2;
                                $shapeXXX = $slide->createRichTextShape()
                                    ->setHeight(17)
                                    ->setWidth($unit_pixel_cur_right_caption + 3)
                                    ->setOffsetX(round($startXXX, 2))
                                    ->setOffsetY($currentY + $space_y_cur_right_caption);
                                $shapeXXX->setInsetRight(0.0);
                                $shapeXXX->setInsetLeft(0.0);
                                $shapeXXX->setInsetBottom(0.0);
                                $shapeXXX->setInsetTop(0.0);
                                $shapeXXX->getFill()->setFillType('solid')->setStartColor(new Color('FFFFFFFF'));
                                $shapeXXX->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                                $shapeXXX->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                                $shapeXXX->createTextRun($cur_right_caption)->getFont()->setSize(intval($request->cur_font_size))->setBold(false)->setName($request->font_type);
                            }

                        }

                    }
                    if ($doc_gantt_chart->cur_type == 'M' || $doc_gantt_chart->cur_type == 'S') {
                        $cur_left_caption = $doc_gantt_chart->cur_left_caption ? $doc_gantt_chart->cur_left_caption : '';
                        if ($doc_gantt_chart->cur_show_ref == 'l') {
                            $cur_left_caption .= $cur_left_caption == '' ? '' : ' - ';

                            $cur_left_caption .= $doc->document->reference;
                        }
                        if ($doc_gantt_chart->cur_show_sd == '1') {
                            $cur_left_caption .= $cur_left_caption == '' ? '' : ' - ';
                            $cur_left_caption .= date('d.M.y', strtotime($sections[0]['sd']));

                        }
                        $cur_right_caption = '';

                        if ($doc_gantt_chart->cur_show_ref == 'r') {
                            $cur_right_caption .= $doc->document->reference;
                        }
                        $cur_right_caption .= $cur_right_caption == '' || $doc_gantt_chart->cur_right_caption==null ? '' : ' - ';
                        $cur_right_caption .= $doc_gantt_chart->cur_right_caption ? $doc_gantt_chart->cur_right_caption : '';
                        $yearMonth1 = date('Y-m', strtotime($sections[0]['sd']));

                        if ($yearMonth1 >= $startTimeScale && $yearMonth1 <= $endTimeScale) {
                            $startXy = $currentX + ($day_width * $this->calc_days($startTimeScale . '-01', $sections[0]['sd'])) - 8;
                            $dd      = new AutoShape;
                            if ($doc_gantt_chart->cur_type == 'M') {
                                $dd->setType(AutoShape::TYPE_DIAMOND);

                            } elseif ($doc_gantt_chart->cur_type == 'S') {
                                $dd->setType(AutoShape::TYPE_5_POINT_STAR);
                            }
                            $dd->setOffsetX($startXy)->setOffsetY($currentY + 5)->setWidth(16)->setHeight(17);            // moved down
                            $dd->getFill()->setFillType('solid')->setStartColor(new Color('FF' . $sections[0]['color'])); // red
                            $outline = $dd->getOutline();
                            $outline->getFill() // The outline's fill defines its color
                                ->setFillType(Fill::FILL_SOLID)
                                ->setStartColor(new Color(Color::COLOR_BLACK)); // Black border
                            $outline->setWidth(1);
                            $slide->addShape($dd);
                            if ($cur_left_caption != '') {
                                $unit_pixel_cur_left_caption = 2 + $this->calc_pixels($cur_left_caption, intval($request->cur_font_size));
                                if ($unit_pixel_cur_left_caption > (($startXy - 1) - $currentX)) {
                                    $unit_pixel_cur_left_caption = ($startXy - 1) - $currentX;
                                    $space_y_cur_left_caption    = -2;
                                } else {
                                    $space_y_cur_left_caption = 5;
                                }
                                $startXX = ($startXy - 1) - $unit_pixel_cur_left_caption;
                                $shapeXX = $slide->createRichTextShape()
                                    ->setHeight(17)
                                    ->setWidth($unit_pixel_cur_left_caption + 3)
                                    ->setOffsetX(round($startXX, 2)-4)
                                    ->setOffsetY($currentY + $space_y_cur_left_caption);
                                $shapeXX->setInsetRight(0.0);
                                $shapeXX->setInsetLeft(0.0);
                                $shapeXX->setInsetBottom(0.0);
                                $shapeXX->setInsetTop(0.0);
                                $shapeXX->getFill()->setFillType('solid')->setStartColor(new Color('FFFFFFFF'));
                                $shapeXX->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                                $shapeXX->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                                $shapeXX->createTextRun($cur_left_caption)->getFont()->setSize(intval($request->cur_font_size))->setBold(false)->setName($request->font_type);
                            }
                            if ($cur_right_caption != '') {

                                $unit_pixel_cur_right_caption = 2 + $this->calc_pixels($cur_right_caption, intval($request->cur_font_size));
                                if ($unit_pixel_cur_right_caption > (($day_width * $this->calc_days($sections[0]['sd'], $endTimeScale . '-30')) - 10)) {

                                    $unit_pixel_cur_right_caption = ($day_width * $this->calc_days($sections[0]['sd'], $endTimeScale . '-30')) - 10;
                                    $space_y_cur_right_caption    = -2;
                                } else {
                                    $space_y_cur_right_caption = 5;
                                }
                                $startXXX = $startXy + 19;
                                $shapeXXX = $slide->createRichTextShape()
                                    ->setHeight(17)
                                    ->setWidth($unit_pixel_cur_right_caption + 3)
                                    ->setOffsetX(round($startXXX, 2))
                                    ->setOffsetY($currentY + $space_y_cur_right_caption);
                                $shapeXXX->setInsetRight(0.0);
                                $shapeXXX->setInsetLeft(0.0);
                                $shapeXXX->setInsetBottom(0.0);
                                $shapeXXX->setInsetTop(0.0);
                                $shapeXXX->getFill()->setFillType('solid')->setStartColor(new Color('FFFFFFFF'));
                                $shapeXXX->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                                $shapeXXX->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                                $shapeXXX->createTextRun($cur_right_caption)->getFont()->setSize(intval($request->cur_font_size))->setBold(false)->setName($request->font_type);
                            }
                        }

                    }
                }
                if ($doc_gantt_chart->show_pl == '1') {
                    if ($doc_gantt_chart->pl_type == 'SB') {
                        $pl_left_caption = $doc_gantt_chart->pl_left_caption ? $doc_gantt_chart->pl_left_caption : '';

                        if ($doc_gantt_chart->pl_show_sd == '1') {
                            $pl_left_caption .= $pl_left_caption == '' || $doc_gantt_chart->pl_sd==null ? '' : ' - ';
                            $pl_left_caption .= date('d.M.y', strtotime($doc_gantt_chart->pl_sd));

                        }

                        $pl_right_caption = '';
                        if ($doc_gantt_chart->pl_show_fd == '1') {
                            $pl_right_caption = date('d.M.y', strtotime($doc_gantt_chart->pl_fd));
                        }
                        $pl_right_caption .= $pl_right_caption == '' || $doc_gantt_chart->pl_right_caption==null ? '' : ' - ';
                        $pl_right_caption .= $doc_gantt_chart->pl_right_caption ? $doc_gantt_chart->pl_right_caption : '';
                        $yearMonth1 = date('Y-m', strtotime($doc_gantt_chart->pl_sd));
                        $yearMonth2 = date('Y-m', strtotime($doc_gantt_chart->pl_fd));
                        if ($yearMonth1 >= $startTimeScale && $yearMonth1 <= $endTimeScale && $yearMonth2 >= $startTimeScale && $yearMonth2 <= $endTimeScale) {
                            $startX_pl    = $currentX + ($day_width * $this->calc_days($startTimeScale . '-01', $doc_gantt_chart->pl_sd));
                            $cur_width_pl = $day_width * $this->calc_days($doc_gantt_chart->pl_sd, $doc_gantt_chart->pl_fd);
                            $dd           = new AutoShape;
                            $dd->setType(AutoShape::TYPE_ROUNDED_RECTANGLE);
                            $dd->setOffsetX($startX_pl)->setOffsetY($currentY + 30)->setWidth(round($cur_width_pl, 2))->setHeight(5);
                            $dd->getFill()->setFillType('solid')->setStartColor(new Color('FF' . $doc_gantt_chart->pl_color)); // red
                            $outline = $dd->getOutline();
                            $outline->getFill() // The outline's fill defines its color
                                ->setFillType(Fill::FILL_SOLID)
                                ->setStartColor(new Color(Color::COLOR_BLACK)); // Black border
                            $outline->setWidth(0.6);
                            $slide->addShape($dd);
                            if ($pl_left_caption != '') {
                                $unit_pixel_pl_left_caption = 1 + $this->calc_pixels($pl_left_caption, intval($request->pl_font_size));
                                if ($unit_pixel_pl_left_caption > (($startX_pl - 1) - $currentX)) {
                                    $unit_pixel_pl_left_caption = ($startX_pl - 1) - $currentX;
                                }
                                $startXX_pl = ($startX_pl - 1) - $unit_pixel_pl_left_caption;
                                $shapeXX    = $slide->createRichTextShape()
                                    ->setHeight(7)
                                    ->setWidth($unit_pixel_pl_left_caption + 3)
                                    ->setOffsetX(round($startXX_pl, 2) - 5)
                                    ->setOffsetY($currentY + 28);
                                $shapeXX->setInsetRight(0.0);
                                $shapeXX->setInsetLeft(0.0);
                                $shapeXX->setInsetBottom(0.0);
                                $shapeXX->setInsetTop(0.0);
                                $shapeXX->getFill()->setFillType('solid')->setStartColor(new Color('FFFFFFFF'));
                                $shapeXX->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                                $shapeXX->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                                $shapeXX->createTextRun($pl_left_caption)->getFont()->setSize(intval($request->pl_font_size))->setBold(false)->setName($request->font_type);
                            }
                            if ($pl_right_caption != '') {

                                $unit_pixel_pl_right_caption = 2 + $this->calc_pixels($pl_right_caption, intval($request->pl_font_size));
                                if ($unit_pixel_pl_right_caption > (($day_width * $this->calc_days($doc_gantt_chart->pl_fd, $endTimeScale . '-30')) - 2)) {

                                    $unit_pixel_pl_right_caption = ($day_width * $this->calc_days($doc_gantt_chart->pl_fd, $endTimeScale . '-30')) - 2;
                                }
                                $startXXX_pl = $startX_pl + $cur_width_pl + 2;
                                $shapeXXX    = $slide->createRichTextShape()
                                    ->setHeight(7)
                                    ->setWidth($unit_pixel_pl_right_caption + 3)
                                    ->setOffsetX(round($startXXX_pl, 2))
                                    ->setOffsetY($currentY + 28);
                                $shapeXXX->setInsetRight(0.0);
                                $shapeXXX->setInsetLeft(0.0);
                                $shapeXXX->setInsetBottom(0.0);
                                $shapeXXX->setInsetTop(0.0);
                                $shapeXXX->getFill()->setFillType('solid')->setStartColor(new Color('FFFFFFFF'));
                                $shapeXXX->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                                $shapeXXX->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                                $shapeXXX->createTextRun($pl_right_caption)->getFont()->setSize(intval($request->pl_font_size))->setBold(false)->setName($request->font_type);
                            }
                        }

                    }
                    if ($doc_gantt_chart->pl_type == 'M') {
                        $pl_left_caption = $doc_gantt_chart->pl_left_caption ? $doc_gantt_chart->pl_left_caption : '';

                        if ($doc_gantt_chart->pl_show_sd == '1') {
                            $pl_left_caption .= $pl_left_caption == '' || $doc_gantt_chart->pl_sd==null ? '' : ' - ';
                            $pl_left_caption .= date('d.M.y', strtotime($doc_gantt_chart->pl_sd));

                        }
                        $pl_right_caption = $doc_gantt_chart->pl_right_caption ? $doc_gantt_chart->pl_right_caption : '';
                        $yearMonth1       = date('Y-m', strtotime($doc_gantt_chart->pl_sd));

                        if ($yearMonth1 >= $startTimeScale && $yearMonth1 <= $endTimeScale) {
                            $startXy = $currentX + ($day_width * $this->calc_days($startTimeScale . '-01', $doc_gantt_chart->pl_sd)) - 3;
                            $dd      = new AutoShape;
                            $dd->setType(AutoShape::TYPE_DIAMOND);
                            $dd->setOffsetX($startXy)->setOffsetY($currentY + 30)->setWidth(6)->setHeight(6);                  // moved down
                            $dd->getFill()->setFillType('solid')->setStartColor(new Color('FF' . $doc_gantt_chart->pl_color)); // red
                            $outline = $dd->getOutline();
                            $outline->getFill() // The outline's fill defines its color
                                ->setFillType(Fill::FILL_SOLID)
                                ->setStartColor(new Color(Color::COLOR_BLACK)); // Black border
                            $outline->setWidth(0.6);
                            $slide->addShape($dd);
                            if ($pl_left_caption != '') {
                                $unit_pixel_pl_left_caption = 0.5 + $this->calc_pixels($pl_left_caption, intval($request->pl_font_size));
                                if ($unit_pixel_pl_left_caption > (($startXy - 1) - $currentX)) {
                                    $unit_pixel_pl_left_caption = ($startXy - 1) - $currentX;
                                }
                                $startXX = ($startXy - 1) - $unit_pixel_pl_left_caption;
                                $shapeXX = $slide->createRichTextShape()
                                    ->setHeight(7)
                                    ->setWidth($unit_pixel_pl_left_caption + 3)
                                    ->setOffsetX(round($startXX, 2)-3)
                                    ->setOffsetY($currentY + 28);
                                $shapeXX->setInsetRight(0.0);
                                $shapeXX->setInsetLeft(0.0);
                                $shapeXX->setInsetBottom(0.0);
                                $shapeXX->setInsetTop(0.0);
                                $shapeXX->getFill()->setFillType('solid')->setStartColor(new Color('FFFFFFFF'));
                                $shapeXX->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                                $shapeXX->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                                $shapeXX->createTextRun($pl_left_caption)->getFont()->setSize(intval($request->pl_font_size))->setBold(false)->setName($request->font_type);
                            }
                            if ($pl_right_caption != '') {

                                $unit_pixel_pl_right_caption = 2 + $this->calc_pixels($pl_right_caption, intval($request->pl_font_size));
                                if ($unit_pixel_pl_right_caption > (($day_width * $this->calc_days($doc_gantt_chart->pl_sd, $endTimeScale . '-30')) - 10)) {

                                    $unit_pixel_pl_right_caption = ($day_width * $this->calc_days($doc_gantt_chart->pl_sd, $endTimeScale . '-30')) - 10;
                                }
                                $startXXX = $startXy + 8;
                                $shapeXXX = $slide->createRichTextShape()
                                    ->setHeight(7)
                                    ->setWidth($unit_pixel_pl_right_caption + 3)
                                    ->setOffsetX(round($startXXX, 2) + 1)
                                    ->setOffsetY($currentY + 28);
                                $shapeXXX->setInsetRight(0.0);
                                $shapeXXX->setInsetLeft(0.0);
                                $shapeXXX->setInsetBottom(0.0);
                                $shapeXXX->setInsetTop(0.0);
                                $shapeXXX->getFill()->setFillType('solid')->setStartColor(new Color('FFFFFFFF'));
                                $shapeXXX->getBorder()->setLineWidth(0)->setColor(new Color(Color::COLOR_WHITE));
                                $shapeXXX->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                                $shapeXXX->createTextRun($pl_right_caption)->getFont()->setSize(intval($request->pl_font_size))->setBold(false)->setName($request->font_type);
                            }
                        }
                    }
                }
                if ($doc_gantt_chart->show_lp == '1' && $doc_gantt_chart->lp_sd != null && $doc_gantt_chart->lp_fd != null) {

                    $yearMonth1 = date('Y-m', strtotime($doc_gantt_chart->lp_sd));
                    $yearMonth2 = date('Y-m', strtotime($doc_gantt_chart->lp_fd));
                    if ($yearMonth1 >= $startTimeScale && $yearMonth1 <= $endTimeScale && $yearMonth2 >= $startTimeScale && $yearMonth2 <= $endTimeScale) {
                        $startX_lp    = $currentX + ($day_width * $this->calc_days($startTimeScale . '-01', $doc_gantt_chart->lp_sd));
                        $cur_width_lp = $day_width * $this->calc_days($doc_gantt_chart->lp_sd, $doc_gantt_chart->lp_fd);
                        $line         = new \PhpOffice\PhpPresentation\Shape\Line((int) $startX_lp, $currentY, (int) $startX_lp + (int) $cur_width_lp, $currentY); // from (x1, y1) to (x2, y2)
                        $line->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
                        //$line->getBorder()->setDashStyle(\PhpOffice\PhpPresentation\Style\Border::DASH_DASHDOT);
                        $line->getBorder()->setLineWidth(3);
                        $line->getBorder()->setColor(new Color('FFFF2400')); // red
                        $slide->addShape($line);
                    }
                }

                if ($doc_gantt_chart->show_cur == '1' || $doc_gantt_chart->show_pl == '1') {
                    $currentY = $currentY + 36;
                    if ($request->raw_height == '1') {
                        $currentY += 16;
                    } elseif ($request->raw_height == '1.5') {
                        $currentY += 25;
                    } elseif ($request->raw_height == '2') {
                        $currentY += 33;
                    }
                }

            }

        }
///////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
        $path          = public_path($projectFolder);
        if (! file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $code      = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

        if (! file_exists($directory)) {
            mkdir($directory, 0755, true); // true = create nested directories
        }
        $fileName = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $file->name . '_presentation_' . time() . '.pptx';
        $filePath = public_path($fileName);

        ob_clean();
        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $writer->save($filePath);
        session(['zip_file' => $code]);
        $headers = [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0,no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ];

        return response()->json(['download_url' => asset($fileName)]);
        // Return the file as a download response
        //return response()->download($filePath, null, $headers)->deleteFileAfterSend(false);
    }
    private function calc_days($date1, $date2)
    {
        dd($date1,$date2);
        $start = new DateTime($date1);
        $end   = new DateTime($date2);

        $interval = $start->diff($end);
        $days     = $interval->days;
        dd($days);
        return $days;
    }
    private function calc_pixels($text, $font_size)
    {
        $fontSize   = intval($font_size); // in points
        $totalWidth = 0;
        $arr1       = [
            '8'  => 0.55,
            '10' => 0.55,
            '12' => 0.56,
            '14' => 0.57,
        ];
        $arr2 = [
            '8'  => 1.7,
            '10' => 1.7,
            '12' => 1.2,
            '14' => 1,
        ];

        // Loop through each character
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];

            // Use wider width for digits and capital letters
            if (ctype_digit($char) || ctype_upper($char)) {
                $charWidth = $fontSize * $arr1[strval($fontSize)] * $arr2[strval($fontSize)];
            } else {
                $charWidth = $fontSize * $arr1[strval($fontSize)];
            }

            $totalWidth += $charWidth;
        }
        return round($totalWidth, 2);
    }

}
