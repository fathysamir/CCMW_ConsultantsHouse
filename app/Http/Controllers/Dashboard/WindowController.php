<?php
namespace App\Http\Controllers\Dashboard;

use App\Exports\WindowsLedgerExport;
use App\Http\Controllers\ApiController;
use App\Models\Activity;
use App\Models\CalculationMethod;
use App\Models\DrivingActivity;
use App\Models\ExportFormate;
use App\Models\Milestone;
use App\Models\ProjectFile;
use App\Models\Window;
use App\Models\WindowNarrativeSetting;
use App\Services\CalculationMethodService;
use Carbon\Carbon;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class WindowController extends ApiController
{
    protected $calc_method;

    public function __construct(CalculationMethodService $calc_method)
    {
        $this->calc_method = $calc_method;
    }
    public function last_ms($project_id, $window_id)
    {
        $milestones_IDs = Milestone::where('project_id', auth()->user()->current_project_id)->pluck('id')->toArray();
        $date           = $this->calc_method->comp_date($project_id, $window_id, 'UPD', $milestones_IDs);
        $row            = DrivingActivity::where('project_id', $project_id)
            ->where('window_id', $window_id)
            ->where('program', 'UPD')->where('ms_come_date', $date)->orderByDesc('id')
            ->first();

        return $row;
    }
    public function getKeyOfLatestDate($array)
    {
        $latestKey  = null;
        $latestDate = null;

        foreach ($array as $key => $date) {
            if (! $latestDate || strtotime($date) > strtotime($latestDate)) {
                $latestDate = $date;
                $latestKey  = $key;
            }
        }

        return $latestKey;
    }
    public function index(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        if ($request->milestone) {
            $milestone_id = $request->milestone;
        } else {
            $miles = Milestone::where('project_id', auth()->user()->current_project_id)->first();
            if ($miles) {
                $milestone_id = $miles->id;
            } else {
                $milestone_id = null;
            }
        }
        $milestones  = Milestone::where('project_id', auth()->user()->current_project_id)->get();
        $all_windows = Window::where('project_id', auth()->user()->current_project_id)
            ->orderByRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED)')
            ->get();
        $last_ms = null;
        if ($milestone_id) {
            $last_ms = null;
            $rows    = [];
            foreach ($all_windows as $window) {
                $BASs = DrivingActivity::where('project_id', auth()->user()->current_project_id)->where('window_id', $window->id)->where('program', 'BAS')->count();
                $IMPs = DrivingActivity::where('project_id', auth()->user()->current_project_id)->where('window_id', $window->id)->where('program', 'IMP')->count();
                $UPDs = DrivingActivity::where('project_id', auth()->user()->current_project_id)->where('window_id', $window->id)->where('program', 'UPD')->count();
                $BUTs = DrivingActivity::where('project_id', auth()->user()->current_project_id)->where('window_id', $window->id)->where('program', 'BUT')->count();
                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                    $window->culpable  = $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $milestone_id);
                    $window->excusable = $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $milestone_id);
                    if ($BUTs > 0) {
                        $window->compensable          = $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                        $window->transfer_compensable = $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);

                    }
                    $window->save();
                }
                if ($UPDs > 0) {
                    $row            = $this->last_ms(auth()->user()->current_project_id, $window->id);
                    $rows[$row->id] = $row->ms_come_date;
                }

            }
            if (count($rows) > 0) {
                $lastRowID = $this->getKeyOfLatestDate($rows);
                $last_ms   = DrivingActivity::where('id', $lastRowID)->first()->milestone_id;
            }

        }
        $all_windows = Window::where('project_id', auth()->user()->current_project_id)
            ->orderByRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED)')
            ->get();
        // $last_ms=11;
        return view('project_dashboard.window_analysis.windows', compact('all_windows', 'milestones', 'milestone_id', 'last_ms'));

    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'window_no'  => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        // Ensure unique window number within same project
        $exists = Window::where('project_id', auth()->user()->current_project_id)
            ->where('no', $validated['window_no'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Window number already exists for this project.');
        }

        // Generate unique slug
        do {
            $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (Window::where('slug', $slug)->exists());

        // Calculate duration in days
        $start    = Carbon::parse($validated['start_date']);
        $end      = Carbon::parse($validated['end_date']);
        $duration = $start->diffInDays($end) + 1; // +1 to include both start & end days

        // Save new window
        $window             = new Window();
        $window->no         = $validated['window_no'];
        $window->start_date = $validated['start_date'];
        $window->end_date   = $validated['end_date'];
        $window->duration   = $duration;
        $window->slug       = $slug;
        $window->project_id = auth()->user()->current_project_id;
        $window->save();

        return redirect()->back()->with('success', 'Window created successfully!');
    }
    public function update(Request $request, $slug)
    {
        $validated = $request->validate([
            'window_no'  => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $window = Window::where('slug', $slug)->firstOrFail();

        // Ensure no duplication for other windows in same project
        $exists = Window::where('project_id', auth()->user()->current_project_id)
            ->where('no', $validated['window_no'])
            ->where('id', '!=', $window->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Window number already exists for another record.');
        }

        // Calculate new duration
        $start    = Carbon::parse($validated['start_date']);
        $end      = Carbon::parse($validated['end_date']);
        $duration = $start->diffInDays($end) + 1;

        // Update fields
        $window->no         = $validated['window_no'];
        $window->start_date = $validated['start_date'];
        $window->end_date   = $validated['end_date'];
        $window->duration   = $duration;
        $window->save();

        return redirect()->back()->with('success', 'Window updated successfully!');
    }
    public function delete($slug)
    {
        Window::where('slug', $slug)->delete();
        return redirect()->back()->with('success', 'Window deleted successfully!');
    }

    ////////////////////////////////////////////////////////////////
    public function store_driving_activity(Request $request)
    {
        $validated = $request->validate([
            'slug'                          => 'required|string|exists:windows,slug',
            'program'                       => 'required|string',
            'driving_activities'            => 'required|array|min:1',
            'driving_activities.*.activity' => 'required|integer|exists:activities,id',
            'driving_activities.*.date'     => 'required|date',
        ]);
        $window = Window::where('slug', $request->slug)->firstOrFail();

        DB::beginTransaction();
        try {
            $array_IDs = [];
            foreach ($request->driving_activities as $activity) {
                $d_a = DrivingActivity::where('project_id', $window->project_id)->where('activity_id', $activity['activity'])->where('milestone_id', ($activity['milestone'] ?? null))->where('window_id', $window->id)->where('program', $request->program)->first();
                if ($d_a) {

                    $d_a->ms_come_date = $activity['date'];
                    $d_a->save();

                } else {
                    $d_a = DrivingActivity::create([
                        'project_id'   => $window->project_id,
                        'activity_id'  => $activity['activity'],
                        'milestone_id' => $activity['milestone'] ?? null,
                        'window_id'    => $window->id, // adjust if available
                        'program'      => $request->program,
                        'ms_come_date' => $activity['date'],
                        'liability'    => $activity['liability'] ?? null, // optional, set if you use it
                        'file_id'      => $activity['file'] ?? null,      // optional, set if needed
                    ]);
                }
                $array_IDs[] = $d_a->id;
            }

            DB::commit();
            DrivingActivity::where('project_id', $window->project_id)->where('window_id', $window->id)->where('program', $request->program)->whereNotIn('id', $array_IDs)->delete();

            if ($request->file('bas_snip')) {
                $bas_snip_image = getFirstMediaUrl($window, $window->BASSnipCollection);
                if ($bas_snip_image != null) {
                    deleteMedia($window, $window->BASSnipCollection);
                }
                uploadMedia($request->bas_snip, $window->BASSnipCollection, $window);

            }
            if ($request->file('frag_snip')) {
                $frag_snip_image = getFirstMediaUrl($window, $window->FRAGSnipCollection);
                if ($frag_snip_image != null) {
                    deleteMedia($window, $window->FRAGSnipCollection);
                }
                uploadMedia($request->frag_snip, $window->FRAGSnipCollection, $window);

            }
            if ($request->file('imp_snip')) {
                $imp_snip_image = getFirstMediaUrl($window, $window->IMPSnipCollection);
                if ($imp_snip_image != null) {
                    deleteMedia($window, $window->IMPSnipCollection);
                }
                uploadMedia($request->imp_snip, $window->IMPSnipCollection, $window);

            }
            if ($request->file('upd_snip')) {
                $upd_snip_image = getFirstMediaUrl($window, $window->UPDSnipCollection);
                if ($upd_snip_image != null) {
                    deleteMedia($window, $window->UPDSnipCollection);
                }
                uploadMedia($request->upd_snip, $window->UPDSnipCollection, $window);

            }
            if ($request->file('but_snip')) {
                $but_snip_image = getFirstMediaUrl($window, $window->BUTSnipCollection);
                if ($but_snip_image != null) {
                    deleteMedia($window, $window->BUTSnipCollection);
                }
                uploadMedia($request->but_snip, $window->BUTSnipCollection, $window);

            }
            return redirect()->back()->with('success', 'Window Updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save driving activities!');

        }
    }
    public function get_driving_activities(Request $request)
    {

        $window = Window::where('slug', $request->slug)->first();
        if ($window) {
            $activities  = Activity::where('project_id', $window->project_id)->get();
            $milestones  = Milestone::where('project_id', $window->project_id)->get();
            $claim_files = ProjectFile::where('project_id', $window->project_id)->whereHas('folder', function ($q) {
                $q->where('potential_impact', '1');
            })->get();

            if ($request->program == 'BAS') {
                if ($request->prev) {
                    $DrivingActivities = DrivingActivity::where('window_id', $window->id)->where('program', $request->prev)->get();
                    if ($DrivingActivities->isEmpty()) {
                        return response()->json([
                            'success' => false,

                        ]);
                    }
                    $html = view('project_dashboard.window_analysis.partial.bas.prev_bas', compact('DrivingActivities', 'activities', 'milestones'))->render();
                } else {
                    $DrivingActivities = DrivingActivity::where('window_id', $window->id)->where('program', $request->program)->get();
                    $bas_snip          = $window->bas_snip;
                    $html              = view('project_dashboard.window_analysis.partial.bas.bas_program', compact('DrivingActivities', 'activities', 'milestones', 'bas_snip'))->render();
                }

            } elseif ($request->program == 'IMP') {

                if ($request->prev) {

                    $DrivingActivities = DrivingActivity::where('window_id', $window->id)->where('program', $request->prev)->get();
                    if ($DrivingActivities->isEmpty()) {
                        return response()->json([
                            'success' => false,

                        ]);
                    }
                    $html = view('project_dashboard.window_analysis.partial.imp.prev_imp', compact('DrivingActivities', 'activities', 'milestones', 'claim_files'))->render();
                } else {
                    $DrivingActivities = DrivingActivity::where('window_id', $window->id)->where('program', $request->program)->get();
                    $imp_snip          = $window->imp_snip;
                    $frag_snip         = $window->frag_snip;
                    $html              = view('project_dashboard.window_analysis.partial.imp.imp_program', compact('DrivingActivities', 'activities', 'milestones', 'claim_files', 'imp_snip', 'frag_snip'))->render();
                }

            } elseif ($request->program == 'UPD') {

                if ($request->prev) {

                    $DrivingActivities = DrivingActivity::where('window_id', $window->id)->where('program', $request->prev)->get();
                    if ($DrivingActivities->isEmpty()) {
                        return response()->json([
                            'success' => false,

                        ]);
                    }
                    $html = view('project_dashboard.window_analysis.partial.upd.prev_upd', compact('DrivingActivities', 'activities', 'milestones', 'claim_files'))->render();
                } else {
                    $DrivingActivities = DrivingActivity::where('window_id', $window->id)->where('program', $request->program)->get();
                    $upd_snip          = $window->upd_snip;
                    $html              = view('project_dashboard.window_analysis.partial.upd.upd_program', compact('DrivingActivities', 'activities', 'milestones', 'claim_files', 'upd_snip'))->render();
                }

            } elseif ($request->program == 'BUT') {

                $DrivingActivities = DrivingActivity::where('window_id', $window->id)->where('program', $request->program)->get();
                $but_snip          = $window->but_snip;
                $html              = view('project_dashboard.window_analysis.partial.but.but_program', compact('DrivingActivities', 'activities', 'but_snip'))->render();

            }
            return response()->json([
                'success' => true,
                'html'    => $html,
            ]);
        } else {
            return response()->json([
                'success' => false,

            ]);
        }

    }

    ////////////////////////////////////////////////////////////
    public function update_calculation_method(Request $request)
    {
        $user      = auth()->user();
        $validated = $request->validate([
            'InCaseOfConcurrency'                      => 'nullable|in:1,2',
            'CompensabilityCalculation'                => 'nullable|in:1,2',
            'WhatIfCompensableExceededWindowDuration'  => 'nullable|in:1,2,3',
            'HowToDealWithMitigation'                  => 'nullable|in:1,2',
            'WhatIfCriticalPathShiftedToCulpableInUPD' => 'nullable|in:1,2',
            'BasedOnWhichProgramme'                    => 'nullable|in:1,2',
            'WhatIfUPDExtendedAsExcusableTookLonger'   => 'nullable|in:1,2',
        ]);

        $projectId = $user->current_project_id;

        foreach ($validated as $key => $value) {
            CalculationMethod::updateOrCreate(
                [
                    'key'        => $key,
                    'project_id' => $projectId,
                ],
                [
                    'value' => $value,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Calculation methods updated successfully.',
        ]);

    }
    
    public function exportLedger(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $projectId     = auth()->user()->current_project_id;
        $projectFolder = 'projects/' . $projectId . '/temp';
        $path          = public_path($projectFolder);

        // Ensure base directory exists
        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        // Create unique temp directory
        $code      = Str::random(10);
        $directory = $path . '/' . $code;

        if (! file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Full path for saving Excel
        $fileName = 'windows_ledger.xlsx';
        $filePath = $directory . '/' . $fileName;

        // Generate Excel binary content
        $excelData = Excel::raw(new WindowsLedgerExport($request->all()), \Maatwebsite\Excel\Excel::XLSX);

        // Save to public folder manually
        file_put_contents($filePath, $excelData);

        // Return file URL to frontend
        return response()->json([
            'status'       => true,
            'download_url' => asset($projectFolder . '/' . $code . '/' . $fileName),
        ]);
    }

    public function exportNarrative(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $phpWord   = new PhpWord;
        $section   = $phpWord->addSection();
        $headingNo = $request->headingNo;
        $formate   = ExportFormate::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->first();
        if ($formate) {
            $formate_values = $formate->value = json_decode($formate->value, true);
        } else {
            $formate_values = null;
        }

        $GetStandardStylesP = [
            'name'      => $formate_values ? $formate_values['body']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['body']['standard']['size']) : 11,
            'bold'      => false,
            'italic'    => false,
            'underline' => 'none',

        ];
        $phpWord->addNumberingStyle(
            'multilevel',
            [
                'type'     => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels'   => [
                    ['Heading0', 'format' => 'decimal', 'text' => '%1.', 'start' => (int) $headingNo],
                    ['Heading1', 'format' => 'decimal', 'text' => '%1.%2', 'start' => 1],
                    ['Heading2', 'format' => 'decimal', 'text' => '%1.%2.%3', 'start' => 1,
                        'font' => $GetStandardStylesP['name'], // Tab position
                        'sz'   => (int) $GetStandardStylesP['size'] * 2,
                        'i'    => $GetStandardStylesP['italic'],
                        'b'    => $GetStandardStylesP['bold'],
                    ],
                    ['Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.%4', 'start' => 1],
                    ['Heading3', 'format' => 'decimal', 'text' => ''],
                ],
            ]
        );
        $phpWord->addNumberingStyle(
            'multilevel2',
            [
                'type'     => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels'   => [
                    ['Heading5', 'format' => 'decimal', 'text' => '%1.'],
                    ['Heading6', 'format' => 'decimal', 'text' => '%1.%2.'],
                    ['Heading7', 'format' => 'decimal', 'text' => '%1.%2.%3.'],

                    // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                    // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                ],
            ]
        );
        $phpWord->addNumberingStyle(
            'unordered',
            [
                'type'   => 'multilevel', // Use 'multilevel' for bullet points
                'levels' => [
                    ['format' => 'bullet', 'text' => '•', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '◦', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '▪', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '■', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '☑', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➤', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➥', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➟', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➡', 'alignment' => 'left'],

                ],
            ]
        );
        $GetStandardStylesH1 = [
            'name'      => $formate_values ? $formate_values['h1']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h1']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['h1']['standard']['size']) : 24,
            'bold'      => $formate_values ? ($formate_values['h1']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['h1']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h1']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH1 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h1']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h1']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h1']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h1']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['h1']['paragraph']['indentation']['hanging'] * 1436) : 1077,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h1']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h1']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h1']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h1']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];
        $GetStandardStylesH2 = [
            'name'      => $formate_values ? $formate_values['h2']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h2']['standard']['alignment'] : 'left',
            'size'      => $formate_values ? intval($formate_values['h2']['standard']['size']) : 16,
            'bold'      => $formate_values ? ($formate_values['h2']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['h2']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h2']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH2 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h2']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h2']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h2']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h2']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['h2']['paragraph']['indentation']['hanging'] * 1436) : 1077,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h2']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h2']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h2']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h2']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];
        $GetStandardStylesH3 = [
            'name'      => $formate_values ? $formate_values['h3']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h3']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['h3']['standard']['size']) : 14,
            'bold'      => $formate_values ? ($formate_values['h3']['standard']['bold'] == '1' ? true : false) : false,
            'italic'    => $formate_values ? ($formate_values['h3']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h3']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH3 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h3']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h3']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h3']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h3']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['h3']['paragraph']['indentation']['hanging'] * 1436) : 1077,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h3']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h3']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h3']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h3']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];

        $GetStandardStylesSubtitle = [
            'name'      => $formate_values ? $formate_values['subtitle']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['subtitle']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['subtitle']['standard']['size']) : 14,
            'bold'      => $formate_values ? ($formate_values['subtitle']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['subtitle']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['subtitle']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleSubtitle = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['subtitle']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['subtitle']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['subtitle']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['subtitle']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['subtitle']['paragraph']['indentation']['hanging'] * 1436) : 0,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['subtitle']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['subtitle']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['subtitle']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['subtitle']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];

        $phpWord->addParagraphStyle('listParagraphStyle', [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['hanging'] * 1436) : 1077,
                'firstLine' => 0,
            ],
            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ]);
        $phpWord->addParagraphStyle('listParagraphStyle2', [
            'spaceBefore'       => 0,
            'spaceAfter'        => 20,
            'lineHeight'        => 1,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) + 350 : 1350,
                'hanging'   => 337.5,
                'firstLine' => 0,
            ],
            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ]);

        $GetStandardStylesFootNotes = [
            'name'      => $formate_values ? $formate_values['footnote']['standard']['name'] : 'Calibri',
            'alignment' => $formate_values ? $formate_values['footnote']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['footnote']['standard']['size']) : 9,
            'bold'      => $formate_values ? ($formate_values['footnote']['standard']['bold'] == '1' ? true : false) : false,
            'italic'    => $formate_values ? ($formate_values['footnote']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['footnote']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleFootNotes = [
            'spaceBefore' => $formate_values ? ((int) $formate_values['footnote']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'  => $formate_values ? ((int) $formate_values['footnote']['paragraph']['spaceAfter'] * 20) : 0,
            'lineHeight'  => $formate_values ? (float) $formate_values['footnote']['paragraph']['lineHeight'] : 1,
            'indentation' => [
                'left'      => $formate_values ? ((float) $formate_values['footnote']['paragraph']['indentation']['left'] * 1436) : 0,
                'hanging'   => $formate_values ? ((float) $formate_values['footnote']['paragraph']['indentation']['hanging'] * 1436) : 0,
                'firstLine' => 0,
            ],
        ];
        //dd($request->all());
        $phpWord->addTitleStyle(1, $GetStandardStylesH1, array_merge($GetParagraphStyleH1, ['numStyle' => 'multilevel', 'numLevel' => 0]));
        $phpWord->addTitleStyle(2, $GetStandardStylesH2, array_merge($GetParagraphStyleH2, ['numStyle' => 'multilevel', 'numLevel' => 1]));
        $phpWord->addTitleStyle(3, $GetStandardStylesH3, $GetParagraphStyleH3);
        $title = $request->title;
        if ($request->H_Include_LastMS) {
            $Milestone = Milestone::findOrFail($request->lastMS);
            $title .= ' (' . $Milestone->name . ')';
        }
        $title = str_replace('&', '&amp;', $title);
        $section->addTitle($title, 1);
        $all_windows = Window::where('project_id', auth()->user()->current_project_id)
            ->orderByRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED)')
            ->get();

        $start_w_id = $request->start_w;
        $end_w_id   = $request->end_w;

        $start_index = $all_windows->search(fn($w) => $w->id == $start_w_id);
        $end_index   = $all_windows->search(fn($w) => $w->id == $end_w_id);

        if ($start_index > $end_index) {
            [$start_index, $end_index] = [$end_index, $start_index];
        }

        $selected_windows = $all_windows->slice($start_index, $end_index - $start_index + 1)->values();
        //dd($selected_windows);
        $window_counter = 0;
        $perv_window    = null;

        foreach ($selected_windows as $window) {
            $BASs = DrivingActivity::where('project_id', auth()->user()->current_project_id)->where('window_id', $window->id)->where('program', 'BAS')->count();
            $IMPs = DrivingActivity::where('project_id', auth()->user()->current_project_id)->where('window_id', $window->id)->where('program', 'IMP')->count();
            $UPDs = DrivingActivity::where('project_id', auth()->user()->current_project_id)->where('window_id', $window->id)->where('program', 'UPD')->count();
            $BUTs = DrivingActivity::where('project_id', auth()->user()->current_project_id)->where('window_id', $window->id)->where('program', 'BUT')->count();

            $fnListOfDEs = false;
            $window_counter++;
            $figure_counter = 1;
            $section->addTitle('Window # ' . str_pad($window->no, 2, '0', STR_PAD_LEFT), 2);
            $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
            $existedList = false;
            $w_st_date   = date('d F Y', strtotime($window->start_date));
            $w_en_date   = date('d F Y', strtotime($window->end_date));
            $listItemRun->addText('From ' . $w_st_date . ' - To ' . $w_en_date, $GetStandardStylesP);
            if ($request->Include_BAS && $BASs > 0) {
                $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Base Program (W' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . '-BAS)';
                $subtitle = str_replace('&', '&amp;', $subtitle);
                $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
                if ($window_counter == 1) {
                    $B = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'B1')->first();
                } else {
                    $B = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'B2')->first();
                }
                //dd($B->paragraph);
                $paragraph  = str_replace('</p><p>', "\n", $B->paragraph);
                $paragraph  = str_replace('<span class="ql-cursor">\u{FEFF}</span>', "", $paragraph);
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';
                //dd($cleanParts);
                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {
                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }

                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnListOfDEs()') {
                            if (! empty(trim($string))) {
                                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                $string      = str_replace('&', '&amp;', $string);
                                $listItemRun->addText($string, $GetStandardStylesP);
                            }
                            $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            foreach ($files as $file) {
                                $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                $string              = $file->code . ': ' . $file->name;
                                $string              = str_replace('&', '&amp;', $string);
                                $unNestedListItemRun->addText($string, $GetStandardStylesP);
                            }
                            $string      = '';
                            $fnListOfDEs = true;
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }
                if (! empty(trim($string))) {
                    if ($fnListOfDEs) {
                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                            'indentation'       => [
                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                            ],
                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                        ]);
                        $string = str_replace('&', '&amp;', $string);
                        $listItemRun2->addTextBreak();
                        $lines     = explode("\n", trim($string));
                        $lastIndex = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun2->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun2->addTextBreak();
                            }
                        }
                        // $listItemRun2->addText($string, $GetStandardStylesP);
                        $fnListOfDEs = false;
                    } else {
                        $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                        $string      = str_replace('&', '&amp;', $string);
                        $lines       = explode("\n", trim($string));
                        $lastIndex   = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun->addTextBreak();
                            }
                        }
                        // $listItemRun->addText($string, $GetStandardStylesP);
                    }

                }

                $imgPath = getFirstMediaUrl($window, $window->BASSnipCollection, false);
                if ($imgPath) {
                    $fullImagePath = public_path($imgPath);

                    if (file_exists($fullImagePath)) {

                        $textRun = $section->addTextRun([
                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                            'indentation' => [
                                'left' => 0,
                            ],
                            'keepNext'    => true,
                        ]);
                        // Add Image
                        $shape = $textRun->addImage($fullImagePath, [
                            'width'     => 450,
                            'height'    => 200,
                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                        ]);

                        $textRun->addTextBreak(); // New line
                        $textRun->addText('Figure ' . $headingNo . '.' . $window_counter . '.' . $figure_counter . ' - W0' . $window->no . '-BAS Longest Path', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                            'alignment'                                                                                                                                     => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                            'size'                                                                                                                                          => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                            'bold'                                                                                                                                          => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                            'italic'                                                                                                                                        => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                            'underline'                                                                                                                                     => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']
                        ); // Add caption in italics
                        $figure_counter++;

                    }
                }

                $B          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'B3')->first();
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $B->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';
                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {
                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }
                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnListOfDEs()') {
                            if (! empty(trim($string))) {
                                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                $string      = str_replace('&', '&amp;', $string);
                                $listItemRun->addText($string, $GetStandardStylesP);
                            }
                            $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            foreach ($files as $file) {
                                $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                $string              = $file->code . ': ' . $file->name;
                                $string              = str_replace('&', '&amp;', $string);
                                $unNestedListItemRun->addText($string, $GetStandardStylesP);
                            }
                            $string      = '';
                            $fnListOfDEs = true;
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }
                if (! empty(trim($string))) {
                    if ($fnListOfDEs) {
                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                            'indentation'       => [
                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                            ],
                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                        ]);
                        $string = str_replace('&', '&amp;', $string);
                        $listItemRun2->addTextBreak();
                        $lines     = explode("\n", trim($string));
                        $lastIndex = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun2->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun2->addTextBreak();
                            }
                        }$fnListOfDEs = false;
                    } else {
                        $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                        $string      = str_replace('&', '&amp;', $string);
                        $lines       = explode("\n", trim($string));
                        $lastIndex   = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun->addTextBreak();
                            }
                        }
                    }

                }

            }
            if ($request->Include_Fragnet) {
                $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Fragnet';
                $subtitle = str_replace('&', '&amp;', $subtitle);
                $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
                $F          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'F1')->first();
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $F->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';

                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {

                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }
                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnListOfDEs()') {
                            if (! empty(trim($string))) {
                                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                $string      = str_replace('&', '&amp;', $string);
                                $listItemRun->addText($string, $GetStandardStylesP);
                            }
                            $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            foreach ($files as $file) {
                                $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                $string              = $file->code . ': ' . $file->name;
                                $string              = str_replace('&', '&amp;', $string);
                                $unNestedListItemRun->addText($string, $GetStandardStylesP);
                            }
                            $string      = '';
                            $fnListOfDEs = true;
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }
                if (! empty(trim($string))) {
                    if ($fnListOfDEs) {

                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                            'indentation'       => [
                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                            ],
                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                        ]);
                        $string = str_replace('&', '&amp;', $string);
                        $listItemRun2->addTextBreak();
                        $lines     = explode("\n", trim($string));
                        $lastIndex = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun2->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun2->addTextBreak();
                            }
                        }$fnListOfDEs = false;
                    } else {
                        $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                        $string      = str_replace('&', '&amp;', $string);
                        $lines       = explode("\n", trim($string));
                        $lastIndex   = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun->addTextBreak();
                            }
                        }
                    }

                }

                $imgPath = getFirstMediaUrl($window, $window->FRAGSnipCollection, false);
                if ($imgPath) {
                    $fullImagePath = public_path($imgPath);

                    if (file_exists($fullImagePath)) {

                        $textRun = $section->addTextRun([
                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                            'indentation' => [
                                'left' => 0,
                            ],
                            'keepNext'    => true,
                        ]);
                        // Add Image
                        $shape = $textRun->addImage($fullImagePath, [
                            'width'     => 450,
                            'height'    => 200,
                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                        ]);

                        $textRun->addTextBreak(); // New line
                        $textRun->addText('Figure ' . $headingNo . '.' . $window_counter . '.' . $figure_counter . ' - W0' . $window->no . '-Fragnet', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                            'alignment'                                                                                                                            => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                            'size'                                                                                                                                 => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                            'bold'                                                                                                                                 => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                            'italic'                                                                                                                               => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                            'underline'                                                                                                                            => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']
                        );
                        $figure_counter++;
                    }
                }

                $F          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'F2')->first();
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $F->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';

                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {

                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }
                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnListOfDEs()') {
                            if (! empty(trim($string))) {
                                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                $string      = str_replace('&', '&amp;', $string);
                                $listItemRun->addText($string, $GetStandardStylesP);
                            }
                            $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            foreach ($files as $file) {
                                $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                $string              = $file->code . ': ' . $file->name;
                                $string              = str_replace('&', '&amp;', $string);
                                $unNestedListItemRun->addText($string, $GetStandardStylesP);
                            }
                            $string      = '';
                            $fnListOfDEs = true;
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }
                if (! empty(trim($string))) {
                    if ($fnListOfDEs) {
                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                            'indentation'       => [
                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                            ],
                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                        ]);
                        $string = str_replace('&', '&amp;', $string);
                        $listItemRun2->addTextBreak();
                        $lines     = explode("\n", trim($string));
                        $lastIndex = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun2->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun2->addTextBreak();
                            }
                        }$fnListOfDEs = false;
                    } else {
                        $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                        $string      = str_replace('&', '&amp;', $string);
                        $lines       = explode("\n", trim($string));
                        $lastIndex   = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun->addTextBreak();
                            }
                        }
                    }

                }
            }
            if ($request->Include_IMP && $IMPs > 0) {
                $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Impacted Program (W' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . '-IMP)';
                $subtitle = str_replace('&', '&amp;', $subtitle);
                $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
                $I          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'I1')->first();
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $I->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';

                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {

                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }
                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnListOfDEs()') {
                            if (! empty(trim($string))) {
                                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                $string      = str_replace('&', '&amp;', $string);
                                $listItemRun->addText($string, $GetStandardStylesP);
                            }
                            $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            foreach ($files as $file) {
                                $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                $string              = $file->code . ': ' . $file->name;
                                $string              = str_replace('&', '&amp;', $string);
                                $unNestedListItemRun->addText($string, $GetStandardStylesP);
                            }
                            $string      = '';
                            $fnListOfDEs = true;
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }
                if (! empty(trim($string))) {
                    if ($fnListOfDEs) {
                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                            'indentation'       => [
                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                            ],
                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                        ]);
                        $string = str_replace('&', '&amp;', $string);
                        $listItemRun2->addTextBreak();
                        $lines     = explode("\n", trim($string));
                        $lastIndex = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun2->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun2->addTextBreak();
                            }
                        }$fnListOfDEs = false;
                    } else {
                        $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                        $string      = str_replace('&', '&amp;', $string);
                        $lines       = explode("\n", trim($string));
                        $lastIndex   = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun->addTextBreak();
                            }
                        }
                    }

                }
                $imgPath = getFirstMediaUrl($window, $window->IMPSnipCollection, false);
                if ($imgPath) {
                    $fullImagePath = public_path($imgPath);

                    if (file_exists($fullImagePath)) {

                        $textRun = $section->addTextRun([
                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                            'indentation' => [
                                'left' => 0,
                            ],
                            'keepNext'    => true,
                        ]);
                        // Add Image
                        $shape = $textRun->addImage($fullImagePath, [
                            'width'     => 450,
                            'height'    => 200,
                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                        ]);

                        $textRun->addTextBreak(); // New line
                        $textRun->addText('Figure ' . $headingNo . '.' . $window_counter . '.' . $figure_counter . ' - W0' . $window->no . '-IMP Longest Path', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                            'alignment'                                                                                                                                     => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                            'size'                                                                                                                                          => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                            'bold'                                                                                                                                          => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                            'italic'                                                                                                                                        => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                            'underline'                                                                                                                                     => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']
                        );
                        $figure_counter++;
                    }
                }
                $date1    = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'IMP', [$request->lastMS]);
                $date2    = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'BAS', [$request->lastMS]);
                $start    = Carbon::parse($date2);
                $end      = Carbon::parse($date1);
                $duration = $start->diffInDays($end, false);
                if ($duration <= $window->duration && $duration != 0) {
                    $I = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'I2')->first();

                } elseif ($duration > $window->duration && $duration <= ($window->duration + 4)) {
                    $I = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'I3')->first();

                } elseif ($duration > ($window->duration + 4)) {
                    $I = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'I4')->first();

                } elseif ($duration <= 0) {
                    $I = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'I5')->first();

                } else {
                    $I = null;
                }
                if ($I) {
                    $pattern    = '/<strong>(.*?)<\/strong>/i';
                    $parts      = preg_split($pattern, $I->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $cleanParts = array_map('strip_tags', $parts);
                    $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                    $cleanParts = array_values($cleanParts);
                    $string     = '';

                    foreach ($cleanParts as $index => $text) {
                        if (containsPlaceholder($text)) {

                            if ($text == 'fnWNo()') {

                                $string .= $window->no;
                            } elseif ($text == 'fnPrevWNo()') {
                                if ($perv_window) {
                                    $string .= $perv_window->no;
                                }
                            } elseif (strpos($text, 'fnCompDate(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x    = $match[1];
                                $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                                $date = date('d F Y', strtotime($date));
                                $string .= $date;
                            } elseif (strpos($text, 'fnDrivAct(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x          = $match[1];
                                $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                                $x          = 1;
                                foreach ($activities as $activity) {
                                    $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                    if (count($activities) - $x > 0) {
                                        if (count($activities) - $x == 1) {
                                            $string .= ' and ';
                                        } else {
                                            $string .= ',';
                                        }

                                    }
                                    $x++;
                                }
                            } elseif ($text == 'fnListOfDEs()') {
                                if (! empty(trim($string))) {
                                    $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                    $string      = str_replace('&', '&amp;', $string);
                                    $listItemRun->addText($string, $GetStandardStylesP);
                                }
                                $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                foreach ($files as $file) {
                                    $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                    $string              = $file->code . ': ' . $file->name;
                                    $string              = str_replace('&', '&amp;', $string);
                                    $unNestedListItemRun->addText($string, $GetStandardStylesP);
                                }
                                $string      = '';
                                $fnListOfDEs = true;
                            } elseif ($text == 'fnCulpable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnExcusable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnCompensable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                    $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                                }
                            } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x = $match[1];
                                if ($x === 'WNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                                } elseif ($x === 'PrevWNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                                }
                            }
                        } else {
                            $string .= $text;
                        }
                    }
                    if (! empty(trim($string))) {
                        if ($fnListOfDEs) {
                            $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                                'indentation'       => [
                                    'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                                ],
                                'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                            ]);
                            $string = str_replace('&', '&amp;', $string);
                            $listItemRun2->addTextBreak();
                            $lines     = explode("\n", trim($string));
                            $lastIndex = count($lines) - 1;

                            foreach ($lines as $index => $line) {
                                $listItemRun2->addText($line, $GetStandardStylesP);
                                if ($index !== $lastIndex) {
                                    $listItemRun2->addTextBreak();
                                }
                            }$fnListOfDEs = false;
                        } else {
                            $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                            $string      = str_replace('&', '&amp;', $string);
                            $lines       = explode("\n", trim($string));
                            $lastIndex   = count($lines) - 1;

                            foreach ($lines as $index => $line) {
                                $listItemRun->addText($line, $GetStandardStylesP);
                                if ($index !== $lastIndex) {
                                    $listItemRun->addTextBreak();
                                }
                            }
                        }

                    }
                }

            }
            if ($request->Include_UPD && $UPDs > 0) {
                $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Updated Program (W' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . '-UPD)';
                $subtitle = str_replace('&', '&amp;', $subtitle);
                $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
                $U          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'U1')->first();
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $U->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';
                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {

                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }
                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnListOfDEs()') {
                            if (! empty(trim($string))) {
                                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                $string      = str_replace('&', '&amp;', $string);
                                $listItemRun->addText($string, $GetStandardStylesP);
                            }
                            $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            foreach ($files as $file) {
                                $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                $string              = $file->code . ': ' . $file->name;
                                $string              = str_replace('&', '&amp;', $string);
                                $unNestedListItemRun->addText($string, $GetStandardStylesP);
                            }
                            $string      = '';
                            $fnListOfDEs = true;
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }
                if (! empty(trim($string))) {
                    if ($fnListOfDEs) {
                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                            'indentation'       => [
                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                            ],
                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                        ]);
                        $string = str_replace('&', '&amp;', $string);
                        $listItemRun2->addTextBreak();
                        $lines     = explode("\n", trim($string));
                        $lastIndex = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun2->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun2->addTextBreak();
                            }
                        }$fnListOfDEs = false;
                    } else {
                        $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                        $string      = str_replace('&', '&amp;', $string);
                        $lines       = explode("\n", trim($string));
                        $lastIndex   = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun->addTextBreak();
                            }
                        }
                    }

                }
                $imgPath = getFirstMediaUrl($window, $window->UPDSnipCollection, false);
                if ($imgPath) {
                    $fullImagePath = public_path($imgPath);

                    if (file_exists($fullImagePath)) {

                        $textRun = $section->addTextRun([
                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                            'indentation' => [
                                'left' => 0,
                            ],
                            'keepNext'    => true,
                        ]);
                        // Add Image
                        $shape = $textRun->addImage($fullImagePath, [
                            'width'     => 450,
                            'height'    => 200,
                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                        ]);

                        $textRun->addTextBreak(); // New line
                        $textRun->addText('Figure ' . $headingNo . '.' . $window_counter . '.' . $figure_counter . ' - W0' . $window->no . '-UPD Longest Path', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                            'alignment'                                                                                                                                     => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                            'size'                                                                                                                                          => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                            'bold'                                                                                                                                          => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                            'italic'                                                                                                                                        => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                            'underline'                                                                                                                                     => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']
                        );
                        $figure_counter++;
                    }
                }
                $date1                = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'UPD', [$request->lastMS]);
                $date2                = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'IMP', [$request->lastMS]);
                $start                = Carbon::parse($date2);
                $end                  = Carbon::parse($date1);
                $duration             = $start->diffInDays($end, false);
                $NoLiabitiy_Culpable  = $this->calc_method->activities_num(auth()->user()->current_project_id, $window->id, 'UPD', $request->lastMS, 'Culpable');
                $NoLiabitiy_Excusable = $this->calc_method->activities_num(auth()->user()->current_project_id, $window->id, 'UPD', $request->lastMS, 'Excusable');
                $NoLiabitiy_All       = $this->calc_method->activities_num(auth()->user()->current_project_id, $window->id, 'UPD', $request->lastMS);
                if ($duration == 0 && $NoLiabitiy_Culpable == 0) {
                    $U = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'U2')->first();

                } elseif ($duration == 0 && $NoLiabitiy_Culpable > 0 && $NoLiabitiy_Excusable > 0) {
                    $U = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'U3')->first();

                } elseif ($duration > 0 && $NoLiabitiy_Culpable == $NoLiabitiy_All) {
                    $U = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'U4')->first();

                } elseif ($duration > 0 && $NoLiabitiy_Culpable == 0) {
                    $U = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'U5')->first();

                } elseif ($duration > 0 && $NoLiabitiy_Culpable > 0 && $NoLiabitiy_Excusable > 0) {
                    $U = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'U6')->first();

                } elseif ($duration < 0 && $NoLiabitiy_Culpable == 0) {
                    $U = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'U7')->first();

                } elseif ($duration < 0 && $NoLiabitiy_Culpable == $NoLiabitiy_All) {
                    $U = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'U8')->first();

                } elseif ($duration < 0 && $NoLiabitiy_Culpable > 1 && $NoLiabitiy_Excusable > 1) {
                    $U = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'U9')->first();

                } else {
                    $U = null;
                }
                if ($U) {
                    $pattern    = '/<strong>(.*?)<\/strong>/i';
                    $parts      = preg_split($pattern, $U->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $cleanParts = array_map('strip_tags', $parts);
                    $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                    $cleanParts = array_values($cleanParts);
                    $string     = '';

                    foreach ($cleanParts as $index => $text) {
                        if (containsPlaceholder($text)) {

                            if ($text == 'fnWNo()') {

                                $string .= $window->no;
                            } elseif ($text == 'fnPrevWNo()') {
                                if ($perv_window) {
                                    $string .= $perv_window->no;
                                }
                            } elseif (strpos($text, 'fnCompDate(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x    = $match[1];
                                $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                                $date = date('d F Y', strtotime($date));
                                $string .= $date;
                            } elseif (strpos($text, 'fnDrivAct(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x          = $match[1];
                                $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                                $x          = 1;
                                foreach ($activities as $activity) {
                                    $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                    if (count($activities) - $x > 0) {
                                        if (count($activities) - $x == 1) {
                                            $string .= ' and ';
                                        } else {
                                            $string .= ',';
                                        }

                                    }
                                    $x++;
                                }
                            } elseif ($text == 'fnListOfDEs()') {
                                if (! empty(trim($string))) {
                                    $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                    $string      = str_replace('&', '&amp;', $string);
                                    $listItemRun->addText($string, $GetStandardStylesP);
                                }
                                $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                foreach ($files as $file) {
                                    $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                    $string              = $file->code . ': ' . $file->name;
                                    $string              = str_replace('&', '&amp;', $string);
                                    $unNestedListItemRun->addText($string, $GetStandardStylesP);
                                }
                                $string      = '';
                                $fnListOfDEs = true;
                            } elseif ($text == 'fnCulpable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnExcusable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnCompensable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                    $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                                }
                            } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x = $match[1];
                                if ($x === 'WNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                                } elseif ($x === 'PrevWNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                                }
                            }
                        } else {
                            $string .= $text;
                        }
                    }
                    if (! empty(trim($string))) {
                        if ($fnListOfDEs) {
                            $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                                'indentation'       => [
                                    'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                                ],
                                'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                            ]);
                            $string = str_replace('&', '&amp;', $string);
                            $listItemRun2->addTextBreak();
                            $lines     = explode("\n", trim($string));
                            $lastIndex = count($lines) - 1;

                            foreach ($lines as $index => $line) {
                                $listItemRun2->addText($line, $GetStandardStylesP);
                                if ($index !== $lastIndex) {
                                    $listItemRun2->addTextBreak();
                                }
                            }$fnListOfDEs = false;
                        } else {
                            $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                            $string      = str_replace('&', '&amp;', $string);
                            $lines       = explode("\n", trim($string));
                            $lastIndex   = count($lines) - 1;

                            foreach ($lines as $index => $line) {
                                $listItemRun->addText($line, $GetStandardStylesP);
                                if ($index !== $lastIndex) {
                                    $listItemRun->addTextBreak();
                                }
                            }
                        }

                    }
                }

            }
            if ($request->Include_BUT && $BUTs > 0) {
                $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - But-For Analysis (W' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . '-BUT)';
                $subtitle = str_replace('&', '&amp;', $subtitle);
                $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
                $NoLiabitiy_Culpable = $this->calc_method->activities_num(auth()->user()->current_project_id, $window->id, 'UPD', $request->lastMS, 'Culpable');
                $date1               = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'BUT', [$request->lastMS]);
                $date2               = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'BAS', [$request->lastMS]);
                if ($NoLiabitiy_Culpable > 1) {
                    $T = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'T1')->first();
                } elseif ($this->calc_method->compare_dates($date1, $date2) === 'after' && $NoLiabitiy_Culpable == 0) {
                    $T = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'T2')->first();
                } elseif (($this->calc_method->compare_dates($date1, $date2) === 'before' || $this->calc_method->compare_dates($date1, $date2) === 'equal') && $NoLiabitiy_Culpable == 0) {
                    $T = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'T3')->first();
                }
                if ($T) {
                    $pattern    = '/<strong>(.*?)<\/strong>/i';
                    $parts      = preg_split($pattern, $T->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $cleanParts = array_map('strip_tags', $parts);
                    $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                    $cleanParts = array_values($cleanParts);
                    $string     = '';

                    foreach ($cleanParts as $index => $text) {
                        if (containsPlaceholder($text)) {

                            if ($text == 'fnWNo()') {

                                $string .= $window->no;
                            } elseif ($text == 'fnPrevWNo()') {
                                if ($perv_window) {
                                    $string .= $perv_window->no;
                                }
                            } elseif (strpos($text, 'fnCompDate(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x    = $match[1];
                                $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                                $date = date('d F Y', strtotime($date));
                                $string .= $date;
                            } elseif (strpos($text, 'fnDrivAct(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x          = $match[1];
                                $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                                $x          = 1;
                                foreach ($activities as $activity) {
                                    $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                    if (count($activities) - $x > 0) {
                                        if (count($activities) - $x == 1) {
                                            $string .= ' and ';
                                        } else {
                                            $string .= ',';
                                        }

                                    }
                                    $x++;
                                }
                            } elseif ($text == 'fnListOfDEs()') {
                                if (! empty(trim($string))) {
                                    $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                    $string      = str_replace('&', '&amp;', $string);
                                    $listItemRun->addText($string, $GetStandardStylesP);
                                }
                                $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                foreach ($files as $file) {
                                    $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                    $string              = $file->code . ': ' . $file->name;
                                    $string              = str_replace('&', '&amp;', $string);
                                    $unNestedListItemRun->addText($string, $GetStandardStylesP);
                                }
                                $string      = '';
                                $fnListOfDEs = true;
                            } elseif ($text == 'fnCulpable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnExcusable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnCompensable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                    $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                                }
                            } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x = $match[1];
                                if ($x === 'WNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                                } elseif ($x === 'PrevWNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                                }
                            }
                        } else {
                            $string .= $text;
                        }
                    }
                    if (! empty(trim($string))) {
                        if ($fnListOfDEs) {
                            $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                                'indentation'       => [
                                    'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                                ],
                                'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                            ]);
                            $string = str_replace('&', '&amp;', $string);
                            $listItemRun2->addTextBreak();
                            $lines     = explode("\n", trim($string));
                            $lastIndex = count($lines) - 1;

                            foreach ($lines as $index => $line) {
                                $listItemRun2->addText($line, $GetStandardStylesP);
                                if ($index !== $lastIndex) {
                                    $listItemRun2->addTextBreak();
                                }
                            }$fnListOfDEs = false;
                        } else {
                            $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                            $string      = str_replace('&', '&amp;', $string);
                            $lines       = explode("\n", trim($string));
                            $lastIndex   = count($lines) - 1;

                            foreach ($lines as $index => $line) {
                                $listItemRun->addText($line, $GetStandardStylesP);
                                if ($index !== $lastIndex) {
                                    $listItemRun->addTextBreak();
                                }
                            }
                        }

                    }
                }

                $imgPath = getFirstMediaUrl($window, $window->BUTSnipCollection, false);
                if ($imgPath) {
                    $fullImagePath = public_path($imgPath);

                    if (file_exists($fullImagePath)) {

                        $textRun = $section->addTextRun([
                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                            'indentation' => [
                                'left' => 0,
                            ],
                            'keepNext'    => true,
                        ]);
                        // Add Image
                        $shape = $textRun->addImage($fullImagePath, [
                            'width'     => 450,
                            'height'    => 200,
                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                        ]);

                        $textRun->addTextBreak(); // New line
                        $textRun->addText('Figure ' . $headingNo . '.' . $window_counter . '.' . $figure_counter . ' - W0' . $window->no . '-BUT Longest Path', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                            'alignment'                                                                                                                                     => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                            'size'                                                                                                                                          => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                            'bold'                                                                                                                                          => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                            'italic'                                                                                                                                        => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                            'underline'                                                                                                                                     => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']
                        );
                        $figure_counter++;
                    }
                }
                if ($this->calc_method->compare_dates($date1, $date2) === 'after' && $NoLiabitiy_Culpable == 0) {
                    $T = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'T4')->first();
                }

                if ($T) {
                    $pattern    = '/<strong>(.*?)<\/strong>/i';
                    $parts      = preg_split($pattern, $T->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $cleanParts = array_map('strip_tags', $parts);
                    $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                    $cleanParts = array_values($cleanParts);
                    $string     = '';

                    foreach ($cleanParts as $index => $text) {
                        if (containsPlaceholder($text)) {

                            if ($text == 'fnWNo()') {

                                $string .= $window->no;
                            } elseif ($text == 'fnPrevWNo()') {
                                if ($perv_window) {
                                    $string .= $perv_window->no;
                                }
                            } elseif (strpos($text, 'fnCompDate(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x    = $match[1];
                                $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                                $date = date('d F Y', strtotime($date));
                                $string .= $date;
                            } elseif (strpos($text, 'fnDrivAct(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x          = $match[1];
                                $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                                $x          = 1;
                                foreach ($activities as $activity) {
                                    $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                    if (count($activities) - $x > 0) {
                                        if (count($activities) - $x == 1) {
                                            $string .= ' and ';
                                        } else {
                                            $string .= ',';
                                        }

                                    }
                                    $x++;
                                }
                            } elseif ($text == 'fnListOfDEs()') {
                                if (! empty(trim($string))) {
                                    $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                    $string      = str_replace('&', '&amp;', $string);
                                    $listItemRun->addText($string, $GetStandardStylesP);
                                }
                                $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                foreach ($files as $file) {
                                    $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                    $string              = $file->code . ': ' . $file->name;
                                    $string              = str_replace('&', '&amp;', $string);
                                    $unNestedListItemRun->addText($string, $GetStandardStylesP);
                                }
                                $string      = '';
                                $fnListOfDEs = true;
                            } elseif ($text == 'fnCulpable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnExcusable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnCompensable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                    $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                                }
                            } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x = $match[1];
                                if ($x === 'WNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                                } elseif ($x === 'PrevWNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                                }
                            }
                        } else {
                            $string .= $text;
                        }
                    }
                    if (! empty(trim($string))) {
                        if ($fnListOfDEs) {
                            $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                                'indentation'       => [
                                    'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                                ],
                                'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                            ]);
                            $string = str_replace('&', '&amp;', $string);
                            $listItemRun2->addTextBreak();
                            $lines     = explode("\n", trim($string));
                            $lastIndex = count($lines) - 1;

                            foreach ($lines as $index => $line) {
                                $listItemRun2->addText($line, $GetStandardStylesP);
                                if ($index !== $lastIndex) {
                                    $listItemRun2->addTextBreak();
                                }
                            }$fnListOfDEs = false;
                        } else {
                            $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                            $string      = str_replace('&', '&amp;', $string);
                            $lines       = explode("\n", trim($string));
                            $lastIndex   = count($lines) - 1;

                            foreach ($lines as $index => $line) {
                                $listItemRun->addText($line, $GetStandardStylesP);
                                if ($index !== $lastIndex) {
                                    $listItemRun->addTextBreak();
                                }
                            }
                        }

                    }
                }
            }
            if ($request->Include_Conclusion && $BUTs > 0) {
                $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Conclusion';
                $subtitle = str_replace('&', '&amp;', $subtitle);
                $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
                $C          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'C1')->first();
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $C->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';

                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {

                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }
                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnListOfDEs()') {
                            if (! empty(trim($string))) {
                                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                                $string      = str_replace('&', '&amp;', $string);
                                $listItemRun->addText($string, $GetStandardStylesP);
                            }
                            $files = $this->calc_method->fnListOfDEs(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            foreach ($files as $file) {
                                $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                $string              = $file->code . ': ' . $file->name;
                                $string              = str_replace('&', '&amp;', $string);
                                $unNestedListItemRun->addText($string, $GetStandardStylesP);
                            }
                            $string      = '';
                            $fnListOfDEs = true;
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }
                if (! empty(trim($string))) {
                    if ($fnListOfDEs) {
                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                            'indentation'       => [
                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,

                            ],
                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                        ]);
                        $string = str_replace('&', '&amp;', $string);
                        $listItemRun2->addTextBreak();
                        $lines     = explode("\n", trim($string));
                        $lastIndex = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun2->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun2->addTextBreak();
                            }
                        }$fnListOfDEs = false;
                    } else {
                        $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                        $string      = str_replace('&', '&amp;', $string);
                        $lines       = explode("\n", trim($string));
                        $lastIndex   = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $listItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $listItemRun->addTextBreak();
                            }
                        }
                    }

                }

                $phpWord->addTableStyle('CustomTable', [
                    'borderSize'       => 6,
                    'borderColor'      => '000000',
                    'cellMarginTop'    => 80,
                    'cellMarginBottom' => 80,
                    'cellAlignment'    => 'center',
                ]);

                $headerStyle = [
                    'bgColor'   => 'FFFF00',
                    'valign'    => 'center',
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                ];
                $paraCenter = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
                $cellCenter = [
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                    'valign'    => 'center',
                    'name'      => $formate_values ? $formate_values['body']['standard']['name'] : 'Arial',
                    'size'      => 9,

                ];

                $table = $section->addTable('CustomTable');

                $row1 = $table->addRow(
                    320,
                    ['exactHeight' => true]
                );
                $cell = $table->addCell(1200, $headerStyle);
                $cell->getStyle()->setVMerge('restart');
                $cell->addText("WNO", array_merge($cellCenter, ['bold' => true]), $paraCenter);

                $cell = $table->addCell(2000, $headerStyle);
                $cell->getStyle()->setVMerge('restart');
                $cell->addText("From", array_merge($cellCenter, ['bold' => true]), $paraCenter);

                $cell = $table->addCell(2000, $headerStyle);
                $cell->getStyle()->setVMerge('restart');
                $cell->addText("To", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                $cell_width = 0;
                if ($request->table_show_BAS && $BASs > 0) {
                    $cell_width += 2000;
                }
                if ($request->table_show_IMP && $IMPs > 0) {
                    $cell_width += 2000;
                }
                if ($request->table_show_UPD && $UPDs > 0) {
                    $cell_width += 2000;
                }
                if ($request->table_show_BUT && $BUTs > 0) {
                    $cell_width += 2000;
                }
                if ($cell_width > 0) {
                    $finish = $table->addCell($cell_width, $headerStyle);
                    $finish->getStyle()->setGridSpan(4);
                    $finish->addText("Finish Date", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                }

                $cell_width = 0;
                if ($request->table_show_Culpable && $BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                    $cell_width += 1100;
                }
                if ($request->table_show_Excusable && $BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                    $cell_width += 1100;
                }
                if ($request->table_show_Compensable && $BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                    $cell_width += 1100;
                }
                if ($perv_window) {
                    $compensableTransfer_perv_window = $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                } else {
                    $compensableTransfer_perv_window = 0;
                }
                $compensableTransfer_current_window = $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                if ($request->table_show_Compensable_transfer && $BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0 && ! ($compensableTransfer_perv_window == 0 && $compensableTransfer_current_window == 0)) {
                    $cell_width += 1300;
                }
                if ($cell_width > 0) {
                    $liability = $table->addCell($cell_width, $headerStyle);
                    $liability->getStyle()->setGridSpan(4);
                    $liability->addText("Liability", array_merge($cellCenter, ['bold' => true]), $paraCenter);

                }

                $row2 = $table->addRow(
                    1250
                );
                $cell = $table->addCell(1200, $headerStyle);
                $cell->getStyle()->setVMerge('continue');

                $cell = $table->addCell(2000, $headerStyle);
                $cell->getStyle()->setVMerge('continue');

                $cell = $table->addCell(2000, $headerStyle);
                $cell->getStyle()->setVMerge('continue');
                if ($request->table_show_BAS && $BASs > 0) {
                    $table->addCell(2000, $headerStyle)->addText("BAS", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                }
                if ($request->table_show_IMP && $IMPs > 0) {
                    $table->addCell(2000, $headerStyle)->addText("IMP", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                }
                if ($request->table_show_UPD && $UPDs > 0) {
                    $table->addCell(2000, $headerStyle)->addText("UPD", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                }
                if ($request->table_show_BUT && $BUTs > 0) {
                    $table->addCell(2000, $headerStyle)->addText("BUT", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                }
                $vertical = ['textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR];
                if ($request->table_show_Culpable && $BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                    $table->addCell(1100, $headerStyle + $vertical)->addText("Culpable", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                }
                if ($request->table_show_Excusable && $BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                    $table->addCell(1100, $headerStyle + $vertical)->addText("Excusable", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                }
                if ($request->table_show_Compensable && $BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                    $table->addCell(1100, $headerStyle + $vertical)->addText("Compensable", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                }
                if ($request->table_show_Compensable_transfer && $BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0 && ! ($compensableTransfer_perv_window == 0 && $compensableTransfer_current_window == 0)) {
                    $table->addCell(1300, $headerStyle + $vertical)->addText("Compensable Transfer", array_merge($cellCenter, ['bold' => true]), $paraCenter);
                }

                $row3 = $table->addRow(
                    300, // height in twips (approx ~0.4 inch)
                    ['exactHeight' => true]
                );
                $table->addCell(1200)->addText(str_pad($window->no, 2, '0', STR_PAD_LEFT), $cellCenter, $paraCenter);
                $table->addCell(2000)->addText(date('d-M-y', strtotime($window->start_date)), $cellCenter, $paraCenter);
                $table->addCell(2000)->addText(date('d-M-y', strtotime($window->end_date)), $cellCenter, $paraCenter);
                if ($request->table_show_BAS && $BASs > 0) {
                    $bas_date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'BAS', [$request->lastMS]);
                    $table->addCell(2000)->addText(date('d-M-y', strtotime($bas_date)), $cellCenter, $paraCenter);
                }
                if ($request->table_show_IMP && $IMPs > 0) {
                    $imp_date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'IMP', [$request->lastMS]);
                    $table->addCell(2000)->addText(date('d-M-y', strtotime($imp_date)), $cellCenter, $paraCenter);
                }
                if ($request->table_show_UPD && $UPDs > 0) {
                    $upd_date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'UPD', [$request->lastMS]);
                    $table->addCell(2000)->addText(date('d-M-y', strtotime($upd_date)), $cellCenter, $paraCenter);
                }
                if ($request->table_show_BUT && $BUTs > 0) {
                    $but_date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, 'BUT', [$request->lastMS]);
                    $table->addCell(2000)->addText(date('d-M-y', strtotime($but_date)), $cellCenter, $paraCenter);
                }

                if ($request->table_show_Culpable && $BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                    $table->addCell(1100)->addText($this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS), $cellCenter, $paraCenter);
                }
                if ($request->table_show_Excusable && $BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                    $table->addCell(1100)->addText($this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS), $cellCenter, $paraCenter);
                }
                if ($request->table_show_Compensable && $BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                    $table->addCell(1100)->addText($this->calc_method->compensable(auth()->user()->current_project_id, $window->id), $cellCenter, $paraCenter);
                }
                if ($request->table_show_Compensable_transfer && $BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0 && ! ($compensableTransfer_perv_window == 0 && $compensableTransfer_current_window == 0)) {
                    $table->addCell(1300)->addText($this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id), $cellCenter, $paraCenter);
                }

                $section->addText(
                    'Table ' . $headingNo . '.' . $window_counter . '.1 - W0' . $window->no . '-Conclusion',
                    [
                        'name'      => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                        'alignment' => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left',
                        'size'      => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                        'bold'      => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1') : false,
                        'italic'    => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1') : true,
                        'underline' => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',
                    ], [
                        'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                        'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                        'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                        'indentation' => [
                            'left' => 0,
                        ],
                        'keepNext'    => true,
                    ]
                );

                $C          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'C2')->first();
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $C->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';
                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {
                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }
                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }

                if (! empty(trim($string))) {

                    $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                    $string      = str_replace('&', '&amp;', $string);
                    $lines       = explode("\n", trim($string));
                    $lastIndex   = count($lines) - 1;

                    foreach ($lines as $index => $line) {
                        $listItemRun->addText($line, $GetStandardStylesP);
                        if ($index !== $lastIndex) {
                            $listItemRun->addTextBreak();
                        }
                    }

                }

                $C          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'C3')->first();
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $C->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';
                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {
                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }
                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }

                if (! empty(trim($string))) {

                    $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2');
                    $string              = str_replace('&', '&amp;', $string);
                    $lines               = explode("\n", trim($string));
                    $lastIndex           = count($lines) - 1;

                    foreach ($lines as $index => $line) {
                        $unNestedListItemRun->addText($line, $GetStandardStylesP);
                        if ($index !== $lastIndex) {
                            $unNestedListItemRun->addTextBreak();
                        }
                    }

                }
                $C          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'C4')->first();
                $pattern    = '/<strong>(.*?)<\/strong>/i';
                $parts      = preg_split($pattern, $C->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                $cleanParts = array_map('strip_tags', $parts);
                $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                $cleanParts = array_values($cleanParts);
                $string     = '';
                foreach ($cleanParts as $index => $text) {
                    if (containsPlaceholder($text)) {

                        if ($text == 'fnWNo()') {
                            $string .= $window->no;
                        } elseif ($text == 'fnPrevWNo()') {
                            if ($perv_window) {
                                $string .= $perv_window->no;
                            }
                        } elseif (strpos($text, 'fnCompDate(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x    = $match[1];
                            $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                            $date = date('d F Y', strtotime($date));
                            $string .= $date;
                        } elseif (strpos($text, 'fnDrivAct(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x          = $match[1];
                            $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                            $x          = 1;
                            foreach ($activities as $activity) {
                                $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                if (count($activities) - $x > 0) {
                                    if (count($activities) - $x == 1) {
                                        $string .= ' and ';
                                    } else {
                                        $string .= ',';
                                    }

                                }
                                $x++;
                            }
                        } elseif ($text == 'fnCulpable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnExcusable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                            }
                        } elseif ($text == 'fnCompensable()') {
                            if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                            }
                        } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                            preg_match('/\((.*?)\)/', $text, $match);
                            $x = $match[1];
                            if ($x === 'WNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                            } elseif ($x === 'PrevWNo') {
                                $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                            }
                        }
                    } else {
                        $string .= $text;
                    }
                }

                if (! empty(trim($string))) {

                    $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2');
                    $string              = str_replace('&', '&amp;', $string);
                    $lines               = explode("\n", trim($string));
                    $lastIndex           = count($lines) - 1;

                    foreach ($lines as $index => $line) {
                        $unNestedListItemRun->addText($line, $GetStandardStylesP);
                        if ($index !== $lastIndex) {
                            $unNestedListItemRun->addTextBreak();
                        }
                    }

                }

                if ($compensableTransfer_perv_window == 0 && $compensableTransfer_current_window == 0) {
                    $C          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'C5')->first();
                    $pattern    = '/<strong>(.*?)<\/strong>/i';
                    $parts      = preg_split($pattern, $C->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $cleanParts = array_map('strip_tags', $parts);
                    $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                    $cleanParts = array_values($cleanParts);
                    $string     = '';
                    foreach ($cleanParts as $index => $text) {
                        if (containsPlaceholder($text)) {

                            if ($text == 'fnWNo()') {
                                $string .= $window->no;
                            } elseif ($text == 'fnPrevWNo()') {
                                if ($perv_window) {
                                    $string .= $perv_window->no;
                                }
                            } elseif (strpos($text, 'fnCompDate(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x    = $match[1];
                                $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                                $date = date('d F Y', strtotime($date));
                                $string .= $date;
                            } elseif (strpos($text, 'fnDrivAct(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x          = $match[1];
                                $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                                $x          = 1;
                                foreach ($activities as $activity) {
                                    $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                    if (count($activities) - $x > 0) {
                                        if (count($activities) - $x == 1) {
                                            $string .= ' and ';
                                        } else {
                                            $string .= ',';
                                        }

                                    }
                                    $x++;
                                }
                            } elseif ($text == 'fnCulpable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnExcusable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnCompensable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                    $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                                }
                            } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x = $match[1];
                                if ($x === 'WNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                                } elseif ($x === 'PrevWNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                                }
                            }
                        } else {
                            $string .= $text;
                        }
                    }

                    if (! empty(trim($string))) {

                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2');
                        $string              = str_replace('&', '&amp;', $string);
                        $lines               = explode("\n", trim($string));
                        $lastIndex           = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $unNestedListItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $unNestedListItemRun->addTextBreak();
                            }
                        }

                    }
                }
                if ($compensableTransfer_perv_window == 1 && $compensableTransfer_current_window == 0) {
                    $C          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'C6')->first();
                    $pattern    = '/<strong>(.*?)<\/strong>/i';
                    $parts      = preg_split($pattern, $C->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $cleanParts = array_map('strip_tags', $parts);
                    $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                    $cleanParts = array_values($cleanParts);
                    $string     = '';
                    foreach ($cleanParts as $index => $text) {
                        if (containsPlaceholder($text)) {

                            if ($text == 'fnWNo()') {
                                $string .= $window->no;
                            } elseif ($text == 'fnPrevWNo()') {
                                if ($perv_window) {
                                    $string .= $perv_window->no;
                                }
                            } elseif (strpos($text, 'fnCompDate(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x    = $match[1];
                                $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                                $date = date('d F Y', strtotime($date));
                                $string .= $date;
                            } elseif (strpos($text, 'fnDrivAct(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x          = $match[1];
                                $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                                $x          = 1;
                                foreach ($activities as $activity) {
                                    $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                    if (count($activities) - $x > 0) {
                                        if (count($activities) - $x == 1) {
                                            $string .= ' and ';
                                        } else {
                                            $string .= ',';
                                        }

                                    }
                                    $x++;
                                }
                            } elseif ($text == 'fnCulpable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnExcusable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnCompensable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                    $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                                }
                            } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x = $match[1];
                                if ($x === 'WNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                                } elseif ($x === 'PrevWNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                                }
                            }
                        } else {
                            $string .= $text;
                        }
                    }

                    if (! empty(trim($string))) {

                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2');
                        $string              = str_replace('&', '&amp;', $string);
                        $lines               = explode("\n", trim($string));
                        $lastIndex           = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $unNestedListItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $unNestedListItemRun->addTextBreak();
                            }
                        }

                    }
                }
                if ($compensableTransfer_perv_window == 0 && $compensableTransfer_current_window == 1) {
                    $C          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'C7')->first();
                    $pattern    = '/<strong>(.*?)<\/strong>/i';
                    $parts      = preg_split($pattern, $C->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $cleanParts = array_map('strip_tags', $parts);
                    $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                    $cleanParts = array_values($cleanParts);
                    $string     = '';
                    foreach ($cleanParts as $index => $text) {
                        if (containsPlaceholder($text)) {

                            if ($text == 'fnWNo()') {
                                $string .= $window->no;
                            } elseif ($text == 'fnPrevWNo()') {
                                if ($perv_window) {
                                    $string .= $perv_window->no;
                                }
                            } elseif (strpos($text, 'fnCompDate(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x    = $match[1];
                                $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                                $date = date('d F Y', strtotime($date));
                                $string .= $date;
                            } elseif (strpos($text, 'fnDrivAct(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x          = $match[1];
                                $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                                $x          = 1;
                                foreach ($activities as $activity) {
                                    $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                    if (count($activities) - $x > 0) {
                                        if (count($activities) - $x == 1) {
                                            $string .= ' and ';
                                        } else {
                                            $string .= ',';
                                        }

                                    }
                                    $x++;
                                }
                            } elseif ($text == 'fnCulpable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnExcusable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnCompensable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                    $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                                }
                            } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x = $match[1];
                                if ($x === 'WNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                                } elseif ($x === 'PrevWNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                                }
                            }
                        } else {
                            $string .= $text;
                        }
                    }

                    if (! empty(trim($string))) {

                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2');
                        $string              = str_replace('&', '&amp;', $string);
                        $lines               = explode("\n", trim($string));
                        $lastIndex           = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $unNestedListItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $unNestedListItemRun->addTextBreak();
                            }
                        }

                    }
                }
                if ($compensableTransfer_perv_window == 1 && $compensableTransfer_current_window == 1) {
                    $C          = WindowNarrativeSetting::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('para_id', 'C8')->first();
                    $pattern    = '/<strong>(.*?)<\/strong>/i';
                    $parts      = preg_split($pattern, $C->paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $cleanParts = array_map('strip_tags', $parts);
                    $cleanParts = array_filter($cleanParts, fn($x) => trim($x) !== '');
                    $cleanParts = array_values($cleanParts);
                    $string     = '';
                    foreach ($cleanParts as $index => $text) {
                        if (containsPlaceholder($text)) {

                            if ($text == 'fnWNo()') {
                                $string .= $window->no;
                            } elseif ($text == 'fnPrevWNo()') {
                                if ($perv_window) {
                                    $string .= $perv_window->no;
                                }
                            } elseif (strpos($text, 'fnCompDate(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x    = $match[1];
                                $date = $this->calc_method->comp_date(auth()->user()->current_project_id, $window->id, $x, [$request->lastMS]);
                                $date = date('d F Y', strtotime($date));
                                $string .= $date;
                            } elseif (strpos($text, 'fnDrivAct(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x          = $match[1];
                                $activities = $this->calc_method->activities(auth()->user()->current_project_id, $window->id, $x, $request->lastMS);
                                $x          = 1;
                                foreach ($activities as $activity) {
                                    $string .= '[' . $activity->act_id . ': ' . $activity->name . ']';
                                    if (count($activities) - $x > 0) {
                                        if (count($activities) - $x == 1) {
                                            $string .= ' and ';
                                        } else {
                                            $string .= ',';
                                        }

                                    }
                                    $x++;
                                }
                            } elseif ($text == 'fnCulpable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->culpable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnExcusable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0) {
                                    $string .= $this->calc_method->excusable(auth()->user()->current_project_id, $window->id, $request->lastMS);
                                }
                            } elseif ($text == 'fnCompensable()') {
                                if ($BASs > 0 && $IMPs > 0 && $UPDs > 0 && $BUTs > 0) {
                                    $string .= $this->calc_method->compensable(auth()->user()->current_project_id, $window->id);
                                }
                            } elseif (strpos($text, 'fnCompensableTransfer(') !== false) {
                                preg_match('/\((.*?)\)/', $text, $match);
                                $x = $match[1];
                                if ($x === 'WNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $window->id);
                                } elseif ($x === 'PrevWNo') {
                                    $string .= $this->calc_method->compensableTransfer(auth()->user()->current_project_id, $perv_window->id);
                                }
                            }
                        } else {
                            $string .= $text;
                        }
                    }

                    if (! empty(trim($string))) {

                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2');
                        $string              = str_replace('&', '&amp;', $string);
                        $lines               = explode("\n", trim($string));
                        $lastIndex           = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $unNestedListItemRun->addText($line, $GetStandardStylesP);
                            if ($index !== $lastIndex) {
                                $unNestedListItemRun->addTextBreak();
                            }
                        }

                    }
                }
            }
            $perv_window = $window;
        }
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
        $path          = public_path($projectFolder);
        if (! file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $xxx       = rand(3, 10);
        $code      = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $xxx);
        $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

        if (! file_exists($directory)) {
            mkdir($directory, 0755, true); // true = create nested directories
        }
        // Save document
        // Define file path in public folder
        $clean_title = preg_replace('/[^A-Za-z0-9]+/', '_', $title);
        $fileName    = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $code . '_' . $clean_title . '.docx';
        $filePath    = public_path($fileName);

        // Save document to public folder
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);
        session(['zip_file' => $code]);

        return response()->json(['success' => true, 'download_url' => asset($fileName)]);

    }
}
