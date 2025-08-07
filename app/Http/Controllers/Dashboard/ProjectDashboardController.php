<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Account;
use App\Models\Document;
use App\Models\FileDocument;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectDashboardController extends ApiController
{
    public function index()
    {

        $user    = auth()->user();
        $account = Account::findOrFail($user->current_account_id);
        $users   = $account->users;

        $project        = Project::findOrFail($user->current_project_id);
        $assigned_users = $project->assign_users()->pluck('users.id')->toArray();

        $allUserDocuments                  = Document::where('user_id', $user->id)->where('project_id', $user->current_project_id)->count();
        $allActiveUserDocuments            = Document::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('assess_not_pursue', '0')->count();
        $allInactiveUserDocuments          = $allUserDocuments - $allActiveUserDocuments;
        $allPendingAnalysisUserDocuments   = Document::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('assess_not_pursue', '0')->where('analysis_complete', '0')->count();
        $allPendingAssignmentUserDocuments = Document::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('assess_not_pursue', '0')->whereDoesntHave('files')->count();
        $allAssignmentUserDocuments        = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        })->whereHas('file', function ($f) use($user) {
            $f->where('assess_not_pursue', '0')
                ->where('closed', '0')
                ->where('user_id', $user->id)
                ->whereHas('folder', function ($d) {
                    $d->where('potential_impact', '1');
                });
        })->count();
        $allNeedNarrativeUserDocuments = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        })->whereHas('file', function ($f)use($user) {
            $f->where('assess_not_pursue', '0')
                ->where('closed', '0')
                ->where('user_id', $user->id)
                ->whereHas('folder', function ($d) {
                    $d->where('potential_impact', '1');
                });
        })->where('narrative', null)->count();
        $allForClaimUserDocuments = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        })->whereHas('file', function ($f)use($user) {
            $f->where('assess_not_pursue', '0')
                ->where('closed', '0')
                ->where('user_id', $user->id)
                ->whereHas('folder', function ($d) {
                    $d->where('potential_impact', '1');
                });
        })->where('forClaim', '1')->count();
        $allHaveConTagsUserDocuments = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        })->whereHas('file', function ($f)use($user) {
            $f->where('assess_not_pursue', '0')
                ->where('closed', '0')
                ->where('user_id', $user->id)
                ->whereHas('folder', function ($d) {
                    $d->where('potential_impact', '1');
                });
        })->whereHas('tags')->count();
        $allHaveConTagsNoticeClaimUserDocuments = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        })->whereHas('file', function ($f)use($user) {
            $f->where('assess_not_pursue', '0')
                ->where('closed', '0')
                ->where('user_id', $user->id)
                ->whereHas('folder', function ($d) {
                    $d->where('potential_impact', '1');
                });
        })->whereHas('tags', function ($t) {
            $t->where('is_notice', '1');
        })->count();
        $ActiveOpenClaimFilesNeed1ClaimNotice = ProjectFile::where('user_id', $user->id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileDocuments', function ($d) use ($user) {
            $d->whereHas('tags', function ($t) {
                $t->where('is_notice', '1');
            })
                ->whereHas('document', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
        })->count();
        $ActiveOpenClaimFilesNeedFurtherNotice = ProjectFile::where('user_id', $user->id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereHas('fileDocuments', function ($d) use ($user) {
            $d->whereHas('tags', function ($t) {
                $t->where('is_notice', '1');
            })
                ->whereHas('document', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0')
                        ->where('start_date', '<', Carbon::now()->subMonth()->format('Y-m-d'));
                });
        })->count();

        $ActiveClaimFile = ProjectFile::where('user_id', $user->id)->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFile = ProjectFile::where('user_id', $user->id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveClosedClaimFile = ProjectFile::where('user_id', $user->id)->where('closed', '1')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFileTime = ProjectFile::where('user_id', $user->id)->where('time', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFileProlongationCost = ProjectFile::where('user_id', $user->id)->where('prolongation_cost', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFileVariation = ProjectFile::where('user_id', $user->id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFileDisruption = ProjectFile::where('user_id', $user->id)->where('disruption_cost', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();

        $needChronology = ProjectFile::where('user_id', $user->id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileDocuments')->count();
        $needSynopsis = ProjectFile::where('user_id', $user->id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileAttachment', function ($a) {
            $a->where('section', '1');
        })->count();
        $needContractualA = ProjectFile::where('user_id', $user->id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileAttachment', function ($a) {
            $a->where('section', '2');
        })->count();
        $needCauseEffectA = ProjectFile::where('user_id', $user->id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileAttachment', function ($a) {
            $a->where('section', '3');
        })->count();

        return view('project_dashboard.home', compact('allForClaimUserDocuments', 'allAssignmentUserDocuments', 'allActiveUserDocuments',
            'allInactiveUserDocuments', 'users', 'allUserDocuments',
            'ActiveClaimFile', 'ActiveOpenClaimFile', 'ActiveClosedClaimFile',
            'ActiveOpenClaimFileTime', 'ActiveOpenClaimFileProlongationCost', 'ActiveOpenClaimFileVariation', 'ActiveOpenClaimFileDisruption',
            'needChronology', 'needSynopsis', 'needContractualA', 'needCauseEffectA',
            'allPendingAnalysisUserDocuments', 'allPendingAssignmentUserDocuments',
            'allNeedNarrativeUserDocuments', 'project', 'assigned_users',
            'allHaveConTagsUserDocuments', 'allHaveConTagsNoticeClaimUserDocuments', 'ActiveOpenClaimFilesNeed1ClaimNotice', 'ActiveOpenClaimFilesNeedFurtherNotice'));

    }

    public function assign_users(Request $request)
    {
        $user = auth()->user();
        // $permissions=['show_contract_tags','create_contract_tags','edit_contract_tags','delete_contract_tags',
        //                 'show_project_folder','create_project_folder','edit_project_folder','delete_project_folder',
        //                 'show_document_type','create_document_type','edit_document_type','delete_document_type',
        //                 'show_contract_settings','create_contract_settings','edit_contract_settings','delete_contract_settings',
        //                 'upload_documents','upload_group_documents','import_documents','edit_documents','delete_documents',
        //                 'analysis',
        //                 'create_file','edit_file','delete_file','cope_move_file'];
        $permissions = ['upload_documents', 'upload_group_documents', 'import_documents', 'edit_documents', 'delete_documents',
            'analysis',
            'create_file', 'edit_file', 'delete_file'];
        if ($request->assigned_users && count($request->assigned_users) > 0) {
            foreach ($request->assigned_users as $id) {
                $userExists = ProjectUser::where('user_id', $id)->where('project_id', $user->current_project_id)->exists();
                if ($userExists == false) {
                    ProjectUser::create(['user_id' => $id, 'account_id' => $user->current_account_id, 'project_id' => $user->current_project_id, 'permissions' => json_encode($permissions)]);
                }
            }
            ProjectUser::where('project_id', $user->current_project_id)->whereNotIn('user_id', $request->assigned_users)->delete();

            return redirect('/project')->with('success', 'Users assigned successfully.');

        } else {
            ProjectUser::where('project_id', $user->current_project_id)->delete();

            return redirect('/project')->with('error', 'No user selected.');

        }

    }
}
