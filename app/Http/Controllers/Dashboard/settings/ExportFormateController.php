<?php
namespace App\Http\Controllers\Dashboard\settings;

use App\Http\Controllers\ApiController;
use App\Models\ExportFormate;
use Illuminate\Http\Request;

class ExportFormateController extends ApiController
{
    public function edit(Request $request)
    {
        $user    = auth()->user();
        $formate = ExportFormate::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->first();
        if ($formate) {
            $formate_values = $formate->value = json_decode($formate->value, true);
        } else {
            $formate_values = null;
        }
        if (auth()->user()->current_account_id == null) {
            return view('dashboard.export_formate.edit', compact('formate_values'));
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return view('account_dashboard.export_formate.edit', compact('formate_values'));
        } else {
            return view('project_dashboard.export_formate.edit', compact('formate_values'));
        }

    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data    = $request->except('_token');
        
        $json    = json_encode($data, JSON_PRETTY_PRINT);
        $formate = ExportFormate::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->first();
        if ($formate) {
            $formate->value = $json;
            $formate->save();
        }else{
            ExportFormate::create(['account_id'=>$user->current_account_id,'project_id'=> $user->current_project_id,'value' => $json]);
        }
        if (auth()->user()->current_account_id == null) {
            return redirect('/accounts/export-formate-settings')->with('success', 'Export Formate Settings updated successfully.');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return redirect('/account/export-formate-settings')->with('success', 'Export Formate Settings updated successfully.');
        } else {
            return redirect('/project/export-formate-settings')->with('success', 'Export Formate Settings updated successfully.');
        }
    }
}
