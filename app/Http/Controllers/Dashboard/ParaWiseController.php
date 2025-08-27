<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\ParaWise;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ParaWiseController extends ApiController
{
    public function para_wise_analysis()
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }

        $all_para_wises = ParaWise::where('project_id', auth()->user()->current_project_id)->get();
        $project        = Project::findOrFail(auth()->user()->current_project_id);
        $users          = $project->assign_users;
        return view('project_dashboard.para_wise_analysis.index', compact('all_para_wises', 'users'));

    }

    public function store(Request $request)
    {
        do {
            $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (ParaWise::where('slug', $slug)->exists());

        ParaWise::create(['slug' => $slug, 'project_id' => auth()->user()->current_project_id, 'title' => $request->title, 'user_id' => $request->user_id, 'percentage_complete' => $request->percentage_complete]);
        return redirect('/project/para-wise-analysis')->with('success', 'Para-wise Created successfully.');

    }

    public function update(Request $request, $id)
    {
        $para_wise                      = ParaWise::where('slug', $id)->first();
        $para_wise->title               = $request->title;
        $para_wise->user_id             = $request->user_id;
        $para_wise->percentage_complete = $request->percentage_complete;
        $para_wise->save();
        return redirect('/project/para-wise-analysis')->with('success', 'Para-wise Updated successfully.');

    }
}
