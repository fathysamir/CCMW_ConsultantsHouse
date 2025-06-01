<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Account;
use App\Models\Category;
use App\Models\Project;
use App\Models\StakeHolder;
use App\Models\ProjectAbbreviation;
use App\Services\ProjectService;
use Illuminate\Http\Request;

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
            if ($hasRole) {
                $EPS = Category::where('account_id', $user->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
            } else {
                $projectsId = $user->assign_projects()->pluck('projects.id')->toArray();

                $categoriesId = Project::whereIn('id', $projectsId)->pluck('category_id')->toArray();
                $subCategories = Category::whereIn('id', $categoriesId)->get();

                // Get top-level parents
                $parentCategoryIds = $subCategories->map(function ($cat) {
                    return $cat->getRootCategory()->id;
                })->unique()->toArray();
                $EPS = Category::whereIn('id', $parentCategoryIds)->where('account_id', $user->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
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
            'Other',
        ];
        $EPS = Category::whereNotIn('name', ['Recycle Bin', 'Archive'])->where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
        $route = 'projects.store_project';

        return view('account_dashboard.projects.create', compact('roles', 'EPS', 'route'));
    }

    public function store_project(Request $request)
    {
        $result = $this->projectService->createProject($request);

        if (! $result['success']) {
            return redirect()->back()->withInput()->withErrors($result['errors']);
        }

        return redirect('/account/projects')->with('success', 'Project created successfully.');
    }

    public function edit_project_view($id)
    {
        $project = Project::where('slug', $id)->first();
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
            'Other',
        ];
        $EPS = Category::whereNotIn('name', ['Recycle Bin', 'Archive'])->where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();

        return view('account_dashboard.projects.edit', compact('roles', 'project', 'EPS'));

    }

    public function update_project(Request $request, Project $project)
    {
        $result = $this->projectService->updateProject($request, $project);

        if (! $result['success']) {
            return redirect()->back()->withInput()->withErrors($result['errors']);
        }
        if (auth()->user()->current_project_id) {
            return redirect('/project')->with('success', 'Project updated successfully.');
        } else {
            return redirect('/account/projects')->with('success', 'Project updated successfully.');
        }
    }

    public function archiveProject(Request $request)
    {
        $archiveEPS = Category::where('account_id', auth()->user()->current_account_id)->where('name', 'Archive')->first();
        $project = Project::findOrFail($request->project_id);
        if ($project->old_category_id == null) {
            $project->old_category_id = $project->category_id;
        }
        $project->category_id = $archiveEPS->id;
        $project->status = 'Archived';
        $project->save();

        return $this->sendResponse(null, 'success');

    }

    public function deleteProject(Request $request)
    {
        $EPS = Category::where('account_id', auth()->user()->current_account_id)->where('name', 'Recycle Bin')->first();
        $project = Project::findOrFail($request->project_id);
        if ($project->status == 'Deleted') {
            $project->delete();
        } else {
            if ($project->old_category_id == null) {
                $project->old_category_id = $project->category_id;
            }
            $project->category_id = $EPS->id;
            $project->status = 'Deleted';
            $project->save();
        }

        return $this->sendResponse(null, 'success');

    }

    public function restoreProject(Request $request)
    {
        $project = Project::findOrFail($request->project_id);
        $project->category_id = $project->old_category_id;
        $project->old_category_id = null;
        $project->status = 'Active';
        $project->save();

        return $this->sendResponse(null, 'success');
    }


    public function stakeholders_view($id){
        $project = Project::where('slug', $id)->first();
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
            'Other',
        ];
        return view('project_dashboard.project.stakeholders', compact('roles', 'project'));

    }

    public function update_stakeholders(Request $request,Project $project){
        $existingIds = [];
        // Update existing stakeholders
        if ($request->old_stakeholders && count($request->old_stakeholders) > 0) {
            foreach ($request->old_stakeholders as $id => $stakeholder) {
                $narrative = $stakeholder['chronology'] ?? $stakeholder['role'];

                StakeHolder::where('id', $id)->update([
                    'name' => $stakeholder['name'],
                    'role' => $stakeholder['role'],
                    'narrative' => $narrative,
                    'article' => $stakeholder['article'],
                ]);
                $existingIds[] = $id;
            }
            // Delete stakeholders that are no longer in the list
            StakeHolder::whereNotIn('id', $existingIds)
                ->where('project_id', $project->id)
                ->delete();
        } else {
            // If no existing stakeholders are provided, delete all current stakeholders
            StakeHolder::where('project_id', $project->id)->delete();
        }

        // Create new stakeholders
        if ($request->stakeholders && count($request->stakeholders) > 0) {
            foreach ($request->stakeholders as $stakeholder) {
                $narrative = $stakeholder['chronology'] ?? $stakeholder['role'];

                StakeHolder::create([
                    'project_id' => $project->id,
                    'name' => $stakeholder['name'],
                    'role' => $stakeholder['role'],
                    'narrative' => $narrative,
                    'article' => $stakeholder['article'],
                ]);
            }
        }
        return redirect('/account/project/stakeholders/'. $project->slug)->with('success', 'Project Stakeholders updated successfully.');
 
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////Project Abbreviations///////////////////////////////////////

    public function index_abbreviations(){
        $abbreviations=ProjectAbbreviation::where('project_id',auth()->user()->current_project_id)->get();
        return view('project_dashboard.project.abbreviations.index', compact('abbreviations'));
    }
    public function create_abbreviation(){
        return view('project_dashboard.project.abbreviations.create');
    }
    public function store_abbreviation(Request $request){
        ProjectAbbreviation::create(['project_id'=>auth()->user()->current_project_id,'abb'=>$request->abb,'description'=>$request->description]);
        return redirect('/account/project/abbreviations')->with('success', 'New Abbreviation Saved successfully.');
    }
    public function edit_abbreviation($id){
        $abbreviation=ProjectAbbreviation::where('id',$id)->first();
        return view('project_dashboard.project.abbreviations.edit',compact('abbreviation'));
    }
    public function update_abbreviation(Request $request,ProjectAbbreviation $abbreviation){
        $abbreviation->abb=$request->abb;
        $abbreviation->description=$request->description;
        $abbreviation->save();
        return redirect('/account/project/abbreviations')->with('success', 'Abbreviation Updated successfully.');
    }
    public function delete_abbreviation($id)
    {
        ProjectAbbreviation::where('id', $id)->delete();
        return redirect('/account/project/abbreviations')->with('success', 'Abbreviation deleted successfully.');

    }
}
