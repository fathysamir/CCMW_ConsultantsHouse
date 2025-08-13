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
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectDashboardController extends ApiController
{
    public function index(Request $request)
    {

        $user    = auth()->user();
        $account = Account::findOrFail($user->current_account_id);
        $users   = $account->users;

        $project        = Project::findOrFail($user->current_project_id);
        $assigned_users = $project->assign_users()->pluck('users.id')->toArray();
        $project_users  = $project->assign_users;
        $con1           = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 1)->first();
        $con2           = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 2)->first();
        $con3           = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 3)->first();
        $con4           = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 4)->first();
        $con5           = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 5)->first();
        $con6           = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 6)->first();

        if ($request->user) {
            if ($request->user == $user->code) {
                $project_dashboard_class_name                     = 'fancy-btn2';
                $my_dashboard_class_name                          = 'fancy-btn';
                $user_dashboard_class_name                        = 'fancy-btn2';
                $selected_user                                    = User::where('code', $request->user)->first();
                $labels['allUserDocuments']                       = 'Documents assigned to me';
                $labels['allActiveUserDocuments']                 = 'Active documents assigned to me';
                $labels['allInactiveUserDocuments']               = 'Inactive documents assigned to me';
                $labels['allAssignmentUserDocuments']             = 'Active documents assigned to my active open claim files';
                $labels['allForClaimUserDocuments']               = 'Chronology documents checked for claim';
                $labels['allNeedNarrativeUserDocuments']          = 'Chronology documents need narrative';
                $labels['allHaveConTagsUserDocuments']            = 'Chronology documents have contractual tag';
                $labels['allHaveConTagsNoticeClaimUserDocuments'] = 'Chronology documents and notice of claim';
                $labels['percent1']                               = 'Average percent of analysis complete for active open claim files assigned to my';
                $labels['percent2']                               = 'Average percent of window analysis complete for active open claim files assigned to my';
                $labels['ActiveOpenClaimFileVariation']           = 'Active open claim files with variation assigned to me';
                $labels['FileVariation1']                         = 'Active open claim files assigned to me which need ' . ($con1 ? $con1->name : '');
                $labels['FileVariation2']                         = 'Active open claim files assigned to me which need ' . ($con2 ? $con2->name : '');
                $labels['FileVariation3']                         = 'Active open claim files assigned to me which need ' . ($con3 ? $con3->name : '');
                $labels['FileVariation4']                         = 'Active open claim files assigned to me which need ' . ($con4 ? $con4->name : '');
                $labels['FileVariation5']                         = 'Active open claim files assigned to me which need ' . ($con5 ? $con5->name : '');
                $labels['FileVariation6']                         = 'Active open claim files assigned to me which need ' . ($con6 ? $con6->name : '');
                $labels['allPendingAnalysisUserDocuments']        = 'Active documents need analysis assigned to me';
                $labels['allPendingAssignmentUserDocuments']      = 'Active documents assigned to me which not assigned to any file';
                $labels['ActiveOpenClaimFilesNeed1ClaimNotice']   = 'Active open claim files assigned to me which need first claim notice';
                $labels['ActiveOpenClaimFilesNeedFurtherNotice']  = 'Active open claim files assigned to me which need further notice';

            } else {
                $project_dashboard_class_name                     = 'fancy-btn2';
                $my_dashboard_class_name                          = 'fancy-btn2';
                $user_dashboard_class_name                        = 'fancy-btn';
                $selected_user                                    = User::where('code', $request->user)->first();
                $labels['allUserDocuments']                       = 'Documents assigned to ' . $selected_user->name;
                $labels['allActiveUserDocuments']                 = 'Active documents assigned to ' . $selected_user->name;
                $labels['allInactiveUserDocuments']               = 'Inactive documents assigned to ' . $selected_user->name;
                $labels['allAssignmentUserDocuments']             = 'Active documents assigned to ' . $selected_user->name . '\'s active open claim files';
                $labels['allForClaimUserDocuments']               = 'Chronology documents checked for claim';
                $labels['allNeedNarrativeUserDocuments']          = 'Chronology documents need narrative';
                $labels['allHaveConTagsUserDocuments']            = 'Chronology documents have contractual tag';
                $labels['allHaveConTagsNoticeClaimUserDocuments'] = 'Chronology documents and notice of claim';
                $labels['percent1']                               = 'Average percent of analysis complete for active open claim files assigned to ' . $selected_user->name;
                $labels['percent2']                               = 'Average percent of window analysis complete for active open claim files assigned to ' . $selected_user->name;
                $labels['ActiveOpenClaimFileVariation']           = 'Active open claim files with variation assigned to ' . $selected_user->name;
                $labels['FileVariation1']                         = 'Active open claim files assigned to ' . $selected_user->name . ' which need ' . ($con1 ? $con1->name : '');
                $labels['FileVariation2']                         = 'Active open claim files assigned to ' . $selected_user->name . ' which need ' . ($con2 ? $con2->name : '');
                $labels['FileVariation3']                         = 'Active open claim files assigned to ' . $selected_user->name . ' which need ' . ($con3 ? $con3->name : '');
                $labels['FileVariation4']                         = 'Active open claim files assigned to ' . $selected_user->name . ' which need ' . ($con4 ? $con4->name : '');
                $labels['FileVariation5']                         = 'Active open claim files assigned to ' . $selected_user->name . ' which need ' . ($con5 ? $con5->name : '');
                $labels['FileVariation6']                         = 'Active open claim files assigned to ' . $selected_user->name . ' which need ' . ($con6 ? $con6->name : '');
                $labels['allPendingAnalysisUserDocuments']        = 'Active documents need analysis assigned to ' . $selected_user->name;
                $labels['allPendingAssignmentUserDocuments']      = 'Active documents assigned to ' . $selected_user->name . ' which not assigned to any file';
                $labels['ActiveOpenClaimFilesNeed1ClaimNotice']   = 'Active open claim files assigned to ' . $selected_user->name . ' which need first claim notice';
                $labels['ActiveOpenClaimFilesNeedFurtherNotice']  = 'Active open claim files assigned to ' . $selected_user->name . ' which need further notice';

            }
        } else {
            $project_dashboard_class_name                     = 'fancy-btn';
            $my_dashboard_class_name                          = 'fancy-btn2';
            $user_dashboard_class_name                        = 'fancy-btn2';
            $labels['allUserDocuments']                       = 'All documents in project';
            $labels['allActiveUserDocuments']                 = 'All active documents in project';
            $labels['allInactiveUserDocuments']               = 'All inactive documents in project';
            $labels['allAssignmentUserDocuments']             = 'All active documents assigned to active open claim files in project';
            $labels['allForClaimUserDocuments']               = 'ALL chronology documents checked for claim';
            $labels['allNeedNarrativeUserDocuments']          = 'All chronology documents need narrative';
            $labels['allHaveConTagsUserDocuments']            = 'All chronology documents have contractual tag';
            $labels['allHaveConTagsNoticeClaimUserDocuments'] = 'All chronology documents and notice of claim';
            $labels['percent1']                               = 'Average percent of analysis complete for active open claim files in project';
            $labels['percent2']                               = 'Average percent of window analysis complete for active open claim files in project';
            $labels['ActiveOpenClaimFileVariation']           = 'All active open claim files with variation in project';
            $labels['FileVariation1']                         = 'All active open claim files in project which need ' . ($con1 ? $con1->name : '');
            $labels['FileVariation2']                         = 'All active open claim files in project which need ' . ($con2 ? $con2->name : '');
            $labels['FileVariation3']                         = 'All active open claim files in project which need ' . ($con3 ? $con3->name : '');
            $labels['FileVariation4']                         = 'All active open claim files in project which need ' . ($con4 ? $con4->name : '');
            $labels['FileVariation5']                         = 'All active open claim files in project which need ' . ($con5 ? $con5->name : '');
            $labels['FileVariation6']                         = 'All active open claim files in project which need ' . ($con6 ? $con6->name : '');
            $labels['allPendingAnalysisUserDocuments']        = 'All active documents need analysis in project';
            $labels['allPendingAssignmentUserDocuments']      = 'All active documents in project which not assigned to any file';
            $labels['ActiveOpenClaimFilesNeed1ClaimNotice']   = 'All active open claim files in project which need first claim notice';
            $labels['ActiveOpenClaimFilesNeedFurtherNotice']  = 'All active open claim files in project which need further notice';
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $allUserDocuments = Document::where('project_id', $user->current_project_id);
        if ($request->user) {
            $allUserDocuments->where('user_id', $selected_user->id);
        }
        $allUserDocuments = $allUserDocuments->count();
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $allActiveUserDocuments = Document::where('project_id', $user->current_project_id)->where('assess_not_pursue', '0');
        if ($request->user) {
            $allActiveUserDocuments->where('user_id', $selected_user->id);
        }
        $allActiveUserDocuments = $allActiveUserDocuments->count();
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $allInactiveUserDocuments = $allUserDocuments - $allActiveUserDocuments;
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $allPendingAnalysisUserDocuments = Document::where('project_id', $user->current_project_id)->where('assess_not_pursue', '0')->where('analysis_complete', '0');
        if ($request->user) {
            $allPendingAnalysisUserDocuments->where('user_id', $selected_user->id);
        }
        $allPendingAnalysisUserDocuments = $allPendingAnalysisUserDocuments->count();
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $allPendingAssignmentUserDocuments = Document::where('project_id', $user->current_project_id)->where('assess_not_pursue', '0')->whereDoesntHave('files');
        if ($request->user) {
            $allPendingAssignmentUserDocuments->where('user_id', $selected_user->id);
        }
        $allPendingAssignmentUserDocuments = $allPendingAssignmentUserDocuments->count();
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $allAssignmentUserDocuments = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        });
        if ($request->user) {
            $allAssignmentUserDocuments->whereHas('file', function ($f) use ($selected_user) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->where('user_id', $selected_user->id)
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });
        } else {
            $allAssignmentUserDocuments->whereHas('file', function ($f) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });
        }
        $allAssignmentUserDocuments = $allAssignmentUserDocuments->count();
        //////////////////////////////////////////////////////////////////////////////////////////////////
        $allNeedNarrativeUserDocuments = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        });
        if ($request->user) {
            $allNeedNarrativeUserDocuments->whereHas('file', function ($f) use ($selected_user) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->where('user_id', $selected_user->id)
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });
        } else {
            $allNeedNarrativeUserDocuments->whereHas('file', function ($f) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });
        }
        $allNeedNarrativeUserDocuments = $allNeedNarrativeUserDocuments->where('narrative', null)->count();
        //////////////////////////////////////////////////////////////////////////////////////////////////
        $allForClaimUserDocuments = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        });
        if ($request->user) {
            $allForClaimUserDocuments->whereHas('file', function ($f) use ($selected_user) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->where('user_id', $selected_user->id)
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });
        } else {
            $allForClaimUserDocuments->whereHas('file', function ($f) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });
        }
        $allForClaimUserDocuments = $allForClaimUserDocuments->where('forClaim', '1')->count();
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        $allHaveConTagsUserDocuments = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        });
        if ($request->user) {
            $allHaveConTagsUserDocuments->whereHas('file', function ($f) use ($selected_user) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->where('user_id', $selected_user->id)
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });
        } else {
            $allHaveConTagsUserDocuments->whereHas('file', function ($f) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });
        }
        $allHaveConTagsUserDocuments = $allHaveConTagsUserDocuments->whereHas('tags')->count();
        //////////////////////////////////////////////////////////////////////////////////////////////////
        $allHaveConTagsNoticeClaimUserDocuments = FileDocument::whereHas('document', function ($q) use ($user) {
            $q->where('project_id', $user->current_project_id)
                ->where('assess_not_pursue', '0');
        });
        if ($request->user) {
            $allHaveConTagsNoticeClaimUserDocuments->whereHas('file', function ($f) use ($selected_user) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->where('user_id', $selected_user->id)
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });
        } else {
            $allHaveConTagsNoticeClaimUserDocuments->whereHas('file', function ($f) {
                $f->where('assess_not_pursue', '0')
                    ->where('closed', '0')
                    ->whereHas('folder', function ($d) {
                        $d->where('potential_impact', '1');
                    });
            });

        }
        $allHaveConTagsNoticeClaimUserDocuments = $allHaveConTagsNoticeClaimUserDocuments->whereHas('tags', function ($t) {
            $t->where('is_notice', '1');
        })->count();
        /////////////////////////////////////////////////////////////////////////////////////////////////////
        $ActiveOpenClaimFilesNeed1ClaimNotice = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $ActiveOpenClaimFilesNeed1ClaimNotice->where('user_id', $selected_user->id);

        }
        $ActiveOpenClaimFilesNeed1ClaimNotice = $ActiveOpenClaimFilesNeed1ClaimNotice->where(function ($query) use ($user) {
            // No file documents at all
            $query->whereDoesntHave('fileDocuments')
            // Or file documents without the notice tag
                ->orWhereDoesntHave('fileDocuments', function ($d) use ($user) {
                    $d->whereHas('tags', function ($t) {
                        $t->where('is_notice', '1');
                    })
                        ->whereHas('document', function ($q) use ($user) {
                            $q->where('project_id', $user->current_project_id)
                                ->where('assess_not_pursue', '0');
                        });
                });
        })->count();
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $ActiveOpenClaimFilesNeedFurtherNotice = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $ActiveOpenClaimFilesNeedFurtherNotice->where('user_id', $selected_user->id);
        }
        $ActiveOpenClaimFilesNeedFurtherNotice = $ActiveOpenClaimFilesNeedFurtherNotice->whereHas('fileDocuments', function ($d) use ($user) {
            $d->whereHas('tags', function ($t) {
                $t->where('is_notice', '1');
            })
                ->whereHas('document', function ($q) use ($user) {
                    $q->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0')
                        ->where('start_date', '<', Carbon::now()->subMonth()->format('Y-m-d'));
                });
        })->count();
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        $ActiveClaimFile = ProjectFile::where('project_id', $user->current_project_id)->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $ActiveClaimFile->where('user_id', $selected_user->id);
        }
        $ActiveClaimFile = $ActiveClaimFile->count();
        //////////////////////////////////////////////////////////////////////////////////////////////////
        $ActiveOpenClaimFile = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $ActiveOpenClaimFile->where('user_id', $selected_user->id);
        }
        $ActiveOpenClaimFile = $ActiveOpenClaimFile->count();
        //////////////////////////////////////////////////////////////////////////////////////////////////
        $ActiveClosedClaimFile = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '1')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $ActiveClosedClaimFile->where('user_id', $selected_user->id);
        }
        $ActiveClosedClaimFile = $ActiveClosedClaimFile->count();
        //////////////////////////////////////////////////////////////////////////////////////////////////
        $ActiveOpenClaimFileTime = ProjectFile::where('project_id', $user->current_project_id)->where('time', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $ActiveOpenClaimFileTime->where('user_id', $selected_user->id);
        }
        $ActiveOpenClaimFileTime = $ActiveOpenClaimFileTime->count();
        ///////////////////////////////////////////////////////////////////////////////////////////////
        $ActiveOpenClaimFileProlongationCost = ProjectFile::where('project_id', $user->current_project_id)->where('prolongation_cost', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $ActiveOpenClaimFileProlongationCost->where('user_id', $selected_user->id);
        }
        $ActiveOpenClaimFileProlongationCost = $ActiveOpenClaimFileProlongationCost->count();
        ///////////////////////////////////////////////////////////////////////////////////////////////
        $ActiveOpenClaimFileVariation = ProjectFile::where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $ActiveOpenClaimFileVariation->where('user_id', $selected_user->id);
        }
        $ActiveOpenClaimFileVariation = $ActiveOpenClaimFileVariation->count();
        //////////////////////////////////////////////////////////////////////////////////////////////
        $ActiveOpenClaimFileDisruption = ProjectFile::where('project_id', $user->current_project_id)->where('disruption_cost', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $ActiveOpenClaimFileDisruption->where('user_id', $selected_user->id);
        }
        $ActiveOpenClaimFileDisruption = $ActiveOpenClaimFileDisruption->count();
        //////////////////////////////////////////////////////////////////////////////////////////////////
        $needChronology = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $needChronology->where('user_id', $selected_user->id);
        }
        $needChronology = $needChronology->whereDoesntHave('fileDocuments')->count();
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $needSynopsis = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        });
        if ($request->user) {
            $needSynopsis->where('user_id', $selected_user->id);
        }
        $needSynopsis = $needSynopsis->whereDoesntHave('fileAttachment', function ($a) {
            $a->where('section', '1');
        })->count();
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $needContractualA = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileAttachment', function ($a) {
            $a->where('section', '2');
        });
        if ($request->user) {
            $needContractualA->where('user_id', $selected_user->id);
        }
        $needContractualA = $needContractualA->count();
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        $needCauseEffectA = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');
        })->whereDoesntHave('fileAttachment', function ($a) {
            $a->where('section', '3');
        });
        if ($request->user) {
            $needCauseEffectA->where('user_id', $selected_user->id);
        }
        $needCauseEffectA = $needCauseEffectA->count();
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        $FileVariation1['label'] = $con1 ? $con1->name : '';
        $FileVariation1_value    = ProjectFile::where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        });
        if ($request->user) {
            $FileVariation1_value->where('user_id', $selected_user->id)->whereHas('fileDocuments', function ($d) use ($user, $selected_user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [1, 2, 3, 4, 5, 6]);
                })->whereHas('document', function ($q) use ($user, $selected_user) {
                    $q->where('user_id', $selected_user->id)
                        ->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        } else {
            $FileVariation1_value->whereHas('fileDocuments', function ($d) use ($user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [1, 2, 3, 4, 5, 6]);
                })->whereHas('document', function ($q) use ($user) {
                    $q->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        }

        $FileVariation1_value    = $FileVariation1_value->count();
        $FileVariation1['value'] = $ActiveOpenClaimFileVariation - $FileVariation1_value;
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $FileVariation2['label'] = $con2 ? $con2->name : '';
        $FileVariation2_value    = ProjectFile::where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        });
        if ($request->user) {
            $FileVariation2_value->where('user_id', $selected_user->id)->whereHas('fileDocuments', function ($d) use ($user, $selected_user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [2, 3, 4, 5, 6]);
                })->whereHas('document', function ($q) use ($user, $selected_user) {
                    $q->where('user_id', $selected_user->id)
                        ->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        } else {
            $FileVariation2_value->whereHas('fileDocuments', function ($d) use ($user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [2, 3, 4, 5, 6]);
                })->whereHas('document', function ($q) use ($user) {
                    $q->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        }

        $FileVariation2_value    = $FileVariation2_value->count();
        $FileVariation2['value'] = $ActiveOpenClaimFileVariation - $FileVariation2_value;
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        $con3                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 3)->first();
        $FileVariation3['label'] = $con3 ? $con3->name : '';
        $FileVariation3_value    = ProjectFile::where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        });
        if ($request->user) {
            $FileVariation3_value->where('user_id', $selected_user->id)->whereHas('fileDocuments', function ($d) use ($user, $selected_user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [3, 4, 5, 6]);
                })->whereHas('document', function ($q) use ($user, $selected_user) {
                    $q->where('user_id', $selected_user->id)
                        ->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        } else {
            $FileVariation3_value->whereHas('fileDocuments', function ($d) use ($user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [3, 4, 5, 6]);
                })->whereHas('document', function ($q) use ($user) {
                    $q->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        }

        $FileVariation3_value    = $FileVariation3_value->count();
        $FileVariation3['value'] = $ActiveOpenClaimFileVariation - $FileVariation3_value;
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        $con4                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 4)->first();
        $FileVariation4['label'] = $con4 ? $con4->name : '';
        $FileVariation4_value    = ProjectFile::where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        });
        if ($request->user) {
            $FileVariation4_value->where('user_id', $selected_user->id)->whereHas('fileDocuments', function ($d) use ($user, $selected_user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [4, 5, 6]);
                })->whereHas('document', function ($q) use ($user, $selected_user) {
                    $q->where('user_id', $selected_user->id)
                        ->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        } else {
            $FileVariation4_value->whereHas('fileDocuments', function ($d) use ($user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [4, 5, 6]);
                })->whereHas('document', function ($q) use ($user) {
                    $q->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        }

        $FileVariation4_value    = $FileVariation4_value->count();
        $FileVariation4['value'] = $ActiveOpenClaimFileVariation - $FileVariation4_value;
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $con5                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 5)->first();
        $FileVariation5['label'] = $con5 ? $con5->name : '';
        $FileVariation5_value    = ProjectFile::where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        });
        if ($request->user) {
            $FileVariation5_value->where('user_id', $selected_user->id)->whereHas('fileDocuments', function ($d) use ($user, $selected_user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [5, 6]);
                })->whereHas('document', function ($q) use ($user, $selected_user) {
                    $q->where('user_id', $selected_user->id)
                        ->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        } else {
            $FileVariation5_value->whereHas('fileDocuments', function ($d) use ($user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [5, 6]);
                })->whereHas('document', function ($q) use ($user) {
                    $q->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        }

        $FileVariation5_value    = $FileVariation5_value->count();
        $FileVariation5['value'] = $ActiveOpenClaimFileVariation - $FileVariation5_value;
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        $con6                    = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->where('var_process', 6)->first();
        $FileVariation6['label'] = $con6 ? $con6->name : '';
        $FileVariation6_value    = ProjectFile::where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->whereHas('folder', function ($f) {
            $f->where('potential_impact', '1');

        });
        if ($request->user) {
            $FileVariation6_value->where('user_id', $selected_user->id)->whereHas('fileDocuments', function ($d) use ($user, $selected_user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [6]);
                })->whereHas('document', function ($q) use ($user, $selected_user) {
                    $q->where('user_id', $selected_user->id)
                        ->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        } else {
            $FileVariation6_value->whereHas('fileDocuments', function ($d) use ($user) {
                $d->whereHas('tags', function ($t) {
                    $t->whereIn('var_process', [6]);
                })->whereHas('document', function ($q) use ($user) {
                    $q->where('project_id', $user->current_project_id)
                        ->where('assess_not_pursue', '0');
                });
            });
        }
        $FileVariation6_value    = $FileVariation6_value->count();
        $FileVariation6['value'] = $ActiveOpenClaimFileVariation - $FileVariation6_value;
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $analysis_complete_value = ProjectFile::where('project_id', $user->current_project_id)
            ->where('closed', '0');
        if ($request->user) {
            $analysis_complete_value->where('user_id', $selected_user->id);

        }
        $analysis_complete_value = $analysis_complete_value->where('assess_not_pursue', '0')
            ->whereHas('folder', function ($f) {
                $f->where('potential_impact', '1');
            })
            ->avg(DB::raw('COALESCE(analyses_complete, 0)')) ?? 0;

        $percent1 = $ActiveOpenClaimFile > 0 ? $analysis_complete_value : 0;
        $percent2 = 0;

        return view('project_dashboard.home', compact('allForClaimUserDocuments', 'allAssignmentUserDocuments', 'allActiveUserDocuments',
            'allInactiveUserDocuments', 'users', 'allUserDocuments', 'percent1', 'percent2', 'project_users',
            'ActiveClaimFile', 'ActiveOpenClaimFile', 'ActiveClosedClaimFile',
            'ActiveOpenClaimFileTime', 'ActiveOpenClaimFileProlongationCost', 'ActiveOpenClaimFileVariation', 'ActiveOpenClaimFileDisruption',
            'needChronology', 'needSynopsis', 'needContractualA', 'needCauseEffectA',
            'allPendingAnalysisUserDocuments', 'allPendingAssignmentUserDocuments',
            'allNeedNarrativeUserDocuments', 'project', 'assigned_users',
            'allHaveConTagsUserDocuments', 'allHaveConTagsNoticeClaimUserDocuments', 'ActiveOpenClaimFilesNeed1ClaimNotice', 'ActiveOpenClaimFilesNeedFurtherNotice',
            'FileVariation1', 'FileVariation2', 'FileVariation3', 'FileVariation4', 'FileVariation5', 'FileVariation6',
            'project_dashboard_class_name', 'my_dashboard_class_name', 'user_dashboard_class_name', 'labels'));

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
