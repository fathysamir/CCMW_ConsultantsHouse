<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends ApiController
{
    public function index(Request $request)
    {
        $all_activities = Activity::where('project_id', auth()->user()->current_project_id)
            ->orderByRaw('CAST(REGEXP_SUBSTR(act_id, "[0-9]+") AS UNSIGNED)')
            ->get();
        return view('project_dashboard.window_analysis.activities', compact('all_activities'));

    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'act_id' => 'required|string|min:2|max:100',
            'name'   => 'required|string|min:3|max:255',
        ]);
        $exists = Activity::where('act_id', $validated['act_id'])

            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Activity ID already exists for another record.');
        }
        // Create slug based on name or act_id
        do {
            $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (Activity::where('slug', $slug)->exists());

        $activity             = new Activity();
        $activity->act_id     = $validated['act_id'];
        $activity->name       = $validated['name'];
        $activity->slug       = $slug;
        $activity->project_id = auth()->user()->current_project_id;
        $activity->save();

        return redirect()->back()->with('success', 'Activity created successfully!');
    }

    public function update(Request $request, $slug)
    {
        $validated = $request->validate([
            'act_id' => 'required|string|min:2|max:100',
            'name'   => 'required|string|min:3|max:255',
        ]);

        $activity = Activity::where('slug', $slug)->firstOrFail();

        // Ensure act_id uniqueness (except current)
        $exists = Activity::where('act_id', $validated['act_id'])
            ->where('id', '!=', $activity->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Activity ID already exists for another record.');
        }

        $activity->act_id = $validated['act_id'];
        $activity->name   = $validated['name'];
        $activity->save();

        return redirect()->back()->with('success', 'Activity updated successfully!');
    }

    public function delete($slug)
    {
        Activity::where('slug', $slug)->delete();
        return redirect()->back()->with('success', 'Activity deleted successfully!');
    }
}
