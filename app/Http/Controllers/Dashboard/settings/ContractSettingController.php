<?php

namespace App\Http\Controllers\Dashboard\settings;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\Account;
use App\Models\Category;
use App\Models\ContractSetting;
use Illuminate\Validation\Rule;

class ContractSettingController extends ApiController
{
    public function index($type)
    {
        $user=auth()->user();
        $contract_settings=ContractSetting::where('type',$type)->where('account_id',$user->current_account_id)->where('project_id',$user->current_project_id)->orderBy('order','asc')->get();
        if(auth()->user()->current_account_id == null){
            return view('dashboard.contract_settings.index', compact('contract_settings','type'));
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return view('account_dashboard.contract_settings.index', compact('contract_settings','type'));
        }else{
            return view('project_dashboard.contract_settings.index', compact('contract_settings','type'));
        }

    }

    public function create($type)
    {
        if(auth()->user()->current_account_id == null){
            return view('dashboard.contract_settings.create',compact('type'));
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return view('account_dashboard.contract_settings.create',compact('type'));
        }else{
            return view('project_dashboard.contract_settings.create',compact('type'));
        }
    }
    public function store(Request $request)
    {
        ContractSetting::create(['project_id'=>auth()->user()->current_project_id,
                            'account_id'=>auth()->user()->current_account_id,
                            'name'=>$request->name,
                            'description'=>$request->description,
                            'order'=>$request->order? intval($request->order) : 0 ,
                            'type'=>$request->type ]);
        
        if(auth()->user()->current_account_id == null){
            return redirect('/accounts/contract-settings/'.$request->type)->with('success', ucwords(str_replace('_', ' ', $request->type)) . ' created successfully.');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return redirect('/account/contract-settings/'.$request->type)->with('success', ucwords(str_replace('_', ' ', $request->type)) . ' created successfully.');
        }else{
            return redirect('/project/contract-settings/'.$request->type)->with('success', ucwords(str_replace('_', ' ', $request->type)) . ' created successfully.');
        }

    }
    public function edit($id)
    {
        $contract_setting=ContractSetting::findOrFail($id);
        if(auth()->user()->current_account_id == null){
            return view('dashboard.contract_settings.edit',compact('contract_setting'));
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return view('account_dashboard.contract_settings.edit',compact('contract_setting'));
        }else{
            return view('project_dashboard.contract_settings.edit',compact('contract_setting'));
        }
    }
    public function update(Request $request,$id)
    {
        ContractSetting::where('id',$id)->update([
                            'name'=>$request->name,
                            'order'=>$request->order? intval($request->order) : 0]);
        $setting=ContractSetting::findOrFail($id);
        if(auth()->user()->current_account_id == null){
            return redirect('/accounts/contract-settings/'. $setting->type)->with('success', ucwords(str_replace('_', ' ', $setting->type)) . ' updated successfully.');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return redirect('/account/contract-settings/'. $setting->type)->with('success', ucwords(str_replace('_', ' ', $setting->type)) . ' updated successfully.');
        }else{
            return redirect('/project/contract-settings/'. $setting->type)->with('success', ucwords(str_replace('_', ' ', $setting->type)) . ' updated successfully.');
        }
    }
    public function delete($id)
    {
        $setting=ContractSetting::findOrFail($id);
        $type=$setting->type;
        ContractSetting::where('id',$id)->delete();
        if(auth()->user()->current_account_id == null){
            return redirect('/accounts/contract-settings/'. $type)->with('success', ucwords(str_replace('_', ' ', $type)) . ' deleted successfully.');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return redirect('/account/contract-settings/'. $type)->with('success', ucwords(str_replace('_', ' ', $type)) . ' deleted successfully.');
        }else{
            return redirect('/project/contract-settings/'. $type)->with('success', ucwords(str_replace('_', ' ', $type)) . ' deleted successfully.');
        }
    }
}
