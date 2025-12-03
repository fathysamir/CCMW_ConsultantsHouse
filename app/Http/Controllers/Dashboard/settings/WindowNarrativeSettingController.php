<?php
namespace App\Http\Controllers\Dashboard\settings;

use App\Http\Controllers\ApiController;
use App\Models\WindowNarrativeSetting;
use Illuminate\Http\Request;

class WindowNarrativeSettingController extends ApiController
{
    public function index(Request $request)
    {
        $user     = auth()->user();
        $settings = WindowNarrativeSetting::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->orderBy('id', 'asc')->get();
        if (auth()->user()->current_account_id == null) {
            return view('dashboard.window_narrative_settings.index', compact('settings'));
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            $settings = WindowNarrativeSetting::where('account_id', null)->where('project_id', null)->orderBy('id', 'asc')->get();
            foreach ($settings as $setting) {
                $exist = WindowNarrativeSetting::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('para_id', $setting->para_id)->first();
                if (! $exist) {
                    WindowNarrativeSetting::create(['account_id' => $user->current_account_id, 'project_id' => $user->current_project_id, 'para_id' => $setting->para_id, 'description' => $setting->description, 'location' => $setting->location, 'paragraph_default' => $setting->paragraph_default, 'paragraph' => $setting->paragraph]);
                }
            }
            $settings = WindowNarrativeSetting::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->orderBy('id', 'asc')->get();
            return view('account_dashboard.window_narrative_settings.index', compact('settings'));
        } else {
            $settings = WindowNarrativeSetting::where('account_id', $user->current_account_id)->where('project_id', null)->orderBy('id', 'asc')->get();
            foreach ($settings as $setting) {
                $exist = WindowNarrativeSetting::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('para_id', $setting->para_id)->first();
                if (! $exist) {
                    WindowNarrativeSetting::create(['account_id' => $user->current_account_id, 'project_id' => $user->current_project_id, 'para_id' => $setting->para_id, 'description' => $setting->description, 'location' => $setting->location, 'paragraph_default' => $setting->paragraph_default, 'paragraph' => $setting->paragraph]);
                }
            }
            $settings = WindowNarrativeSetting::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->orderBy('id', 'asc')->get();

            return view('project_dashboard.window_narrative_settings.index', compact('settings'));
        }

    }
    public function edit($id)
    {
        $setting = WindowNarrativeSetting::findOrFail($id);
        if (auth()->user()->current_account_id == null) {
            return view('dashboard.window_narrative_settings.edit', compact('setting'));
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return view('account_dashboard.window_narrative_settings.edit', compact('setting'));
        } else {
            return view('project_dashboard.window_narrative_settings.edit', compact('setting'));
        }
    }

    public function update(Request $request, $id)
    {
        WindowNarrativeSetting::where('id', $id)->update([
            'paragraph' => $request->paragraph,
        ]);
        if (auth()->user()->current_account_id == null) {
            $setting                    = WindowNarrativeSetting::findOrFail($id);
            $setting->paragraph_default = $request->paragraph;
            $setting->save();
            return redirect('/accounts/window/narrative-settings')->with('success', 'Narrative Settings updated successfully.');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return redirect('/account/window/narrative-settings')->with('success', 'Narrative Settings updated successfully.');
        } else {
            return redirect('/project/window/narrative-settings')->with('success', 'Narrative Settings updated successfully.');
        }
    }

}
