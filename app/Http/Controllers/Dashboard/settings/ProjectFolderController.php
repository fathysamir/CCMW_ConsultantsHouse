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
use App\Models\Project;
use App\Models\ProjectFolder;
use Illuminate\Validation\Rule;

class ProjectFolderController extends ApiController
{
    public function index(Request $request)
    {
        $user=auth()->user();
        $folders=ProjectFolder::where('account_id',$user->current_account_id)->where('project_id',$user->current_project_id)->orderBy('order','asc')->get();
        if(auth()->user()->current_account_id == null){
            return view('dashboard.project_folders.index', compact('folders'));
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return view('account_dashboard.project_folders.index', compact('folders'));
        }else{
            return view('project_dashboard.project_folders.index', compact('folders'));
        }

    }

    public function create()
    {
        if(auth()->user()->current_account_id == null){
            return view('dashboard.project_folders.create');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return view('account_dashboard.project_folders.create');
        }else{
            return view('project_dashboard.project_folders.create');
        }
    }
    public function store(Request $request)
    {
        $folder=ProjectFolder::create(['project_id'=>auth()->user()->current_project_id,
                            'account_id'=>auth()->user()->current_account_id,
                            'name'=>$request->name,
                            'order'=>$request->order? intval($request->order) : 0,
                            'label3'=>$request->label3 ?? 'End Date',
                            'label2'=>$request->label2 ?? 'Start Date',
                            'label1'=>$request->label1 ?? 'Against']);
        if($request->potential_impact){
            $folder->potential_impact='1';
           
        }
        if($request->shortcut){
            $folder->shortcut='1';
            
        }
        $folder->save();
        if(auth()->user()->current_account_id == null){
            return redirect('/accounts/project-folders')->with('success', 'Folder created successfully.');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return redirect('/account/project-folders')->with('success', 'Folder Type created successfully.');
        }else{
            return redirect('/project/project-folders')->with('success', 'Folder Type created successfully.');
        }

    }
    public function edit($id)
    {
        $folder=ProjectFolder::findOrFail($id);
        if(auth()->user()->current_account_id == null){
            return view('dashboard.project_folders.edit',compact('folder'));
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return view('account_dashboard.project_folders.edit',compact('folder'));
        }else{
            return view('project_dashboard.project_folders.edit',compact('folder'));
        }
    }
    public function update(Request $request,$id)
    {
        ProjectFolder::where('id',$id)->update([
                            'name'=>$request->name,
                            'order'=>$request->order? intval($request->order) : 0,
                            'label3'=>$request->label3 ?? 'End Date',
                            'label2'=>$request->label2 ?? 'Start Date',
                            'label1'=>$request->label1 ?? 'Against']);
        $folder=ProjectFolder::findOrFail($id);
        if(!$request->potential_impact){
            $folder->potential_impact='0';
        }else{
            $folder->potential_impact='1';
        }

        if(!$request->shortcut){
            $folder->shortcut='0';
        }else{
            $folder->shortcut='1';
        }
        $folder->save();
        if(auth()->user()->current_account_id == null){
            return redirect('/accounts/project-folders')->with('success', 'Folder updated successfully.');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return redirect('/account/project-folders')->with('success', 'Folder updated successfully.');
        }else{
            return redirect('/project/project-folders')->with('success', 'Folder updated successfully.');
        }
    }
    public function delete($id)
    {
        ProjectFolder::where('id',$id)->delete();
        if(auth()->user()->current_account_id == null){
            return redirect('/accounts/project-folders')->with('success', 'Folder deleted successfully.');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return redirect('/account/project-folders')->with('success', 'Folder deleted successfully.');
        }else{
            return redirect('/project/project-folders')->with('success', 'Folder deleted successfully.');
        }
    }
}
