<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Account;
use App\Models\ContractTag;
use App\Models\Document;
use App\Models\FileDocument;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        })->whereHas('file', function ($f) use ($user) {
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
        })->whereHas('file', function ($f) use ($user) {
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
        })->whereHas('file', function ($f) use ($user) {
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
        })->whereHas('file', function ($f) use ($user) {
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
        })->whereHas('file', function ($f) use ($user) {
            $f->where('assess_not_pursue', '0')
                ->where('closed', '0')
                ->where('user_id', $user->id)
                ->whereHas('folder', function ($d) {
                    $d->where('potential_impact', '1');
                });
        })->whereHas('tags', function ($t) {
            $t->where('is_notice', '1');
        })->count();
        $ActiveOpenClaimFilesNeed1ClaimNotice = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->where(function ($query) use ($user) {
            // No file documents at all
            $query->whereDoesntHave('fileDocuments')
            // Or file documents without the notice tag
                ->orWhereDoesntHave('fileDocuments', function ($d) use ($user) {
                    $d->whereHas('tags', function ($t) {
                        $t->where('is_notice', '1');
                    })
                        ->whereHas('document', function ($q) use ($user) {
                            $q->where('user_id', $user->id)
                                ->where('project_id', $user->current_project_id)
                                ->where('assess_not_pursue', '0');
                        });
                });
        })->count();

        $ActiveOpenClaimFilesNeedFurtherNotice = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
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

        $ActiveClaimFile = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFile = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveClosedClaimFile = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('closed', '1')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFileTime = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('time', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFileProlongationCost = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('prolongation_cost', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFileVariation = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();
        $ActiveOpenClaimFileDisruption = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('disruption_cost', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->count();

        $needChronology = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileDocuments')->count();
        $needSynopsis = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileAttachment', function ($a) {
            $a->where('section', '1');
        })->count();
        $needContractualA = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileAttachment', function ($a) {
            $a->where('section', '2');
        })->count();
        $needCauseEffectA = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileAttachment', function ($a) {
            $a->where('section', '3');
        })->count();
        $con1                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 1)->first();
        $FileVariation1['label'] = $con1 ? $con1->name : '';
        $FileVariation1['value'] = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        })->whereHas('fileDocuments', function ($d) use ($user) {
            $d->whereHas('tags', function ($t) {
                $t->whereIn('var_process', [1, 2, 3, 4, 5, 6]);
            })->whereHas('document', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('project_id', $user->current_project_id)
                    ->where('assess_not_pursue', '0');
            });
        })->count();
        $FileVariation1['value'] = $ActiveOpenClaimFileVariation - $FileVariation1['value'];
        $con2                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 2)->first();
        $FileVariation2['label'] = $con2 ? $con2->name : '';
        $FileVariation2['value'] = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        })->whereHas('fileDocuments', function ($d) use ($user) {
            $d->whereHas('tags', function ($t) {
                $t->whereIn('var_process', [2, 3, 4, 5, 6]);
            })->whereHas('document', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('project_id', $user->current_project_id)
                    ->where('assess_not_pursue', '0');
            });
        })->count();
        $FileVariation2['value'] = $ActiveOpenClaimFileVariation - $FileVariation2['value'];
        $con3                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 3)->first();
        $FileVariation3['label'] = $con3 ? $con3->name : '';
        $FileVariation3['value'] = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        })->whereHas('fileDocuments', function ($d) use ($user) {
            $d->whereHas('tags', function ($t) {
                $t->whereIn('var_process', [3, 4, 5, 6]);
            })->whereHas('document', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('project_id', $user->current_project_id)
                    ->where('assess_not_pursue', '0');
            });
        })->count();
        $FileVariation3['value'] = $ActiveOpenClaimFileVariation - $FileVariation3['value'];
        $con4                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 4)->first();
        $FileVariation4['label'] = $con4 ? $con4->name : '';
        $FileVariation4['value'] = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        })->whereHas('fileDocuments', function ($d) use ($user) {
            $d->whereHas('tags', function ($t) {
                $t->whereIn('var_process', [4, 5, 6]);
            })->whereHas('document', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('project_id', $user->current_project_id)
                    ->where('assess_not_pursue', '0');
            });
        })->count();
        $FileVariation4['value'] = $ActiveOpenClaimFileVariation - $FileVariation4['value'];
        $con5                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 5)->first();
        $FileVariation5['label'] = $con5 ? $con5->name : '';
        $FileVariation5['value'] = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        })->whereHas('fileDocuments', function ($d) use ($user) {
            $d->whereHas('tags', function ($t) {
                $t->whereIn('var_process', [5, 6]);
            })->whereHas('document', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('project_id', $user->current_project_id)
                    ->where('assess_not_pursue', '0');
            });
        })->count();
        $FileVariation5['value'] = $ActiveOpenClaimFileVariation - $FileVariation5['value'];
        $con6                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 6)->first();
        $FileVariation6['label'] = $con6 ? $con6->name : '';
        $FileVariation6['value'] = ProjectFile::where('user_id', $user->id)->where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        })->whereHas('fileDocuments', function ($d) use ($user) {
            $d->whereHas('tags', function ($t) {
                $t->whereIn('var_process', [6]);
            })->whereHas('document', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('project_id', $user->current_project_id)
                    ->where('assess_not_pursue', '0');
            });
        })->count();
        $FileVariation6['value'] = $ActiveOpenClaimFileVariation - $FileVariation6['value'];
        $analysis_complete_value = ProjectFile::where('user_id', $user->id)
            ->where('project_id', $user->current_project_id)
            ->where('closed', '0')
            ->where('assess_not_pursue', '0')
            ->whereHas('folder', function ($f) {
                $f->where('potential_impact', '1');
            })
            ->avg(DB::raw('COALESCE(analyses_complete, 0)')) ?? 0;
        $percent1 = $ActiveOpenClaimFile > 0 ? $analysis_complete_value : 0;
        return view('project_dashboard.home', compact('allForClaimUserDocuments', 'allAssignmentUserDocuments', 'allActiveUserDocuments',
            'allInactiveUserDocuments', 'users', 'allUserDocuments', 'percent1',
            'ActiveClaimFile', 'ActiveOpenClaimFile', 'ActiveClosedClaimFile',
            'ActiveOpenClaimFileTime', 'ActiveOpenClaimFileProlongationCost', 'ActiveOpenClaimFileVariation', 'ActiveOpenClaimFileDisruption',
            'needChronology', 'needSynopsis', 'needContractualA', 'needCauseEffectA',
            'allPendingAnalysisUserDocuments', 'allPendingAssignmentUserDocuments',
            'allNeedNarrativeUserDocuments', 'project', 'assigned_users',
            'allHaveConTagsUserDocuments', 'allHaveConTagsNoticeClaimUserDocuments', 'ActiveOpenClaimFilesNeed1ClaimNotice', 'ActiveOpenClaimFilesNeedFurtherNotice',
            'FileVariation1', 'FileVariation2', 'FileVariation3', 'FileVariation4', 'FileVariation5', 'FileVariation6'));

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
