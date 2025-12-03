<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Document;
use App\Models\ExportFormate;
use App\Models\FileAttachment;
use App\Models\FileDocument;
use App\Models\GanttChartDocData;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectFolder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class FileController extends ApiController
{
    public function index(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users   = $project->assign_users;
        $user    = auth()->user();
        if ($request->filter == 'need_1_claim_notice') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->where(function ($query) use ($user) {
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
            })->get();

        } elseif ($request->filter == 'need_further_notice') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->whereHas('fileDocuments', function ($d) use ($user) {
                $d->whereHas('tags', function ($t) {
                    $t->where('is_notice', '1');
                })
                    ->whereHas('document', function ($q) use ($user) {
                        $q->where('project_id', $user->current_project_id)
                            ->where('assess_not_pursue', '0')
                            ->where('start_date', '<', Carbon::now()->subMonth()->format('Y-m-d'));
                    });
            })->get();

        } elseif ($request->filter == 'ActiveClaimFile') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->get();

        } elseif ($request->filter == 'ActiveOpenClaimFile') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->get();

        } elseif ($request->filter == 'ActiveClosedClaimFile') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '1')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->get();

        } elseif ($request->filter == 'ActiveOpenClaimFileTime') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('time', '1')->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->get();

        } elseif ($request->filter == 'ActiveOpenClaimFileProlongationCost') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('prolongation_cost', '1')->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->get();

        } elseif ($request->filter == 'ActiveOpenClaimFileDisruption') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('disruption_cost', '1')->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->get();

        } elseif ($request->filter == 'ActiveOpenClaimFileVariation') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('variation', '1')->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->get();

        } elseif ($request->filter == 'needChronology') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->whereDoesntHave('fileDocuments')->get();

        } elseif ($request->filter == 'needSynopsis') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->whereDoesntHave('fileAttachment', function ($a) {
                $a->where('section', '1');
            })->get();

        } elseif ($request->filter == 'needContractualA') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->whereDoesntHave('fileAttachment', function ($a) {
                $a->where('section', '2');
            })->get();

        } elseif ($request->filter == 'needCauseEffectA') {
            $folder    = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->where('potential_impact', '1')->first();
            $all_files = ProjectFile::where('project_id', $user->current_project_id)->where('closed', '0')->where('assess_not_pursue', '0')->where('folder_id', $folder->id);
            if ($request->authUser != 'non') {
                $selected_user = User::where('code', $request->authUser)->first();
                $all_files->where('user_id', $selected_user->id);

            }
            $all_files = $all_files->whereDoesntHave('fileAttachment', function ($a) {
                $a->where('section', '3');
            })->get();

        } else {
            $folder    = ProjectFolder::findOrFail($user->current_folder_id);
            $all_files = ProjectFile::where('folder_id', $folder->id)->orderBy('code', 'asc')->get();

        }
        $folders = ProjectFolder::where('id', '!=', $folder->id)->where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');

        return view('project_dashboard.project_files.index', compact('all_files', 'folders', 'folder', 'users'));
    }

    public function create()
    {
        $project       = Project::findOrFail(auth()->user()->current_project_id);
        $users         = $project->assign_users;
        $user          = auth()->user();
        $folder        = ProjectFolder::findOrFail($user->current_folder_id);
        $stake_holders = $project->stakeHolders;
        $milestones    = Milestone::where('project_id', auth()->user()->current_project_id)->get();
        $all_documents = Document::where('project_id', auth()->user()->current_project_id)->orderBy('start_date', 'asc')->orderBy('reference', 'asc')->get();

        return view('project_dashboard.project_files.create', compact('folder', 'all_documents', 'milestones', 'users', 'stake_holders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required', // 10MB max
            'owner_id'   => 'required|exists:users,id',
            'milestones' => 'array|nullable',
        ]);
        do {
            $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (ProjectFile::where('slug', $invitation_code)->exists());

        $file = ProjectFile::create(['name' => $request->name,
            'slug'                              => $invitation_code,
            'code'                              => $request->code,
            'user_id'                           => $request->owner_id,
            'project_id'                        => auth()->user()->current_project_id, 'against_id' => $request->against_id, 'start_date' => $request->start_date,
            'end_date'                          => $request->end_date, 'folder_id'                  => auth()->user()->current_folder_id,
            'notes'                             => $request->notes,
            'description1'                      => $request->description1,
            'description2'                      => $request->description2,
            'analyses_complete'                 => intval($request->Percentage_Analysis_Complete)]);

        if ($request->time) {
            $file->time = '1';
        }
        if ($request->prolongation_cost) {
            $file->prolongation_cost = '1';
        }
        if ($request->disruption_cost) {
            $file->disruption_cost = '1';
        }
        if ($request->variation) {
            $file->variation = '1';
        }
        if ($request->closed) {
            $file->closed = '1';
        }
        if ($request->assess_not_pursue) {
            $file->assess_not_pursue = '1';
        }
        if ($request->sup_doc_1) {
            $fileDoc     = FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $file->id, 'document_id' => $request->sup_doc_1]);
            $start_date  = $fileDoc->document->start_date;
            $end_date    = $fileDoc->document->end_date;
            $gantt_chart = GanttChartDocData::create(['file_document_id' => $fileDoc->id]);

            $gantt_chart->lp_sd = $start_date;
            $gantt_chart->lp_fd = $end_date;

            $sections[] = [
                'sd'    => $start_date,
                'fd'    => $end_date,
                'color' => '00008B',
            ];

            $gantt_chart->cur_sections = json_encode($sections);
            if ($end_date == null) {
                $gantt_chart->cur_type = 'M';
            }
            $gantt_chart->save();
            $file->sup_doc_1 = $request->sup_doc_1;
        }
        if ($request->sup_doc_2) {
            $fileDoc     = FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $file->id, 'document_id' => $request->sup_doc_2]);
            $start_date  = $fileDoc->document->start_date;
            $end_date    = $fileDoc->document->end_date;
            $gantt_chart = GanttChartDocData::create(['file_document_id' => $fileDoc->id]);

            $gantt_chart->lp_sd = $start_date;
            $gantt_chart->lp_fd = $end_date;

            $sections[] = [
                'sd'    => $start_date,
                'fd'    => $end_date,
                'color' => '00008B',
            ];

            $gantt_chart->cur_sections = json_encode($sections);
            if ($end_date == null) {
                $gantt_chart->cur_type = 'M';
            }
            $gantt_chart->save();
            $file->sup_doc_2 = $request->sup_doc_2;
        }
        $file->milestones = $request->milestones ? implode(',', $request->milestones) : null;
        $file->save();

        return redirect('/project/files')->with('success', 'File Created successfully.');

    }

    public function edit($id)
    {
        $project       = Project::findOrFail(auth()->user()->current_project_id);
        $users         = $project->assign_users;
        $user          = auth()->user();
        $folder        = ProjectFolder::findOrFail($user->current_folder_id);
        $stake_holders = $project->stakeHolders;
        $file          = ProjectFile::where('slug', $id)->first();
        $milestones    = Milestone::where('project_id', auth()->user()->current_project_id)->get();
        $all_documents = $file->documents()->orderBy('start_date', 'asc')->orderBy('reference', 'asc')->get();

        return view('project_dashboard.project_files.edit', compact('folder','all_documents', 'milestones', 'users', 'stake_holders', 'file'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required', // 10MB max
            'owner_id'   => 'required|exists:users,id',
            'milestones' => 'array|nullable',
        ]);

        ProjectFile::where('id', $id)->update(['name' => $request->name, 'code'             => $request->code,
            'user_id'                                     => $request->owner_id,
            'against_id'                                  => $request->against_id, 'start_date' => $request->start_date,
            'end_date'                                    => $request->end_date,
            'notes'                                       => $request->notes,
            'description1'                      => $request->description1,
            'description2'                      => $request->description2,
            'analyses_complete'                           => intval($request->Percentage_Analysis_Complete)]);
        $file             = ProjectFile::findOrFail($id);
        $file->milestones = $request->milestones ? implode(',', $request->milestones) : null;
        if ($request->time) {
            $file->time = '1';
        } else {
            $file->time = '0';
        }
        if ($request->prolongation_cost) {
            $file->prolongation_cost = '1';
        } else {
            $file->prolongation_cost = '0';
        }
        if ($request->disruption_cost) {
            $file->disruption_cost = '1';
        } else {
            $file->disruption_cost = '0';
        }
        if ($request->variation) {
            $file->variation = '1';
        } else {
            $file->variation = '0';
        }
        if ($request->closed) {
            $file->closed = '1';
        } else {
            $file->closed = '0';
        }
        if ($request->assess_not_pursue) {
            $file->assess_not_pursue = '1';
        } else {
            $file->assess_not_pursue = '0';
        }
        if ($request->sup_doc_1) {
            $fileDoc = FileDocument::where('file_id', $file->id)->where('document_id', $request->sup_doc_1)->first();
            if (! $fileDoc) {
                $fileDoc     = FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $file->id, 'document_id' => $request->sup_doc_1]);
                $start_date  = $fileDoc->document->start_date;
                $end_date    = $fileDoc->document->end_date;
                $gantt_chart = GanttChartDocData::create(['file_document_id' => $fileDoc->id]);

                $gantt_chart->lp_sd = $start_date;
                $gantt_chart->lp_fd = $end_date;

                $sections[] = [
                    'sd'    => $start_date,
                    'fd'    => $end_date,
                    'color' => '00008B',
                ];

                $gantt_chart->cur_sections = json_encode($sections);
                if ($end_date == null) {
                    $gantt_chart->cur_type = 'M';
                }
                $gantt_chart->save();
            }
            $file->sup_doc_1 = $request->sup_doc_1;
        }
        if ($request->sup_doc_2) {
            $fileDoc = FileDocument::where('file_id', $file->id)->where('document_id', $request->sup_doc_2)->first();
            if (! $fileDoc) {
                $fileDoc     = FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $file->id, 'document_id' => $request->sup_doc_2]);
                $start_date  = $fileDoc->document->start_date;
                $end_date    = $fileDoc->document->end_date;
                $gantt_chart = GanttChartDocData::create(['file_document_id' => $fileDoc->id]);

                $gantt_chart->lp_sd = $start_date;
                $gantt_chart->lp_fd = $end_date;

                $sections[] = [
                    'sd'    => $start_date,
                    'fd'    => $end_date,
                    'color' => '00008B',
                ];

                $gantt_chart->cur_sections = json_encode($sections);
                if ($end_date == null) {
                    $gantt_chart->cur_type = 'M';
                }
                $gantt_chart->save();
            }
            $file->sup_doc_2 = $request->sup_doc_2;
        }
        $file->save();

        return redirect('/project/files')->with('success', 'File Updated successfully.');
    }

    public function changeOwner(Request $request)
    {
        $request->validate([
            'file_id'      => 'required|exists:project_files,id',
            'new_owner_id' => 'required|exists:users,id',
        ]);

        $file          = ProjectFile::find($request->file_id);
        $file->user_id = $request->new_owner_id;
        $file->save();

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $file    = ProjectFile::where('id', $id)->first();
        $user    = auth()->user();
        $Archive = ProjectFolder::where('account_id', $user->current_account_id)
            ->where('project_id', $user->current_project_id)->where('name', 'Archive')
            ->first();
        $Folder = ProjectFolder::where('account_id', $user->current_account_id)
            ->where('project_id', $user->current_project_id)->where('name', 'Recycle Bin')
            ->first();
        if ($file->older_folder_id == null) {

            $file->older_folder_id = $file->folder_id;
            $file->folder_id       = $Folder->id;
            $file->save();
        } elseif ($file->folder_id == $Archive->id) {
            $file->folder_id = $Folder->id;
            $file->save();
        } else {
            FileAttachment::where('file_id', $file->id)->delete();
            $file_doc_IDs = FileDocument::where('file_id', $file->id)->pluck('id');
            GanttChartDocData::whereIn('id', $file_doc_IDs)->delete();
            FileDocument::where('file_id', $file->id)->delete();
            $file->delete();

        }

        return redirect('/project/files')->with('success', 'File Deleted successfully.');

    }

    public function archive($id)
    {
        $file   = ProjectFile::where('id', $id)->first();
        $user   = auth()->user();
        $Folder = ProjectFolder::where('account_id', $user->current_account_id)
            ->where('project_id', $user->current_project_id)->where('name', 'Archive')
            ->first();
        if ($file->older_folder_id == null) {
            $file->older_folder_id = $file->folder_id;
        }
        $file->folder_id = $Folder->id;
        $file->save();

        return redirect('/project/files')->with('success', 'File Archive successfully.');

    }

    public function exportWordClaimDocs(Request $request)
    {

        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        $chapter       = $request->Chapter; // Dynamic chapter number
        $sectionNumber = $request->Section; // Dynamic section number
        $phpWord->addNumberingStyle(
            'multilevel',
            [
                'type'     => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels'   => [
                    ['Heading0', 'format' => 'decimal', 'text' => '%1.', 'start' => (int) $chapter],
                    ['Heading1', 'format' => 'decimal', 'text' => '%1.%2', 'start' => (int) $sectionNumber],
                    ['Heading2', 'format' => 'decimal', 'text' => '%1.%2.%3', 'start' => 1],
                    ['Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.%4', 'start' => 1],
                    ['Heading3', 'format' => 'decimal', 'text' => ''],
                ],
            ]
        );

        $phpWord->addNumberingStyle(
            'multilevel2',
            [
                'type'     => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels'   => [
                    ['Heading5', 'format' => 'decimal', 'text' => '%1.'],
                    ['Heading6', 'format' => 'decimal', 'text' => '%1.%2.'],
                    ['Heading7', 'format' => 'decimal', 'text' => '%1.%2.%3.'],

                    // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                    // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                ],
            ]
        );
        $phpWord->addNumberingStyle(
            'unordered',
            [
                'type'   => 'multilevel', // Use 'multilevel' for bullet points
                'levels' => [
                    ['format' => 'bullet', 'text' => '•', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '◦', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '▪', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '■', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '☑', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➤', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➥', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➟', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➡', 'alignment' => 'left'],

                ],
            ]
        );
        $formate = ExportFormate::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->first();
        if ($formate) {
            $formate_values = $formate->value = json_decode($formate->value, true);
        } else {
            $formate_values = null;
        }
        // Define styles for headings
        $GetStandardStylesH1 = [
            'name'      => $formate_values ? $formate_values['h1']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h1']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['h1']['standard']['size']) : 24,
            'bold'      => $formate_values ? ($formate_values['h1']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['h1']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h1']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH1 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h1']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h1']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h1']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h1']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['h1']['paragraph']['indentation']['hanging'] * 1436) : 1077,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h1']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h1']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h1']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h1']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];
        $GetStandardStylesH2 = [
            'name'      => $formate_values ? $formate_values['h2']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h2']['standard']['alignment'] : 'left',
            'size'      => $formate_values ? intval($formate_values['h2']['standard']['size']) : 16,
            'bold'      => $formate_values ? ($formate_values['h2']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['h2']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h2']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH2 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h2']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h2']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h2']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h2']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['h2']['paragraph']['indentation']['hanging'] * 1436) : 1077,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h2']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h2']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h2']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h2']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];

        $GetStandardStylesH3 = [
            'name'      => $formate_values ? $formate_values['h3']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h3']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['h3']['standard']['size']) : 14,
            'bold'      => $formate_values ? ($formate_values['h3']['standard']['bold'] == '1' ? true : false) : false,
            'italic'    => $formate_values ? ($formate_values['h3']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h3']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH3 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h3']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h3']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h3']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h3']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['h3']['paragraph']['indentation']['hanging'] * 1436) : 1077,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h3']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h3']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h3']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h3']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];

        $GetStandardStylesSubtitle = [
            'name'      => $formate_values ? $formate_values['subtitle']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['subtitle']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['subtitle']['standard']['size']) : 14,
            'bold'      => $formate_values ? ($formate_values['subtitle']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['subtitle']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['subtitle']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleSubtitle = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['subtitle']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['subtitle']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['subtitle']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['subtitle']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['subtitle']['paragraph']['indentation']['hanging'] * 1436) : 0,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['subtitle']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['subtitle']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['subtitle']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['subtitle']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];
        $GetStandardStylesP = [
            'name'      => $formate_values ? $formate_values['body']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['body']['standard']['size']) : 11,
            'bold'      => false,
            'italic'    => false,
            'underline' => 'none',
        ];

        $phpWord->addParagraphStyle('listParagraphStyle', [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                'hanging'   => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['hanging'] * 1436) : 1077,
                'firstLine' => 0,
            ],
            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ]);

        $phpWord->addParagraphStyle('listParagraphStyle2', [
            'spaceBefore'       => 0,
            'spaceAfter'        => 20,
            'lineHeight'        => 1,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) + 350 : 1350,
                'hanging'   => 337.5,
                'firstLine' => 0,
            ],
            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ]);

        $phpWord->addTitleStyle(1, $GetStandardStylesH1, $GetParagraphStyleH1);
        $phpWord->addTitleStyle(2, $GetStandardStylesH2, array_merge($GetParagraphStyleH2, ['numStyle' => 'multilevel', 'numLevel' => 1]));
        $phpWord->addTitleStyle(3, $GetStandardStylesH3, $GetParagraphStyleH3);

        $file = ProjectFile::where('slug', $request->file_id111)->first();
        // Header (Level 1 Outline)
        $header = $file->name;
        $header = str_replace('&', '&amp;', $header);
        $section->addTitle($header, 2);

        $paragraphs = FileAttachment::where('section', '1')->where('file_id', $file->id);
        if ($request->forclaimdocs) {
            $paragraphs->where('forClaim', '1');
        }

        $paragraphs = $paragraphs->orderBy('order', 'asc')->get();

        if (count($paragraphs) > 0) {
            $subtitle1 = $request->subtitle1;
            $subtitle1 = str_replace('&', '&amp;', $subtitle1);
            $section->addText($subtitle1, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
            foreach ($paragraphs as $index => $paragraph) {
                // dd($paragraphs);
                $listItemRun  = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                $existedList1 = false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;

                if ($paragraph->narrative == null) {
                    $listItemRun->addText('____________.');
                } else {
                    if (! $containsHtml) {
                        $listItemRun->addText($paragraph->narrative . '.');
                    } else {

                        $paragraph_ = $this->fixParagraphsWithImages($paragraph->narrative);

                        // preg_match('/<ol>.*?<\/ol>/s', $paragraph_, $olMatches);
                        // $olContent = $olMatches[0] ?? ''; // Get the <ol> content if it exists
                        // preg_match('/<ul>.*?<\/ul>/s', $paragraph_, $ulMatches);
                        // $ulContent = $ulMatches[0] ?? ''; // Get the <ol> content if it exists

                        // Step 2: Remove the <ol> content from the main paragraph
                        // $paragraphWithoutOl = preg_replace('/<ol>.*?<\/ol>/s', '', $paragraph);
                        // $paragraphWithoutOlUl = preg_replace('/<ul>.*?<\/ul>/s', '', $paragraphWithoutOl);
                        $paragraphWithoutImagesAndBreaks = preg_replace('/<(br)[^>]*>/i', '', $paragraph_);

                        // Step 2: Remove empty <p></p> tags
                        $paragraphWithoutEmptyParagraphs = preg_replace('/<p>\s*<\/p>/i', '', $paragraphWithoutImagesAndBreaks);

                        $paragraphsArray = $this->splitHtmlToArray($paragraphWithoutEmptyParagraphs);

                        // Step 3: Split into an array of <p> tags
                        // $paragraphsArray = preg_split('/(?=<p>)|(?<=<\/p>)/', $paragraphWithoutEmptyParagraphs);

                        // Step 4: Filter out empty elements
                        $paragraphsArray = array_filter($paragraphsArray, function ($item) {
                            return ! empty(trim($item));
                        });

                        // Step 5: Add each <p> tag to the document with a newline after it
                        foreach ($paragraphsArray as $index2 => $pTag) {
                            // dd($paragraphsArray);
                            if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath       = $matches[1];                                 // Extract image path
                                $altText       = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                                $fullImagePath = public_path($imgPath);                       // Convert relative path to absolute

                                if ($existedList1) {
                                    if (file_exists($fullImagePath)) {
                                        $textRun = $section->addTextRun([
                                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation' => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                        ]);

                                        // Add Image
                                        $shape = $textRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        // Add Caption (Alt text)
                                        if (! empty($altText)) {
                                            $textRun->addTextBreak(); // New line
                                            $textRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                                'alignment'                               => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                                'size'                                    => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                                'bold'                                    => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                                'italic'                                  => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                                'underline'                               => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                        }

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $textRun->addTextBreak();
                                            }

                                        }
                                    }
                                } else {
                                    if (file_exists($fullImagePath)) {
                                        // Add Image
                                        $listItemRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        // Add Caption (Alt text)
                                        if (! empty($altText)) {
                                            $listItemRun->addTextBreak(); // New line
                                            $listItemRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                                'alignment'                                   => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                                'size'                                        => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                                'bold'                                        => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                                'italic'                                      => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                                'underline'                                   => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                        }

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }
                                }
                            } elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath = $matches[1]; // Extract image path

                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute

                                if ($existedList1) {
                                    if (file_exists($fullImagePath)) {
                                        $textRun = $section->addTextRun([
                                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation' => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                        ]);

                                        // Add Image
                                        $textRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $textRun->addTextBreak();
                                            }

                                        }
                                    }
                                } else {
                                    if (file_exists($fullImagePath)) {
                                        // Add Image
                                        $listItemRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }

                                    }
                                }
                            } elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1' . $index . $index2 . '1',
                                    [
                                        'type'     => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels'   => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.'],

                                            // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                            // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                        ],
                                    ]
                                );
                                if (preg_match_all('/<li>(.*?)<\/li>/', $olMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                                          // Add a nested list item
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1' . $index . $index2 . '1', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                                          // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList1 = true;
                            } elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                // Add a nested list item
                                                                                                                                // dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';
                                        Html::addHtml($unNestedListItemRun, $item, false, false);
                                    }
                                }

                                $existedList1 = true;
                            } else {

                                // If the paragraph contains only text (including <span>, <strong>, etc.)
                                try {
                                    if ($existedList1) {

                                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation'       => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],

                                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                                        ]);
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun2->addTextBreak();
                                            }

                                        }
                                    } else {
                                        // $pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun, $pTag, false, false);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }

                                } catch (\Exception $e) {
                                    error_log('Error adding HTML: ' . $e->getMessage());
                                }
                            }

                        }

                    }
                }

            }
        }

        // $paragraphs=FileDocument::where('file_id',$file->id)->where('forClaim','1')->orderBy()->get();
        $paragraphs = FileDocument::with(['document', 'note'])
            ->where('file_id', $file->id);
        if ($request->forclaimdocs) {
            $paragraphs->where('forClaim', '1');
        }

        $paragraphs = $paragraphs->get()
            ->sortBy([
                fn($a, $b) => ($a->document->start_date ?? $a->note->start_date ?? '9999-12-31')
                <=> ($b->document->start_date ?? $b->note->start_date ?? '9999-12-31'),
                fn($a, $b) => $a->sn <=> $b->sn,
            ])
            ->values();
        if (count($paragraphs) > 0) {
            $subtitle2 = $request->subtitle2;
            $subtitle2 = str_replace('&', '&amp;', $subtitle2);
            $section->addText($subtitle2, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
            $GetStandardStylesFootNotes = [
                'name'      => $formate_values ? $formate_values['footnote']['standard']['name'] : 'Calibri',
                'alignment' => $formate_values ? $formate_values['footnote']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                'size'      => $formate_values ? intval($formate_values['footnote']['standard']['size']) : 9,
                'bold'      => $formate_values ? ($formate_values['footnote']['standard']['bold'] == '1' ? true : false) : false,
                'italic'    => $formate_values ? ($formate_values['footnote']['standard']['italic'] == '1' ? true : false) : false,
                'underline' => $formate_values ? ($formate_values['footnote']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

            ];
            $GetParagraphStyleFootNotes = [
                'spaceBefore' => $formate_values ? ((int) $formate_values['footnote']['paragraph']['spaceBefore'] * 20) : 0,
                'spaceAfter'  => $formate_values ? ((int) $formate_values['footnote']['paragraph']['spaceAfter'] * 20) : 0,
                'lineHeight'  => $formate_values ? (float) $formate_values['footnote']['paragraph']['lineHeight'] : 1,
                'indentation' => [
                    'left'      => $formate_values ? ((float) $formate_values['footnote']['paragraph']['indentation']['left'] * 1436) : 0,
                    'hanging'   => $formate_values ? ((float) $formate_values['footnote']['paragraph']['indentation']['hanging'] * 1436) : 0,
                    'firstLine' => 0,
                ],
            ];
            $x = intval($request->Start);
            foreach ($paragraphs as $index => $paragraph) {
                // dd($paragraphs);
                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                $existedList = false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;
                if ($paragraph->document) {
                    $date = date('d F Y', strtotime($paragraph->document->start_date));

                    // Add the main sentence
                    $listItemRun->addText('On ', $GetStandardStylesP);

                    // Add the date with a footnote
                    $listItemRun->addText($date, $GetStandardStylesP);
                    $footnote         = $listItemRun->addFootnote($GetParagraphStyleFootNotes);
                    $Exhibit          = true;
                    $dated            = true;
                    $senderAndDocType = true;
                    $hint             = '';
                    if ($request->formate_type2 == 'reference') {
                        $hint = $paragraph->document->reference . '.';
                    } elseif ($request->formate_type2 == 'dateAndReference') {

                        $date2 = date('y_m_d', strtotime($paragraph->document->start_date));
                        $hint  = preg_replace('/_/', '', $date2) . ' - ' . $paragraph->document->reference . '.';
                    } elseif ($request->formate_type2 == 'formate') {
                        $sn         = $request->sn2;
                        $prefix     = $request->prefix2;
                        $listNumber = "$prefix" . str_pad($x, $sn, '0', STR_PAD_LEFT);
                        $hint       = $listNumber . ': ';
                        if ($request->fromForL_E) {
                            $text = strtolower(($paragraph->document->docType->name ?? '') . ' ' . ($paragraph->document->docType->description ?? ''));

                            if (preg_match('/\b(letter|email|e-mail)\b/', $text)) {

                                $from = $paragraph->document->fromStakeHolder ? $paragraph->document->fromStakeHolder->narrative . "'s " : '';
                            } else {
                                $from = '';
                            }
                        } else {
                            $from = $paragraph->document->fromStakeHolder ? $paragraph->document->fromStakeHolder->narrative . "'s " : '';
                        }
                        $type = $paragraph->document->docType->name;
                        $hint .= $from . $type . ' ';
                        if (str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $paragraph->document->docType->name)), 'email') || str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $paragraph->document->docType->description)), 'email')) {
                            $ref_part = $request->ref_part2;
                            if ($ref_part == 'option1') {
                                $hint .= ', ';
                            } elseif ($ref_part == 'option2') {

                                $hint .= 'From: ' . $paragraph->document->reference . ', ';
                            } elseif ($ref_part == 'option3') {
                                $hint .= 'Ref: ' . $paragraph->document->reference . ', ';
                            }
                        } else {
                            $hint .= 'Ref: ' . $paragraph->document->reference . ', ';
                        }
                        $hint .= 'dated: ' . $date . '.';

                    }
                    $footnote->addText($hint, $GetStandardStylesFootNotes);
                    $listItemRun->addText(', ', $GetStandardStylesP);
                    $x++;
                } else {
                    $listItemRun->addText('Note: ', $GetStandardStylesP);
                }

                if ($paragraph->narrative == null) {
                    $listItemRun->addText('____________.');
                } else {
                    if (! $containsHtml) {
                        $listItemRun->addText($paragraph->narrative . '.');
                    } else {

                        $paragraph_ = $this->fixParagraphsWithImages($paragraph->narrative);

                        // preg_match('/<ol>.*?<\/ol>/s', $paragraph_, $olMatches);
                        // $olContent = $olMatches[0] ?? ''; // Get the <ol> content if it exists
                        // preg_match('/<ul>.*?<\/ul>/s', $paragraph_, $ulMatches);
                        // $ulContent = $ulMatches[0] ?? ''; // Get the <ol> content if it exists

                        // Step 2: Remove the <ol> content from the main paragraph
                        // $paragraphWithoutOl = preg_replace('/<ol>.*?<\/ol>/s', '', $paragraph);
                        // $paragraphWithoutOlUl = preg_replace('/<ul>.*?<\/ul>/s', '', $paragraphWithoutOl);
                        $paragraphWithoutImagesAndBreaks = preg_replace('/<(br)[^>]*>/i', '', $paragraph_);

                        // Step 2: Remove empty <p></p> tags
                        $paragraphWithoutEmptyParagraphs = preg_replace('/<p>\s*<\/p>/i', '', $paragraphWithoutImagesAndBreaks);

                        $paragraphsArray = $this->splitHtmlToArray($paragraphWithoutEmptyParagraphs);

                        // Step 3: Split into an array of <p> tags
                        // $paragraphsArray = preg_split('/(?=<p>)|(?<=<\/p>)/', $paragraphWithoutEmptyParagraphs);

                        // Step 4: Filter out empty elements
                        $paragraphsArray = array_filter($paragraphsArray, function ($item) {
                            return ! empty(trim($item));
                        });

                        // Step 5: Add each <p> tag to the document with a newline after it
                        foreach ($paragraphsArray as $index2 => $pTag) {
                            // dd($paragraphsArray);
                            if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath       = $matches[1];                                 // Extract image path
                                $altText       = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                                $fullImagePath = public_path($imgPath);                       // Convert relative path to absolute

                                if ($existedList) {
                                    if (file_exists($fullImagePath)) {
                                        $textRun = $section->addTextRun([
                                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation' => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                        ]);

                                        // Add Image
                                        $shape = $textRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        // Add Caption (Alt text)
                                        if (! empty($altText)) {
                                            $textRun->addTextBreak(); // New line
                                            $textRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                                'alignment'                               => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                                'size'                                    => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                                'bold'                                    => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                                'italic'                                  => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                                'underline'                               => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                        }

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $textRun->addTextBreak();
                                            }

                                        }
                                    }
                                } else {
                                    if (file_exists($fullImagePath)) {
                                        // Add Image
                                        $listItemRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        // Add Caption (Alt text)
                                        if (! empty($altText)) {
                                            $listItemRun->addTextBreak(); // New line
                                            $listItemRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                                'alignment'                                   => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                                'size'                                        => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                                'bold'                                        => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                                'italic'                                      => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                                'underline'                                   => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                        }

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }
                                }
                            } elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath = $matches[1]; // Extract image path

                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute

                                if ($existedList) {
                                    if (file_exists($fullImagePath)) {
                                        $textRun = $section->addTextRun([
                                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation' => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                        ]);

                                        // Add Image
                                        $textRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $textRun->addTextBreak();
                                            }

                                        }
                                    }
                                } else {
                                    if (file_exists($fullImagePath)) {
                                        // Add Image
                                        $listItemRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }

                                    }
                                }
                            } elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1' . $index . $index2 . '2',
                                    [
                                        'type'     => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels'   => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.'],

                                            // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                            // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                        ],
                                    ]
                                );
                                if (preg_match_all('/<li>(.*?)<\/li>/', $olMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                                          // Add a nested list item
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1' . $index . $index2 . '2', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                                          // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList = true;
                            } elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                // Add a nested list item
                                                                                                                                // dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';
                                        Html::addHtml($unNestedListItemRun, $item, false, false);
                                    }
                                }

                                $existedList = true;
                            } else {

                                // If the paragraph contains only text (including <span>, <strong>, etc.)
                                try {
                                    if ($existedList) {

                                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation'       => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                                        ]);
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun2->addTextBreak();
                                            }

                                        }
                                    } else {
                                        // $pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun, $pTag, false, false);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }

                                } catch (\Exception $e) {
                                    error_log('Error adding HTML: ' . $e->getMessage());
                                }
                            }

                        }

                    }
                }

            }
        }

        $paragraphs = FileAttachment::where('section', '2')->where('file_id', $file->id);
        if ($request->forclaimdocs) {
            $paragraphs->where('forClaim', '1');
        }

        $paragraphs = $paragraphs->orderBy('order', 'asc')->get();

        if (count($paragraphs) > 0) {
            $subtitle3 = $request->subtitle3;
            $subtitle3 = str_replace('&', '&amp;', $subtitle3);
            $section->addText($subtitle3, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
            foreach ($paragraphs as $index => $paragraph) {
                // dd($paragraphs);
                $listItemRun  = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                $existedList2 = false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;

                if ($paragraph->narrative == null) {
                    $listItemRun->addText('____________.');
                } else {
                    if (! $containsHtml) {
                        $listItemRun->addText($paragraph->narrative . '.');
                    } else {

                        $paragraph_ = $this->fixParagraphsWithImages($paragraph->narrative);

                        // preg_match('/<ol>.*?<\/ol>/s', $paragraph_, $olMatches);
                        // $olContent = $olMatches[0] ?? ''; // Get the <ol> content if it exists
                        // preg_match('/<ul>.*?<\/ul>/s', $paragraph_, $ulMatches);
                        // $ulContent = $ulMatches[0] ?? ''; // Get the <ol> content if it exists

                        // Step 2: Remove the <ol> content from the main paragraph
                        // $paragraphWithoutOl = preg_replace('/<ol>.*?<\/ol>/s', '', $paragraph);
                        // $paragraphWithoutOlUl = preg_replace('/<ul>.*?<\/ul>/s', '', $paragraphWithoutOl);
                        $paragraphWithoutImagesAndBreaks = preg_replace('/<(br)[^>]*>/i', '', $paragraph_);

                        // Step 2: Remove empty <p></p> tags
                        $paragraphWithoutEmptyParagraphs = preg_replace('/<p>\s*<\/p>/i', '', $paragraphWithoutImagesAndBreaks);

                        $paragraphsArray = $this->splitHtmlToArray($paragraphWithoutEmptyParagraphs);

                        // Step 3: Split into an array of <p> tags
                        // $paragraphsArray = preg_split('/(?=<p>)|(?<=<\/p>)/', $paragraphWithoutEmptyParagraphs);

                        // Step 4: Filter out empty elements
                        $paragraphsArray = array_filter($paragraphsArray, function ($item) {
                            return ! empty(trim($item));
                        });

                        // Step 5: Add each <p> tag to the document with a newline after it
                        foreach ($paragraphsArray as $index2 => $pTag) {
                            // dd($paragraphsArray);
                            if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath       = $matches[1];                                 // Extract image path
                                $altText       = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                                $fullImagePath = public_path($imgPath);                       // Convert relative path to absolute

                                if ($existedList2) {
                                    if (file_exists($fullImagePath)) {
                                        $textRun = $section->addTextRun([
                                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation' => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                        ]);

                                        // Add Image
                                        $shape = $textRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        // Add Caption (Alt text)
                                        if (! empty($altText)) {
                                            $textRun->addTextBreak(); // New line
                                            $textRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                                'alignment'                               => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                                'size'                                    => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                                'bold'                                    => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                                'italic'                                  => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                                'underline'                               => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                        }

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $textRun->addTextBreak();
                                            }

                                        }
                                    }
                                } else {
                                    if (file_exists($fullImagePath)) {
                                        // Add Image
                                        $listItemRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        // Add Caption (Alt text)
                                        if (! empty($altText)) {
                                            $listItemRun->addTextBreak(); // New line
                                            $listItemRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                                'alignment'                                   => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                                'size'                                        => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                                'bold'                                        => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                                'italic'                                      => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                                'underline'                                   => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                        }

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }
                                }
                            } elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath = $matches[1]; // Extract image path

                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute

                                if ($existedList2) {
                                    if (file_exists($fullImagePath)) {
                                        $textRun = $section->addTextRun([
                                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation' => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                        ]);

                                        // Add Image
                                        $textRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $textRun->addTextBreak();
                                            }

                                        }
                                    }
                                } else {
                                    if (file_exists($fullImagePath)) {
                                        // Add Image
                                        $listItemRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }

                                    }
                                }
                            } elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1' . $index . $index2 . '3',
                                    [
                                        'type'     => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels'   => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.'],

                                            // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                            // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                        ],
                                    ]
                                );
                                if (preg_match_all('/<li>(.*?)<\/li>/', $olMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                                          // Add a nested list item
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1' . $index . $index2 . '3', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                                          // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList2 = true;
                            } elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                // Add a nested list item
                                                                                                                                // dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';
                                        Html::addHtml($unNestedListItemRun, $item, false, false);
                                    }
                                }

                                $existedList2 = true;
                            } else {

                                // If the paragraph contains only text (including <span>, <strong>, etc.)
                                try {
                                    if ($existedList2) {

                                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation'       => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                                        ]);
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun2->addTextBreak();
                                            }

                                        }
                                    } else {
                                        // $pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun, $pTag, false, false);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }

                                } catch (\Exception $e) {
                                    error_log('Error adding HTML: ' . $e->getMessage());
                                }
                            }

                        }

                    }
                }

            }
        }

        $paragraphs = FileAttachment::where('section', '3')->where('file_id', $file->id);
        if ($request->forclaimdocs) {
            $paragraphs->where('forClaim', '1');
        }

        $paragraphs = $paragraphs->orderBy('order', 'asc')->get();

        if (count($paragraphs) > 0) {
            $subtitle4 = $request->subtitle4;
            $subtitle4 = str_replace('&', '&amp;', $subtitle4);
            $section->addText($subtitle4, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
            foreach ($paragraphs as $index => $paragraph) {
                // dd($paragraphs);
                $listItemRun  = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                $existedList3 = false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;

                if ($paragraph->narrative == null) {
                    $listItemRun->addText('____________.');
                } else {
                    if (! $containsHtml) {
                        $listItemRun->addText($paragraph->narrative . '.');
                    } else {

                        $paragraph_ = $this->fixParagraphsWithImages($paragraph->narrative);

                        // preg_match('/<ol>.*?<\/ol>/s', $paragraph_, $olMatches);
                        // $olContent = $olMatches[0] ?? ''; // Get the <ol> content if it exists
                        // preg_match('/<ul>.*?<\/ul>/s', $paragraph_, $ulMatches);
                        // $ulContent = $ulMatches[0] ?? ''; // Get the <ol> content if it exists

                        // Step 2: Remove the <ol> content from the main paragraph
                        // $paragraphWithoutOl = preg_replace('/<ol>.*?<\/ol>/s', '', $paragraph);
                        // $paragraphWithoutOlUl = preg_replace('/<ul>.*?<\/ul>/s', '', $paragraphWithoutOl);
                        $paragraphWithoutImagesAndBreaks = preg_replace('/<(br)[^>]*>/i', '', $paragraph_);

                        // Step 2: Remove empty <p></p> tags
                        $paragraphWithoutEmptyParagraphs = preg_replace('/<p>\s*<\/p>/i', '', $paragraphWithoutImagesAndBreaks);

                        $paragraphsArray = $this->splitHtmlToArray($paragraphWithoutEmptyParagraphs);

                        // Step 3: Split into an array of <p> tags
                        // $paragraphsArray = preg_split('/(?=<p>)|(?<=<\/p>)/', $paragraphWithoutEmptyParagraphs);

                        // Step 4: Filter out empty elements
                        $paragraphsArray = array_filter($paragraphsArray, function ($item) {
                            return ! empty(trim($item));
                        });

                        // Step 5: Add each <p> tag to the document with a newline after it
                        foreach ($paragraphsArray as $index2 => $pTag) {
                            // dd($paragraphsArray);
                            if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath       = $matches[1];                                 // Extract image path
                                $altText       = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                                $fullImagePath = public_path($imgPath);                       // Convert relative path to absolute

                                if ($existedList3) {
                                    if (file_exists($fullImagePath)) {
                                        $textRun = $section->addTextRun([
                                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation' => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                        ]);

                                        // Add Image
                                        $shape = $textRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        // Add Caption (Alt text)
                                        if (! empty($altText)) {
                                            $textRun->addTextBreak(); // New line
                                            $textRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                                'alignment'                               => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                                'size'                                    => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                                'bold'                                    => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                                'italic'                                  => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                                'underline'                               => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                        }

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $textRun->addTextBreak();
                                            }

                                        }
                                    }
                                } else {
                                    if (file_exists($fullImagePath)) {
                                        // Add Image
                                        $listItemRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        // Add Caption (Alt text)
                                        if (! empty($altText)) {
                                            $listItemRun->addTextBreak(); // New line
                                            $listItemRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                                'alignment'                                   => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                                'size'                                        => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                                'bold'                                        => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                                'italic'                                      => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                                'underline'                                   => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                        }

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }
                                }
                            } elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath = $matches[1]; // Extract image path

                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute

                                if ($existedList3) {
                                    if (file_exists($fullImagePath)) {
                                        $textRun = $section->addTextRun([
                                            'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation' => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                        ]);

                                        // Add Image
                                        $textRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $textRun->addTextBreak();
                                            }

                                        }
                                    }
                                } else {
                                    if (file_exists($fullImagePath)) {
                                        // Add Image
                                        $listItemRun->addImage($fullImagePath, [
                                            'width'     => 100,
                                            'height'    => 80,
                                            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                        ]);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }

                                    }
                                }
                            } elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1' . $index . $index2 . '4',
                                    [
                                        'type'     => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels'   => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.'],

                                            // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                            // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                        ],
                                    ]
                                );
                                if (preg_match_all('/<li>(.*?)<\/li>/', $olMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                                          // Add a nested list item
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1' . $index . $index2 . '4', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                                          // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList3 = true;
                            } elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                // Add a nested list item
                                                                                                                                // dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';
                                        Html::addHtml($unNestedListItemRun, $item, false, false);
                                    }
                                }

                                $existedList3 = true;
                            } else {

                                // If the paragraph contains only text (including <span>, <strong>, etc.)
                                try {
                                    if ($existedList3) {

                                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                            'indentation'       => [
                                                'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1436) : 1077,
                                            ],
                                            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                                        ]);
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun2->addTextBreak();
                                            }

                                        }
                                    } else {
                                        // $pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun, $pTag, false, false);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }

                                } catch (\Exception $e) {
                                    error_log('Error adding HTML: ' . $e->getMessage());
                                }
                            }

                        }

                    }
                }

            }
        }
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
        $path          = public_path($projectFolder);
        if (! file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $code      = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

        if (! file_exists($directory)) {
            mkdir($directory, 0755, true); // true = create nested directories
        }
        // Save document
        // Define file path in public folder
        $fileName = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $file->code . '_' . $header . '.docx';
        $filePath = public_path($fileName);

        // Save document to public folder
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);
        session(['zip_file' => $code]);

        return response()->json(['download_url' => asset($fileName)]);
        // Return file as a response and delete after download
        // return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function lowercaseFirstCharOnly($html)
    {
        return preg_replace_callback(
            '/(?:^|>)(T)/u', // Match only "T" after start or closing tag
            function ($matches) {
                return str_replace('T', 't', $matches[0]);
            },
            $html,
            1// Only first match
        );
    }

    public function splitHtmlToArray($html)
    {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true); // Prevent warnings from invalid HTML
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $resultArray = [];
        $xpath       = new \DOMXPath($dom);
        $elements    = $xpath->query('//p | //ul | //ol'); // Select only <p>, <ul>, and <ol> elements

        foreach ($elements as $element) {
            $resultArray[] = $dom->saveHTML($element); // Store each element as a separate string
        }

        return $resultArray;
    }

    public function fixParagraphsWithImages($html)
    {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath     = new \DOMXPath($dom);
        $pElements = $xpath->query('//p');

        foreach ($pElements as $p) {
            $newNodes        = [];
            $currentFragment = new \DOMDocument;
            $newP            = $dom->createElement('p'); // Use the original document to avoid Wrong Document Error

            foreach (iterator_to_array($p->childNodes) as $child) {
                if ($child->nodeName === 'img') {
                    // If the current <p> already has text, save it
                    if ($newP->hasChildNodes()) {
                        $newNodes[] = $newP;
                        $newP       = $dom->createElement('p');
                    }

                    // Create a new <p> for the image
                    $imgP        = $dom->createElement('p');
                    $importedImg = $dom->importNode($child, true); // Import the image to avoid Wrong Document Error
                    $imgP->appendChild($importedImg);
                    $newNodes[] = $imgP;

                    // Start a new <p> for the remaining content
                    $newP = $dom->createElement('p');
                } else {
                    $importedNode = $dom->importNode($child, true); // Import text nodes to avoid errors
                    $newP->appendChild($importedNode);
                }
            }

            // If there's leftover text, add it as a new <p>
            if ($newP->hasChildNodes()) {
                $newNodes[] = $newP;
            }

            // Replace original <p> with the new structured <p> elements
            $parent = $p->parentNode;
            foreach ($newNodes as $newNode) {
                $parent->insertBefore($newNode, $p);
            }
            $parent->removeChild($p);
        }

        // Clean up the output and return formatted HTML
        $cleanHtml = $dom->saveHTML();

        return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<html>', '</html>', '<body>', '</body>'], '', $cleanHtml));
    }

    public function copy_move_file(Request $request)
    {
        $file    = ProjectFile::where('slug', $request->file_id)->first();
        $counter = 1;
        $ex      = '';
        if ($request->action_type == 'Copy') {
            do {
                $name = $file->name . $ex;
                $ex   = ' (' . $counter . ')';
                $counter++;
            } while (ProjectFile::where('name', $name)->where('folder_id', $request->folder_id)->exists());
            do {
                $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            } while (ProjectFile::where('slug', $slug)->exists());
            $new_file = ProjectFile::create(['name' => $name, 'slug'                       => $slug, 'code'                    => $file->code,
                'user_id'                               => $file->user_id, 'project_id'        => $file->project_id,
                'against_id'                            => $file->against_id, 'start_date'     => $file->start_date,
                'end_date'                              => $file->end_date, 'folder_id'        => $request->folder_id,
                'notes'                                 => $file->notes, 'time'                => $file->time, 'prolongation_cost' => $file->prolongation_cost,
                'disruption_cost'                       => $file->disruption_cost, 'variation' => $file->variation,
                'closed'                                => $file->closed, 'assess_not_pursue'  => $file->assess_not_pursue]);
            $file_attachment = FileAttachment::where('file_id', $file->id)->get();
            foreach ($file_attachment as $attachment) {
                FileAttachment::create(['file_id' => $new_file->id, 'user_id' => auth()->user()->id,
                    'order'                           => $attachment->order,
                    'narrative'                       => $attachment->narrative,
                    'forClaim'                        => $attachment->forClaim,
                    'section'                         => $attachment->section]);
            }
            $file_documents = FileDocument::where('file_id', $file->id)->get();
            foreach ($file_documents as $doc) {
                $new_doc = FileDocument::create(['file_id' => $new_file->id, 'user_id' => auth()->user()->id,
                    'document_id'                              => $doc->document_id,
                    'note_id'                                  => $doc->note_id,
                    'sn'                                       => $doc->sn, 'forClaim'     => $doc->forClaim, 'narrative' => $doc->narrative, 'notes1' => $doc->notes1,
                    'forChart'                                 => $doc->forChart, 'notes2' => $doc->notes2,
                    'forLetter'                                => $doc->forLetter]);
                $ids = $doc->tags->pluck('id')->toArray();
                if (count($ids) > 0) {
                    $new_doc->tags()->sync($ids); // Sync tags
                }
                $doc_gantt_chart = GanttChartDocData::where('file_document_id', $doc->id)->first();
                if ($doc_gantt_chart) {
                    GanttChartDocData::create(['file_document_id' => $new_doc->id, 'show_cur'                                => $doc_gantt_chart->show_cur,
                        'cur_type'                                    => $doc_gantt_chart->cur_type, 'cur_sections'              => $doc_gantt_chart->cur_sections,
                        'cur_left_caption'                            => $doc_gantt_chart->cur_left_caption, 'cur_right_caption' => $doc_gantt_chart->cur_right_caption, 'cur_show_sd'    => $doc_gantt_chart->cur_show_sd, 'cur_show_fd'     => $doc_gantt_chart->cur_show_fd,
                        'cur_show_ref'                                => $doc_gantt_chart->cur_show_ref, 'show_pl'               => $doc_gantt_chart->show_pl, 'pl_type'                  => $doc_gantt_chart->pl_type, 'pl_sd'               => $doc_gantt_chart->pl_sd, 'pl_fd' => $doc_gantt_chart->pl_fd,
                        'pl_color'                                    => $doc_gantt_chart->pl_color, 'pl_left_caption'           => $doc_gantt_chart->pl_left_caption, 'pl_right_caption' => $doc_gantt_chart->pl_right_caption, 'pl_show_sd' => $doc_gantt_chart->pl_show_sd,
                        'pl_show_fd'                                  => $doc_gantt_chart->pl_show_fd, 'show_lp'                 => $doc_gantt_chart->show_lp, 'lp_sd'                    => $doc_gantt_chart->lp_sd, 'lp_fd'                 => $doc_gantt_chart->lp_fd]);
                }

            }

            return response()->json([
                'status'  => 'success',
                'message' => 'File Copied To Selected Folder Successfully.',

                // 'redirect' => url('/project/file/' . $file_doc->file->slug . '/documents')
            ]);
        } elseif ($request->action_type == 'Move') {
            do {
                $name = $file->name . $ex;
                $ex   = ' (' . $counter . ')';
                $counter++;
            } while (ProjectFile::where('name', $name)->where('folder_id', $request->folder_id)->exists());
            $file->name      = $name;
            $file->folder_id = $request->folder_id;
            $file->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'File Moved To Selected Folder Successfully.',

                // 'redirect' => url('/project/file/' . $file_doc->file->slug . '/documents')
            ]);
        }
    }

    public function changeFlag(Request $request)
    {
        $request->validate([
            'file_slug' => 'required|exists:project_files,slug',
            'flag'      => 'required|in:blue_flag,red_flag,green_flag',
        ]);

        $file = ProjectFile::where('slug', $request->file_slug)->first();

        // قلب القيمة 0 ↔ 1
        $file->{$request->flag} = ! $file->{$request->flag};
        $file->save();

        return response()->json([
            'status' => 'success',
            'flag'   => $request->flag,
            'value'  => $file->{$request->flag},
        ]);
    }

    public function change_for_tOrDOrV(Request $request)
    {
        if (count($request->file_ids) > 1) {
            $do = ProjectFile::where('slug', $request->file_ids[0])->first();
            $ac = $request->action_type;
            if ($do->$ac == '1') {
                $va = '0';
            } else {
                $va = '1';
            }
            ProjectFile::whereIn('slug', $request->file_ids)->update([$ac => $va]);

            return response()->json([
                'status' => 'success',
                'value'  => $va,
            ]);
        } else {
            ProjectFile::whereIn('slug', $request->file_ids)->update([$request->action_type => $request->val]);

            return response()->json([
                'status' => 'success',
            ]);
        }
    }
}
