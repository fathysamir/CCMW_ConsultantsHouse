<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Activity;
use App\Models\DrivingActivity;
use App\Models\Milestone;
use App\Models\ProjectFile;
use App\Models\Window;
use Carbon\Carbon;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WindowController extends ApiController
{
    public function index(Request $request)
    {
        $all_windows = Window::where('project_id', auth()->user()->current_project_id)
            ->orderByRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED)')
            ->get();
        return view('project_dashboard.window_analysis.windows', compact('all_windows'));

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
            'slug'                           => 'required|string|exists:windows,slug',
            'program'                        => 'required|string',
            'driving_activities'             => 'required|array|min:1',
            'driving_activities.*.activity'  => 'required|integer|exists:activities,id',
            'driving_activities.*.date'      => 'required|date',
        ]);
        $window = Window::where('slug', $request->slug)->firstOrFail();

        DB::beginTransaction();
        try {
            $array_IDs = [];
            foreach ($request->driving_activities as $activity) {
                $d_a = DrivingActivity::where('project_id', $window->project_id)->where('activity_id', $activity['activity'])->where('milestone_id', ($activity['milestone']??null))->where('window_id', $window->id)->where('program', $request->program)->first();
                if ($d_a) {

                    $d_a->ms_come_date = $activity['date'];
                    $d_a->save();

                } else {
                    $d_a = DrivingActivity::create([
                        'project_id'   => $window->project_id,
                        'activity_id'  => $activity['activity'],
                        'milestone_id' => $activity['milestone']?? null,
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

}
