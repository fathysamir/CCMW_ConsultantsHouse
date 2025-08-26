<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\ParaWise;
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
        return view('project_dashboard.para_wise_analysis.index', compact('all_para_wises'));

    }
}
