<?php
namespace App\Http\Controllers\Dashboard;

use App\Exports\WindowsLedgerExport;
use App\Http\Controllers\ApiController;
use App\Models\Activity;
use App\Models\CalculationMethod;
use App\Models\DrivingActivity;
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
            'WhatIfCompensableExceededWindowDuration'  => 'nullable|in:1,2',
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

}
