<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\Document;
use App\Models\User;
use App\Models\ProjectFolder;
use App\Models\ProjectFile;
use App\Models\StorageFile;
use App\Models\Project;
use Illuminate\Validation\Rule;

class FileController extends ApiController
{
    public function index(){
        $zip_file= session('zip_file');
        if($zip_file){
            $filePath=public_path('projects/' . auth()->user()->current_project_id . '/temp/') . $zip_file;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            session()->forget('zip_file');
        }
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $user = auth()->user();
        $folder = ProjectFolder::findOrFail($user->current_folder_id);
        $all_files = ProjectFile::where('folder_id',$folder->id)->orderBy('code', 'asc')->get(); 

        return view('project_dashboard.project_files.index', compact('all_files','folder','users'));
    }

    public function create(){
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $user = auth()->user();
        $folder = ProjectFolder::findOrFail($user->current_folder_id);
        $stake_holders = $project->stakeHolders;

        return view('project_dashboard.project_files.create', compact('folder','users','stake_holders'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required', // 10MB max
            'owner_id' => 'required|exists:users,id',
        ]);
        do {
            $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (ProjectFile::where('slug', $invitation_code)->exists());

        $file=ProjectFile::create(['name'=>$request->name,'slug'=>$invitation_code,'code'=>$request->code,
                                    'user_id'=>$request->owner_id,
                                    'project_id'=>auth()->user()->current_project_id,'against_id'=>$request->against_id,'start_date'=>$request->start_date,
                                    'end_date'=>$request->end_date,'folder_id'=>auth()->user()->current_folder_id,
                                    'notes'=>$request->notes]);
        if($request->time){
            $file->time='1';
        }
        if($request->prolongation_cost){
            $file->prolongation_cost='1';
        }
        if($request->disruption_cost){
            $file->disruption_cost='1';
        }
        if($request->variation){
            $file->variation='1';
        }
        if($request->closed){
            $file->closed='1';
        }
        if($request->assess_not_pursue){
            $file->assess_not_pursue='1';
        }
        $file->save();
        return redirect('/project/files')->with('success', 'File Created successfully.');

    }

    public function edit($id){
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $user = auth()->user();
        $folder = ProjectFolder::findOrFail($user->current_folder_id);
        $stake_holders = $project->stakeHolders;
        $file = ProjectFile::where('slug',$id)->first();
        return view('project_dashboard.project_files.edit', compact('folder','users','stake_holders','file'));
    }

    public function update(Request $request,$id){
        $request->validate([
            'name' => 'required', // 10MB max
            'owner_id' => 'required|exists:users,id',
        ]);
        

        ProjectFile::where('id',$id)->update(['name'=>$request->name,'code'=>$request->code,
                                    'user_id'=>$request->owner_id,
                                    'against_id'=>$request->against_id,'start_date'=>$request->start_date,
                                    'end_date'=>$request->end_date,
                                    'notes'=>$request->notes]);
        $file=ProjectFile::findOrFail($id);
        if($request->time){
            $file->time='1';
        }else{
            $file->time='0';
        }
        if($request->prolongation_cost){
            $file->prolongation_cost='1';
        }else{
            $file->prolongation_cost='0';
        }
        if($request->disruption_cost){
            $file->disruption_cost='1';
        }else{
            $file->disruption_cost='0';
        }
        if($request->variation){
            $file->variation='1';
        }else{
            $file->variation='0';
        }
        if($request->closed){
            $file->closed='1';
        }else{
            $file->closed='0';
        }
        if($request->assess_not_pursue){
            $file->assess_not_pursue='1';
        }else{
            $file->assess_not_pursue='0';
        }
        $file->save();
        return redirect('/project/files')->with('success', 'File Updated successfully.');
    }
    public function changeOwner(Request $request)
    {
        $request->validate([
            'file_id' => 'required|exists:project_files,id',
            'new_owner_id' => 'required|exists:users,id',
        ]);

        $file = ProjectFile::find($request->file_id);
        $file->user_id = $request->new_owner_id;
        $file->save();

        return response()->json(['success' => true]);
    }

    public function delete($id){
        $file = ProjectFile::where('id', $id)->first();
        $user = auth()->user();
        $Archive = ProjectFolder::where('account_id', $user->current_account_id)
        ->where('project_id', $user->current_project_id)->where('name','Archive')
        ->first();
        $Folder = ProjectFolder::where('account_id', $user->current_account_id)
            ->where('project_id', $user->current_project_id)->where('name','Recycle Bin')
            ->first();
        if($file->older_folder_id == null){
            
            $file->older_folder_id = $file->folder_id;
            $file->folder_id = $Folder->id;
            $file->save();
        }elseif($file->folder_id == $Archive->id){
            $file->folder_id = $Folder->id;
            $file->save();
        }else{
            $file->delete();
        }
        return redirect('/project/files')->with('success', 'File Deleted successfully.');

    }

    public function archive($id){
        $file = ProjectFile::where('id', $id)->first();
        $user = auth()->user();
        $Folder = ProjectFolder::where('account_id', $user->current_account_id)
        ->where('project_id', $user->current_project_id)->where('name','Archive')
        ->first();
        if($file->older_folder_id==null){
            $file->older_folder_id = $file->folder_id;
        }
        $file->folder_id = $Folder->id;
        $file->save();
        return redirect('/project/files')->with('success', 'File Archive successfully.');

    }

}