<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\FileDocument;
use App\Models\Note;
use App\Models\Project;
use App\Models\ProjectFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class NoteController extends ApiController
{
    public function edit_note($id)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/'.auth()->user()->current_project_id.'/temp/'.$zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $note = Note::where('slug', $id)->first();
        $folders = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');

        return view('project_dashboard.notes.edit', compact('users', 'note', 'folders'));

    }

    public function update_note(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'start_date' => 'required', // 10MB max
        ]);
        Note::where('slug', $id)->update([
            'user_id' => $request->user_id,
            'subject' => $request->subject,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'note' => $request->notes,
        ]);
        $note = Note::where('slug', $id)->first();

        if ($request->file_id) {
            $fileDoc = FileDocument::where('file_id', $request->file_id)->where('note_id', $note->id)->first();
            if (! $fileDoc) {
                FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $request->file_id, 'note_id' => $note->id]);
            }
        }

        if (session()->has('current_view') && session('current_view') == 'file') {
            if ($request->action == 'save') {
                return redirect('/project/note/edit/'.$note->slug)->with('success', 'Note Updated successfully.');
            } else {
                $current_file = session('current_file2');

                session()->forget('current_file2');
                session()->forget('current_view');

                return redirect(route('project.file-documents.index', $current_file))->with('success', 'Note Updated successfully.');
            }
        } elseif (session()->has('current_view') && session('current_view') == 'file_doc') {
            if ($request->action == 'save') {
                return redirect('/project/note/edit/'.$note->slug)->with('success', 'Note Updated successfully.');
            } else {
                $current_file_doc = session('current_file_doc');
                session()->forget('current_file_doc');
                session()->forget('current_view');

                return redirect('/project/file-document-first-analyses/'.$current_file_doc)->with('success', 'Note Updated successfully.');
            }
        } else {
            if ($request->action == 'save') {
                return redirect('/project/note/edit/'.$note->slug)->with('success', 'Note Updated successfully.');
            } else {
                return redirect('/project/all-notes')->with('success', 'Note Updated successfully.');
            }
        }

    }
}
