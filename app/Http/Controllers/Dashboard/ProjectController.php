<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\Account;
use App\Models\Category;
use App\Services\ProjectService;
use App\Models\Project;
use Illuminate\Validation\Rule;

class ProjectController extends ApiController
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    public function index()
    {
        $user = auth()->user();
        $account = Account::findOrFail($user->current_account_id);
        if (auth()->user()->roles->first()->name == 'Super Admin') {
            // $project_count= Project::where('account_id',$user->current_account_id)->get();
            $EPS = Category::where('account_id', $user->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();

        } else {
            $hasRole = $user->accounts()
                ->where('accounts.id', $user->current_account_id)
                ->wherePivot('role', 'Admin Account')
                ->exists();
            if($hasRole){
                $EPS = Category::where('account_id', $user->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
            }else{
                $projectsId=$user->assign_projects->pluck('id')->toArray();
                $categoriesId=Project::whereIn('id',$projectsId)->pluck('category_id')->toArray();
                $EPS = Category::whereIn('id',$categoriesId)->where('account_id', $user->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
            }
           
        }
        return view('account_dashboard.projects', compact('EPS', 'account'));

    }
    public function create_project_view()
    {
        $roles = [
            'Employer',
            'Contractor',
            'Engineer',
            'Project Manager',
            'Consultant',
            'Sub-Contractor',
            'Authority',
            'Another Contractor',
            'Lower-Tier Subcontractor',
            'Other'
        ];
        $EPS = Category::whereNotIn('name',['Recycle Bin','Archive'])->where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
        $route = 'projects.store_project';
        return view('account_dashboard.projects.create', compact('roles', 'EPS', 'route'));
    }

    public function store_project(Request $request)
    {
        $result = $this->projectService->createProject($request);

        if (!$result['success']) {
            return redirect()->back()->withInput()->withErrors($result['errors']);
        }

        return redirect('/account/projects')->with('success', 'Project created successfully.');
    }

    public function edit_project_view($id)
    {
        $project = Project::findOrFail($id);
        $project->logo = getFirstMediaUrl($project, $project->logoCollection);
        $roles = [
            'Employer',
            'Contractor',
            'Engineer',
            'Project Manager',
            'Consultant',
            'Sub-Contractor',
            'Authority',
            'Another Contractor',
            'Lower-Tier Subcontractor',
            'Other'
        ];
        $EPS = Category::whereNotIn('name',['Recycle Bin','Archive'])->where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
        return view('account_dashboard.projects.edit', compact('roles', 'project', 'EPS'));

    }

    public function update_project(Request $request, Project $project)
    {
        $result = $this->projectService->updateProject($request, $project);

        if (!$result['success']) {
            return redirect()->back()->withInput()->withErrors($result['errors']);
        }

        return redirect('/account/projects')->with('success', 'Project updated successfully.');
    }

    public function archiveProject(Request $request){
        $archiveEPS=Category::where('account_id',auth()->user()->current_account_id)->where('name','Archive')->first();
        $project=Project::findOrFail($request->project_id);
        if($project->old_category_id==null){
            $project->old_category_id=$project->category_id;
        }
        $project->category_id=$archiveEPS->id;
        $project->status='Archived';
        $project->save();
        return $this->sendResponse(null, 'success');

    }

    public function deleteProject(Request $request){
        $EPS=Category::where('account_id',auth()->user()->current_account_id)->where('name','Recycle Bin')->first();
        $project=Project::findOrFail($request->project_id);
        if($project->status=='Deleted'){
            $project->delete();
        }else{
            if($project->old_category_id==null){
                $project->old_category_id=$project->category_id;
            }
            $project->category_id=$EPS->id;
            $project->status='Deleted';
            $project->save();
        }
        return $this->sendResponse(null, 'success');

    }

    public function restoreProject(Request $request){
        $project=Project::findOrFail($request->project_id);
        $project->category_id=$project->old_category_id;
        $project->old_category_id=null;
        $project->status='Active';
        $project->save();
        return $this->sendResponse(null, 'success');
    }

}
