<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Window;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);
        $duration = $start->diffInDays($end) + 1; // +1 to include both start & end days

        // Save new window
        $window = new Window();
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
        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);
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
}
