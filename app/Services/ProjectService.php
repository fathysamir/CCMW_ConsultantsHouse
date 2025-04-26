<?php

namespace App\Services;

use App\Models\Project;
use App\Models\StakeHolder;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\DocType;
use App\Models\ContractTag;
use App\Models\ProjectFolder;
use App\Models\ContractSetting;

class ProjectService
{
    public function createProject(Request $request)
    {
        $validator = $this->validateProjectData($request);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $project = $this->storeProject($request);
        $this->handleLogoUpload($request, $project);
        $this->createStakeholders($request, $project);
        $this->createMilestones($request, $project);
        $all_project_folders=ProjectFolder::where('project_id',null)->where('account_id',$project->account_id)->get();
        foreach($all_project_folders as $folder){
            ProjectFolder::create(['account_id'=>$project->account_id,'project_id'=>$project->id,'name'=>$folder->name,'order'=>$folder->order,'label1'=>$folder->label1,
            'label2'=>$folder->label2,
            'label3'=>$folder->label3,'potential_impact'=>$folder->potential_impact,'shortcut'=>$folder->shortcut]);
        }
        $all_DocTypes=DocType::where('project_id',null)->where('account_id',$project->account_id)->get();
        foreach($all_DocTypes as $DocType){
            DocType::create(['account_id'=>$project->account_id,'project_id'=>$project->id,'name'=>$DocType->name,'order'=>$DocType->order,'description'=>$DocType->description]);
        }
        $all_ContractTags=ContractTag::where('project_id',null)->where('account_id',$project->account_id)->get();
        foreach($all_ContractTags as $ContractTag){
            ContractTag::create(['account_id'=>$project->account_id,'project_id'=>$project->id,'name'=>$ContractTag->name,'order'=>$ContractTag->order,
                                'description'=>$ContractTag->description,'is_notice'=>$ContractTag->is_notice,
                                'sub_clause'=>$ContractTag->sub_clause,'for_letter'=>$ContractTag->for_letter,'var_process'=>$ContractTag->var_process]);
        }
        $all_ContractSettings=ContractSetting::where('project_id',null)->where('account_id',$project->account_id)->get();
        foreach($all_ContractSettings as $ContractSetting){
            ContractSetting::create(['account_id'=>$project->account_id,'project_id'=>$project->id,'name'=>$ContractSetting->name,'order'=>$ContractSetting->order,'type'=>$ContractSetting->type]);
        }

        return ['success' => true, 'project' => $project];
    }

    public function updateProject(Request $request, Project $project)
    {
        
        $validator = $this->validateProjectData($request, $project->id);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $this->updateProjectDetails($request, $project);
        $this->handleLogoUpload($request, $project);
        $this->updateStakeholders($request, $project);
        $this->updateMilestones($request, $project);

        return ['success' => true, 'project' => $project];
    }

