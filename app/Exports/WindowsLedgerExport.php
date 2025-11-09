<?php
namespace App\Exports;

use App\Models\DrivingActivity;
use App\Models\Window;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WindowsLedgerExport implements FromArray, WithEvents
{
    protected $request;

    public function __construct($request)
    {

        $this->request = $request;
    }

    public function array(): array
    {
        // Build the rows exactly like your screenshot
        $rows = [];

        return $rows;
    }
    public function comp_date($project_id, $window_id, $prog, $ms = [])
    {
        $query = DrivingActivity::where('project_id', $project_id)
            ->where('window_id', $window_id)
            ->where('program', $prog);

        if (! empty($ms)) {
            $query->whereIn('milestone_id', $ms);
        }

        // Get the latest (newest) ms_come_date
        $latestDate = $query->max('ms_come_date');

        return $latestDate;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getPageMargins()->setTop(0.75 / 2.54); // 0.75 inch → cm
                $sheet->getPageMargins()->setBottom(0.75 / 2.54);
                $sheet->getPageMargins()->setLeft(0.25 / 2.54);
                $sheet->getPageMargins()->setRight(0.25 / 2.54);
                $sheet->getPageMargins()->setHeader(0.3 / 2.54);
                $sheet->getPageMargins()->setFooter(0.3 / 2.54);

                $request = $this->request;
                //dd($request);
                $col_array = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
                foreach (range('A', 'Q') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(false);
                }

                $key = 7;
                // Header titles
                $sheet->setCellValue('A1', 'Window no.');
                $sheet->setCellValue('B1', 'Start Date');
                $sheet->setCellValue('C1', 'Finish Date');
                $sheet->setCellValue('D1', 'Window Duration');
                $sheet->setCellValue('E1', 'POW');
                $sheet->setCellValue('F1', 'Data Date');
                $sheet->setCellValue('G1', 'Finish Date');
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('E1:E2');
                $sheet->mergeCells('F1:F2');
                $sheet->mergeCells('G1:G2');
                $sheet->getColumnDimension('A')->setWidth(8);  // Window no.
                $sheet->getColumnDimension('B')->setWidth(12); // Start Date
                $sheet->getColumnDimension('C')->setWidth(12); // Finish Date
                $sheet->getColumnDimension('D')->setWidth(9);
                $sheet->getColumnDimension('E')->setWidth(5);  // POW
                $sheet->getColumnDimension('F')->setWidth(12); // Data Date
                $sheet->getColumnDimension('G')->setWidth(12);
                $start_variance = null;
                $end_variance   = null;
                if (($request['BAS'] === 'option1' && isset($request['IMP'])) || (isset($request['IMP']) && isset($request['UPD'])) || (isset($request['UPD']) && isset($request['BUT']))) {
                    $start_variance = $col_array[$key];
                    $sheet->setCellValue($col_array[$key] . '1', 'Variance');
                    if ($request['BAS'] === 'option1' && isset($request['IMP'])) {
                        $sheet->setCellValue($col_array[$key] . '2', 'IMP-BAS');
                        $end_variance = $col_array[$key];
                        $sheet->getColumnDimension($col_array[$key])->setWidth(9);
                    }
                    if (isset($request['IMP']) && isset($request['UPD'])) {
                        $key += 1;
                        $sheet->setCellValue($col_array[$key] . '2', 'UPD-IMP');
                        $end_variance = $col_array[$key];
                        $sheet->getColumnDimension($col_array[$key])->setWidth(9);
                    }
                    if (isset($request['UPD']) && isset($request['BUT'])) {
                        $key += 1;
                        $sheet->setCellValue($col_array[$key] . '2', 'BUT-UPD');
                        $end_variance = $col_array[$key];
                        $sheet->getColumnDimension($col_array[$key])->setWidth(9);
                    }
                    if ($start_variance != $end_variance) {
                        $sheet->mergeCells($start_variance . '1:' . $end_variance . '1');
                    }
                }
                $key += 1;
                $sheet->setCellValue($col_array[$key] . '1', 'Driving DE');
                $sheet->mergeCells($col_array[$key] . '1:' . $col_array[$key] . '2');

                $key++;
                $sheet->setCellValue($col_array[$key] . '1', 'Driving Act ID');
                $sheet->mergeCells($col_array[$key] . '1:' . $col_array[$key] . '2');
                $key++;
                $sheet->setCellValue($col_array[$key] . '1', 'Driving Act Name');
                $sheet->mergeCells($col_array[$key] . '1:' . $col_array[$key] . '2');

                $start_Liability = null;
                $end_Liability   = null;
                if (isset($request['Culpable']) || isset($request['Excusable']) || isset($request['Compensable']) || isset($request['Compensable_Transfer'])) {
                    $key += 1;
                    $start_Liability = $col_array[$key];
                    $sheet->setCellValue($col_array[$key] . '1', 'Liability');
                    if (isset($request['Culpable'])) {
                        $sheet->setCellValue($col_array[$key] . '2', 'Culpable');
                        $end_Liability = $col_array[$key];
                    }
                    if (isset($request['Excusable'])) {
                        $key += 1;
                        $sheet->setCellValue($col_array[$key] . '2', 'Excusable');
                        $end_Liability = $col_array[$key];

                    }
                    if (isset($request['Compensable'])) {
                        $key += 1;
                        $sheet->setCellValue($col_array[$key] . '2', 'Compensable');
                        $end_Liability = $col_array[$key];
                    }
                    if (isset($request['Compensable_Transfer'])) {
                        $key += 1;
                        $sheet->setCellValue($col_array[$key] . '2', 'Compensable Transfer');
                        $end_Liability = $col_array[$key];

                    }
                    if ($start_Liability != $end_Liability) {
                        $sheet->mergeCells($start_Liability . '1:' . $end_Liability . '1');
                    }
                }

                // Header style
                $sheet->getStyle('A1:' . $col_array[$key] . '2')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'fill'      => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '000000'],
                    ],
                    'borders'   => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                ]);
                $color       = false;
                $row_counter = 3;
                $x           = 0;
                $all_windows = Window::where('project_id', auth()->user()->current_project_id)
                    ->orderByRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED)')
                    ->get();
                $first_w = true;
                foreach ($all_windows as $window) {
                    $row_counter        = $row_counter + $x;
                    $x                  = 0;
                    $dates              = [];
                    $variance_array_col = [];
                    $sheet->setCellValue('A' . $row_counter, $window->no);
                    $sheet->setCellValue('B' . $row_counter, date('d-M-Y', strtotime($window->start_date)));
                    $sheet->setCellValue('C' . $row_counter, date('d-M-Y', strtotime($window->end_date)));
                    $sheet->setCellValue('D' . $row_counter, $window->duration);
                    if ($request['BAS'] === 'option2') {
                        if ($first_w) {
                            $sheet->setCellValue('E' . $row_counter, 'BAS');
                            $sheet->setCellValue('F' . $row_counter, date('d-M-Y', strtotime($window->start_date)));
                            $ms_come_date = $this->comp_date(auth()->user()->current_project_id, $window->id, 'BAS', [$request['lastMS']]);
                            $dates['BAS'] = $ms_come_date;
                            $sheet->setCellValue('G' . $row_counter, $ms_come_date ? date('d-M-Y', strtotime($ms_come_date)) : '__');
                            $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                                ->where('window_id', $window->id)
                                ->where('program', 'BAS')->where('milestone_id', $request['lastMS'])->where('ms_come_date', $dates['BAS'])->count();
                            if ($activities_count > 0) {
                                $x = $x + $activities_count - 1;
                                $sheet->mergeCells('E' . $row_counter . ':' . 'E' . $row_counter + $activities_count - 1);
                                $sheet->mergeCells('F' . $row_counter . ':' . 'F' . $row_counter + $activities_count - 1);
                                $sheet->mergeCells('G' . $row_counter . ':' . 'G' . $row_counter + $activities_count - 1);
                            }
                        } else {
                            $sheet->setCellValue('E' . $row_counter, '__');
                            $sheet->setCellValue('F' . $row_counter, '__');
                            $sheet->setCellValue('G' . $row_counter, '__');

                        }
                    } else {
                        $sheet->setCellValue('E' . $row_counter, 'BAS');
                        $sheet->setCellValue('F' . $row_counter, date('d-M-Y', strtotime($window->start_date)));
                        $ms_come_date = $this->comp_date(auth()->user()->current_project_id, $window->id, 'BAS', [$request['lastMS']]);
                        $dates['BAS'] = $ms_come_date;
                        $sheet->setCellValue('G' . $row_counter, $ms_come_date ? date('d-M-Y', strtotime($ms_come_date)) : '__');
                        $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                            ->where('window_id', $window->id)
                            ->where('program', 'BAS')->where('milestone_id', $request['lastMS'])->where('ms_come_date', $dates['BAS'])->count();
                        if ($activities_count > 0) {
                            $x = $x + $activities_count - 1;
                            $sheet->mergeCells('E' . $row_counter . ':' . 'E' . $row_counter + $activities_count - 1);
                            $sheet->mergeCells('F' . $row_counter . ':' . 'F' . $row_counter + $activities_count - 1);
                            $sheet->mergeCells('G' . $row_counter . ':' . 'G' . $row_counter + $activities_count - 1);
                        }
                    }

                    if (isset($request['IMP'])) {
                        $x++;
                        $sheet->setCellValue('E' . $row_counter + $x, 'IMP');
                        $sheet->setCellValue('F' . $row_counter + $x, date('d-M-Y', strtotime($window->start_date)));
                        $ms_come_date = $this->comp_date(auth()->user()->current_project_id, $window->id, 'IMP', [$request['lastMS']]);
                        $dates['IMP'] = $ms_come_date;
                        $sheet->setCellValue('G' . $row_counter + $x, $ms_come_date ? date('d-M-Y', strtotime($ms_come_date)) : '__');
                        $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                            ->where('window_id', $window->id)
                            ->where('program', 'IMP')->where('milestone_id', $request['lastMS'])->where('ms_come_date', $dates['IMP'])->count();
                        if ($activities_count > 0) {

                            $sheet->mergeCells('E' . $row_counter + $x . ':' . 'E' . $row_counter + $x + $activities_count - 1);
                            $sheet->mergeCells('F' . $row_counter + $x . ':' . 'F' . $row_counter + $x + $activities_count - 1);
                            $sheet->mergeCells('G' . $row_counter + $x . ':' . 'G' . $row_counter + $x + $activities_count - 1);
                            $x = $x + $activities_count - 1;
                        }
                    }
                    if (isset($request['UPD'])) {
                        $x++;
                        $sheet->setCellValue('E' . $row_counter + $x, 'UPD');
                        $sheet->setCellValue('F' . $row_counter + $x, date('d-M-Y', strtotime($window->end_date)));
                        $ms_come_date = $this->comp_date(auth()->user()->current_project_id, $window->id, 'UPD', [$request['lastMS']]);
                        $dates['UPD'] = $ms_come_date;
                        $sheet->setCellValue('G' . $row_counter + $x, $ms_come_date ? date('d-M-Y', strtotime($ms_come_date)) : '__');
                        $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                            ->where('window_id', $window->id)
                            ->where('program', 'UPD')->where('milestone_id', $request['lastMS'])->where('ms_come_date', $dates['UPD'])->count();
                        if ($activities_count > 0) {
                            $sheet->mergeCells('E' . $row_counter + $x . ':' . 'E' . $row_counter + $x + $activities_count - 1);
                            $sheet->mergeCells('F' . $row_counter + $x . ':' . 'F' . $row_counter + $x + $activities_count - 1);
                            $sheet->mergeCells('G' . $row_counter + $x . ':' . 'G' . $row_counter + $x + $activities_count - 1);
                            $x = $x + $activities_count - 1;
                        }
                    }
                    if (isset($request['BUT'])) {
                        $x++;
                        $sheet->setCellValue('E' . $row_counter + $x, 'BUT');
                        $sheet->setCellValue('F' . $row_counter + $x, date('d-M-Y', strtotime($window->end_date)));
                        $ms_come_date = $this->comp_date(auth()->user()->current_project_id, $window->id, 'BUT');
                        $dates['BUT'] = $ms_come_date;
                        $sheet->setCellValue('G' . $row_counter + $x, $ms_come_date ? date('d-M-Y', strtotime($ms_come_date)) : '__');
                        $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                            ->where('window_id', $window->id)
                            ->where('program', 'BUT')->where('ms_come_date', $dates['BUT'])->count();
                        if ($activities_count > 0) {
                            $sheet->mergeCells('E' . $row_counter + $x . ':' . 'E' . $row_counter + $x + $activities_count - 1);
                            $sheet->mergeCells('F' . $row_counter + $x . ':' . 'F' . $row_counter + $x + $activities_count - 1);
                            $sheet->mergeCells('G' . $row_counter + $x . ':' . 'G' . $row_counter + $x + $activities_count - 1);
                            $x = $x + $activities_count - 1;
                        }
                    }
                    $key2 = 7;

                    if ($request['BAS'] === 'option1' && isset($request['IMP'])) {
                        if ($dates['BAS'] != null && $dates['IMP'] != null) {
                            $start    = Carbon::parse($dates['BAS']);
                            $end      = Carbon::parse($dates['IMP']);
                            $duration = $start->diffInDays($end, false);
                            $sheet->setCellValue($col_array[$key2] . $row_counter, $duration);
                        } else {
                            $sheet->setCellValue($col_array[$key2] . $row_counter, '__');

                        }
                        $variance_array_col['IMP'] = $col_array[$key2];
                    }
                    if (isset($request['IMP']) && isset($request['UPD'])) {
                        $key2 += 1;
                        if ($dates['IMP'] != null && $dates['UPD'] != null) {
                            $start    = Carbon::parse($dates['IMP']);
                            $end      = Carbon::parse($dates['UPD']);
                            $duration = $start->diffInDays($end, false);
                            $sheet->setCellValue($col_array[$key2] . $row_counter, $duration);
                        } else {
                            $sheet->setCellValue($col_array[$key2] . $row_counter, '__');

                        }
                        $variance_array_col['UPD'] = $col_array[$key2];

                    }
                    if (isset($request['UPD']) && isset($request['BUT'])) {
                        $key2 += 1;
                        if ($dates['BUT'] != null && $dates['UPD'] != null) {
                            $start    = Carbon::parse($dates['UPD']);
                            $end      = Carbon::parse($dates['BUT']);
                            $duration = $start->diffInDays($end, false);
                            $sheet->setCellValue($col_array[$key2] . $row_counter, $duration);
                        } else {
                            $sheet->setCellValue($col_array[$key2] . $row_counter, '__');

                        }
                        $variance_array_col['BUT'] = $col_array[$key2];

                    }
                    $key2 += 1;
                    $y_act = 0;
                    if ($request['BAS'] === 'option2') {
                        if ($first_w) {
                            $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                                ->where('window_id', $window->id)
                                ->where('program', 'BAS')->where('milestone_id', $request['lastMS'])->where('ms_come_date', $dates['BAS'])->get();
                            if ($activities_count->isEmpty()) {
                                $y_act_row_counter = $row_counter + $y_act;
                                $y_act++;
                                $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                                $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, '__');
                                $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, '__');
                            } else {

                                foreach ($activities_count as $activity) {
                                    $y_act_row_counter = $row_counter + $y_act;
                                    $y_act++;
                                    $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                                    $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, $activity->activity->act_id);
                                    $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, $activity->activity->name);
                                }

                            }
                        } else {
                            $y_act_row_counter = $row_counter + $y_act;
                            $y_act++;
                            $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, '__');

                        }
                    } else {
                        $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                            ->where('window_id', $window->id)
                            ->where('program', 'BAS')->where('milestone_id', $request['lastMS'])->where('ms_come_date', $dates['BAS'])->get();
                        if ($activities_count->isEmpty()) {
                            $y_act_row_counter = $row_counter + $y_act;
                            $y_act++;
                            $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, '__');
                        } else {

                            foreach ($activities_count as $activity) {
                                $y_act_row_counter = $row_counter + $y_act;
                                $y_act++;
                                $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                                $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, $activity->activity->act_id);
                                $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, $activity->activity->name);
                            }

                        }
                    }

                    if (isset($request['IMP'])) {
                        $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                            ->where('window_id', $window->id)
                            ->where('program', 'IMP')->where('milestone_id', $request['lastMS'])->where('ms_come_date', $dates['IMP'])->get();
                        if ($activities_count->isEmpty()) {
                            $y_act_row_counter = $row_counter + $y_act;
                            $y_act++;
                            $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, '__');
                        } else {

                            foreach ($activities_count as $activity) {
                                $y_act_row_counter = $row_counter + $y_act;
                                $y_act++;
                                $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                                $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, $activity->activity->act_id);
                                $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, $activity->activity->name);
                            }

                        }
                    }

                    if (isset($request['UPD'])) {
                        $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                            ->where('window_id', $window->id)
                            ->where('program', 'UPD')->where('milestone_id', $request['lastMS'])->where('ms_come_date', $dates['UPD'])->get();
                        if ($activities_count->isEmpty()) {
                            $y_act_row_counter = $row_counter + $y_act;
                            $y_act++;
                            $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, '__');
                        } else {

                            foreach ($activities_count as $activity) {
                                $y_act_row_counter = $row_counter + $y_act;
                                $y_act++;
                                $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                                $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, $activity->activity->act_id);
                                $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, $activity->activity->name);
                            }

                        }
                    }

                    if (isset($request['BUT'])) {
                        $activities_count = DrivingActivity::where('project_id', auth()->user()->current_project_id)
                            ->where('window_id', $window->id)
                            ->where('program', 'BUT')->where('ms_come_date', $dates['BUT'])->get();
                        if ($activities_count->isEmpty()) {
                            $y_act_row_counter = $row_counter + $y_act;
                            $y_act++;
                            $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, '__');
                            $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, '__');
                        } else {

                            foreach ($activities_count as $activity) {
                                $y_act_row_counter = $row_counter + $y_act;
                                $y_act++;
                                $sheet->setCellValue($col_array[$key2] . $y_act_row_counter, '__');
                                $sheet->setCellValue($col_array[$key2 + 1] . $y_act_row_counter, $activity->activity->act_id);
                                $sheet->setCellValue($col_array[$key2 + 2] . $y_act_row_counter, $activity->activity->name);
                            }

                        }
                    }
                    $key2 += 3;
                    if (isset($request['Culpable'])) {
                        $sheet->setCellValue($col_array[$key2] . $row_counter, $window->culpable);
                        $sheet->mergeCells($col_array[$key2] . $row_counter . ':' . $col_array[$key2] . $row_counter + $x);
                    }
                    if (isset($request['Excusable'])) {
                        $key2 += 1;
                        $sheet->setCellValue($col_array[$key2] . $row_counter, $window->excusable);
                        $sheet->mergeCells($col_array[$key2] . $row_counter . ':' . $col_array[$key2] . $row_counter + $x);
                    }
                    if (isset($request['Compensable'])) {
                        $key2 += 1;
                        $sheet->setCellValue($col_array[$key2] . $row_counter, $window->compensable);
                        $sheet->mergeCells($col_array[$key2] . $row_counter . ':' . $col_array[$key2] . $row_counter + $x);
                    }
                    if (isset($request['Compensable_Transfer'])) {
                        $key2 += 1;
                        $sheet->setCellValue($col_array[$key2] . $row_counter, $window->transfer_compensable);
                        $sheet->mergeCells($col_array[$key2] . $row_counter . ':' . $col_array[$key2] . $row_counter + $x);
                    }
                    $sheet->mergeCells('A' . $row_counter . ':' . 'A' . $row_counter + $x);
                    $sheet->mergeCells('B' . $row_counter . ':' . 'B' . $row_counter + $x);
                    $sheet->mergeCells('C' . $row_counter . ':' . 'C' . $row_counter + $x);
                    $sheet->mergeCells('D' . $row_counter . ':' . 'D' . $row_counter + $x);
                    if (isset($variance_array_col['IMP'])) {
                        $sheet->mergeCells($variance_array_col['IMP'] . $row_counter . ':' . $variance_array_col['IMP'] . $row_counter + $x);
                    }
                    if (isset($variance_array_col['UPD'])) {
                        $sheet->mergeCells($variance_array_col['UPD'] . $row_counter . ':' . $variance_array_col['UPD'] . $row_counter + $x);
                    }
                    if (isset($variance_array_col['But'])) {
                        $sheet->mergeCells($variance_array_col['But'] . $row_counter . ':' . $variance_array_col['But'] . $row_counter + $x);
                    }
                    $windowStartRow = $row_counter;
                    $windowEndRow   = $row_counter + $x;
                    // $fillColor      = ($window->id % 2 == 1) ? 'FFF2CC' : 'D9E1F2'; // odd: yellow, even: blue
                    if ($color) {
                        $color = false;
                        $sheet->getStyle('A' . $windowStartRow . ':' . $col_array[$key] . $windowEndRow)->applyFromArray([
                            'fill' => [
                                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'D9E1F2'],
                            ],
                        ]);
                    } else {
                        $color = true;
                    }

                    $x++;
                    $first_w = false;
                }
                $row_counter = $row_counter + $x - 1;
                // Data rows styling
                // $rowCount = count($this->windows) * 4 + 2;
                $sheet->getStyle('A3:' . $col_array[$key] . $row_counter)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                ]);

                // Auto column width
                // foreach (range('A', $col_array[$key]) as $col) {
                //     $sheet->getColumnDimension($col)->setAutoSize(true);
                // }

                // Set row heights
                $sheet->getRowDimension(2)->setRowHeight(15);
                for ($i = 3; $i <= $row_counter; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(20);
                }
            },
        ];
    }
}
