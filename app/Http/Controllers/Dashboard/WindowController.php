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

    /**
     * Update an existing window.
     */
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

        $phpWord->addTitleStyle(1, $GetStandardStylesH1, $GetParagraphStyleH1);
        $phpWord->addTitleStyle(2, $GetStandardStylesH2, $GetParagraphStyleH2);
        $phpWord->addTitleStyle(3, $GetStandardStylesH3, array_merge($GetParagraphStyleH3, ['numStyle' => 'multilevel', 'numLevel' => 1]));
        $title = $request->title;
        $title = str_replace('&', '&amp;', $title);
        $section->addTitle($title, 2);
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

        foreach ($selected_windows as $window) {
            
            $section->addTitle('Window # ' . str_pad($window->no, 2, '0', STR_PAD_LEFT), 3);
            $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
            $existedList = false;
            $w_st_date   = date('d F Y', strtotime($window->start_date));
            $w_en_date   = date('d F Y', strtotime($window->end_date));
            $listItemRun->addText('From ' . $w_st_date . ' - To ' . $w_en_date, $GetStandardStylesP);

            $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Base Program (W' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . '-BAS)';
            $subtitle = str_replace('&', '&amp;', $subtitle);
            $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);

            $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Fragnet Program (W' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . '-FRA)';
            $subtitle = str_replace('&', '&amp;', $subtitle);
            $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);

            $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Impacted Program (W' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . '-IMP)';
            $subtitle = str_replace('&', '&amp;', $subtitle);
            $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);

            $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Updated Program (W' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . '-UPD)';
            $subtitle = str_replace('&', '&amp;', $subtitle);
            $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);

            $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - But-For Program (W' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . '-BUT)';
            $subtitle = str_replace('&', '&amp;', $subtitle);
            $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);

            $subtitle = 'Window ' . str_pad($window->no, 2, '0', STR_PAD_LEFT) . ' - Conclusion';
            $subtitle = str_replace('&', '&amp;', $subtitle);
            $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
            
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