    private function validateProjectData(Request $request, $projectId = null)
    {

       
        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:191'],
            'code' => [
                'required', 
                'string', 
                'max:191', 
                Rule::unique('projects', 'code')
                    ->whereNull('deleted_at')
                    ->ignore($projectId,'id')
            ],
            'selected_category' => ['required']
        ], [
            'name.required' => 'The project name is required.',
            'name.string' => 'The project name must be a valid string.',
            'name.max' => 'The project name cannot exceed 191 characters.',
            'code.required' => 'The site code is required.',
            'code.string' => 'The site code must be a valid string.',
            'code.max' => 'The site code cannot exceed 191 characters.',
            'code.unique' => 'This site code is already in use.',
            'selected_category.required' => 'You must select a EPS.'
        ]);
    }

    private function storeProject(Request $request)
    {
        do {
            $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (Project::where('slug', $slug)->exists());
        return Project::create([
            'name' => $request->name,
            'code' => $request->code,
            'slug'=> $slug,
            'account_id' => auth()->user()->current_account_id,
            'category_id' => intval($request->selected_category),
            'contract_date' => $request->contract_date,
            'commencement_date' => $request->commencement_date,
            'condation_contract' => $request->condation_contract,
            'original_value' => floatval($request->original_value),
            'revised_value' => floatval($request->revised_value),
            'currency' => $request->currency,
            'measurement_basis' => $request->measurement_basis,
            'notes' => $request->notes,
            'summary' => $request->summary,
            'user_id' => auth()->user()->id
        ]);
    }

    private function updateProjectDetails(Request $request, Project $project)
    {
        $project->update([
            'name' => $request->name,
            'code' => $request->code,
            'category_id' => intval($request->selected_category),
            'contract_date' => $request->contract_date,
            'commencement_date' => $request->commencement_date,
            'condation_contract' => $request->condation_contract,
            'original_value' => floatval($request->original_value),
            'revised_value' => floatval($request->revised_value),
            'currency' => $request->currency,
            'measurement_basis' => $request->measurement_basis,
            'notes' => $request->notes,
            'summary' => $request->summary
        ]);

        return $project;
    }

    private function handleLogoUpload(Request $request, Project $project)
    {
        if ($request->file('logo')) {
            $logo = getFirstMediaUrl($project, $project->logoCollection);
            if ($logo != null) {
                deleteMedia($project, $project->logoCollection);
            }
            uploadMedia($request->logo, $project->logoCollection, $project);
        }
    }

    private function createStakeholders(Request $request, Project $project)
    {
        if ($request->stakeholders && count($request->stakeholders) > 0) {
            foreach ($request->stakeholders as $stakeholder) {
                $narrative = $stakeholder['chronology'] ?? $stakeholder['role'];
                
                StakeHolder::create([
                    'project_id' => $project->id,
                    'name' => $stakeholder['name'],
                    'role' => $stakeholder['role'],
                    'narrative' => $narrative,
                    'article' => $stakeholder['article']
                ]);
            }
        }
    }

    private function updateStakeholders(Request $request, Project $project)
    {
        $existingIds = [];
        // Update existing stakeholders
        if ($request->old_stakeholders && count($request->old_stakeholders) > 0) {
            foreach ($request->old_stakeholders as $id => $stakeholder) {
                $narrative = $stakeholder['chronology'] ?? $stakeholder['role'];
                
                StakeHolder::where('id', $id)->update([
                    'name' => $stakeholder['name'],
                    'role' => $stakeholder['role'],
                    'narrative' => $narrative,
                    'article' => $stakeholder['article']
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
                    'article' => $stakeholder['article']
                ]);
            }
        }
    }

    private function createMilestones(Request $request, Project $project)
    {
        if ($request->milestones && count($request->milestones) > 0) {
            foreach ($request->milestones as $milestone) {
                Milestone::create([
                    'project_id' => $project->id,
                    'name' => $milestone['name'],
                    'contract_finish_date' => $milestone['contract_finish_date'],
                    'revised_finish_date' => $milestone['revised_finish_date']
                ]);
            }
        }
    }

    private function updateMilestones(Request $request, Project $project)
    {
        $existingIds = [];

        // Update existing milestones
        if ($request->old_milestones && count($request->old_milestones) > 0) {
            foreach ($request->old_milestones as $id => $milestone) {
                Milestone::where('id', $id)->update([
                    'name' => $milestone['name'],
                    'contract_finish_date' => $milestone['contract_finish_date'],
                    'revised_finish_date' => $milestone['revised_finish_date']
                ]);
                $existingIds[] = $id;
            }
            // Delete milestones that are no longer in the list
            Milestone::whereNotIn('id', $existingIds)
                    ->where('project_id', $project->id)
                    ->delete();
        } else {
            // If no existing milestones are provided, delete all current milestones
            Milestone::where('project_id', $project->id)->delete();
        }

        // Create new milestones
        if ($request->milestones && count($request->milestones) > 0) {
            foreach ($request->milestones as $milestone) {
                Milestone::create([
                    'project_id' => $project->id,
                    'name' => $milestone['name'],
                    'contract_finish_date' => $milestone['contract_finish_date'],
                    'revised_finish_date' => $milestone['revised_finish_date']
                ]);
            }
        }
    }
}