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
use App\Models\DocType;
use App\Models\ProjectFolder;
use App\Models\StorageFile;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\FileDocument;
use Illuminate\Validation\Rule;

class DocumentController extends ApiController
{
    public function create_single_doc_view()
    {
        $users = User::all();
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->get();
        $stake_holders = $project->stakeHolders;
        $threads = Document::where('project_id', auth()->user()->current_project_id)->pluck('reference');

        return view('project_dashboard.upload_documents.upload_single_doc', compact('documents_types', 'users', 'stake_holders', 'threads'));
    }

    public function store_single_doc(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'start_date' => 'required' // 10MB max
        ]);
        do {
            $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (Document::where('slug', $invitation_code)->exists());

        $doc = Document::create([
            'slug' => $invitation_code,
            'doc_type_id' => $request->doc_type,
            'user_id' => $request->user_id,
            'project_id' => auth()->user()->current_project_id,
            'subject' => $request->subject,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'from_id' => intval($request->from_id),
            'to_id' => intval($request->to_id),
            'reference' => $request->reference,
            'revision' => $request->revision,
            'status' => $request->status,
            'notes' => $request->notes,
            'storage_file_id' => intval($request->doc_id),
            'threads' => $request->threads && count($request->threads) > 0 ? json_encode($request->threads) : null

        ]);

        if ($request->analyzed) {
            $doc->analyzed = '1';
        }
        $doc->save();
        return redirect('/project/all-documents')->with('success', 'Document Created successfully.');

    }

    public function upload_single_doc(Request $request)
    {

        $request->validate([
            'file' => 'required|file|max:51200' // 10MB max
        ]);

        $file = $request->file('file');
        $name = $file->getClientOriginalName();
        $size = $file->getSize();
        $type = $file->getMimeType();

        $storageFile = StorageFile::where('user_id', auth()->user()->id)->where('project_id', auth()->user()->current_project_id)->where('file_name', $name)->where('size', $size)->where('file_type', $type)->first();
        if ($storageFile) {
            return response()->json([
                'success' => true,
                'file' => $storageFile
            ]);
        }
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Create project-specific folder in public path
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/documents';
        $path = public_path($projectFolder);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Move file to public folder
        $file->move($path, $fileName);

        // Save file info to database
        $storageFile = StorageFile::create([
            'user_id' => auth()->user()->id,
            'project_id' => auth()->user()->current_project_id,
            'file_name' => $name,
            'size' => $size,
            'file_type' => $type,
            'path' => $projectFolder . '/' . $fileName
        ]);

        return response()->json([
            'success' => true,
            'file' => $storageFile
        ]);
    }

    public function all_documents()
    {
        $users = User::all();
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->get();
        $all_documents = Document::where('project_id', auth()->user()->current_project_id)->orderBy('start_date', 'asc')->orderBy('reference', 'asc')->get();
        $stake_holders = $project->stakeHolders;
        $folders = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive','Recycle Bin'])->pluck('name', 'id');
        return view('project_dashboard.documents', compact('all_documents', 'users', 'stake_holders', 'documents_types', 'folders'));

    }

    public function edit_document($id)
    {
        $users = User::all();
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->get();
        $stake_holders = $project->stakeHolders;
        $document = Document::where('slug', $id)->first();
        $threads = Document::where('project_id', auth()->user()->current_project_id)->where('id', '!=', $document->id)->pluck('reference');
        return view('project_dashboard.upload_documents.edit_document', compact('documents_types', 'users', 'stake_holders', 'document', 'threads'));
    }

    public function update_document(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'start_date' => 'required' // 10MB max
        ]);
        Document::where('slug', $id)->update([

            'doc_type_id' => $request->doc_type,
            'user_id' => $request->user_id,
            'subject' => $request->subject,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'from_id' => intval($request->from_id),
            'to_id' => intval($request->to_id),
            'reference' => $request->reference,
            'revision' => $request->revision,
            'status' => $request->status,
            'notes' => $request->notes,
            'storage_file_id' => intval($request->doc_id),
            'threads' => $request->threads && count($request->threads) > 0 ? json_encode($request->threads) : null

        ]);
        $doc = Document::where('slug', $id)->first();
        if ($request->analyzed) {
            $doc->analyzed = '1';
        } else {
            $doc->analyzed = '0';
        }
        $doc->save();
        return redirect('/project/all-documents')->with('success', 'Document Updated successfully.');
    }
    public function getFolderFiles($folderId)
    {
        $files = ProjectFile::where('folder_id', $folderId)->get(['id', 'name']); // Fetch files
        return response()->json(['files' => $files]);
    }

    public function assignDocument(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'file_id' => 'required|exists:project_files,id',
        ]);
        $fileDoc = FileDocument::where('file_id', $request->file_id)->where('document_id', $request->document_id)->first();
        if (!$fileDoc) {
            FileDocument::create(['user_id' => auth()->user()->id,'file_id' => $request->file_id,'document_id' => $request->document_id]);
            return response()->json(['message' => 'Document assigned successfully']);

        } else {
            return response()->json(['message' => 'This Document Is Existed In Selected File']);

        }

    }

    public function assignToFileForAll(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id', // Validate each document ID
            'file_id' => 'required|exists:project_files,id',
        ]);
        $userId = auth()->user()->id; // Get logged-in user ID
        $assignedDocs = []; // To track successfully assigned documents
        $skippedDocs = []; // To track already existing documents

        foreach ($request->document_ids as $documentId) {
            $fileDoc = FileDocument::where('file_id', $request->file_id)
                ->where('document_id', $documentId)
                ->first();

            if (!$fileDoc) {
                FileDocument::create([
                    'user_id' => $userId,
                    'file_id' => $request->file_id,
                    'document_id' => $documentId,
                ]);
                $assignedDocs[] = $documentId; // Track assigned document
            } else {
                $skippedDocs[] = $documentId; // Track already existing document
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Batch assignment completed.',
            'assigned_count' => count($assignedDocs),
            'skipped_count' => count($skippedDocs),
            'skipped_docs' => $skippedDocs, // List of documents that were already assigned
        ]);
    }
    public function changeOwner(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'new_owner_id' => 'required|exists:users,id',
        ]);

        $document = Document::find($request->document_id);
        $document->user_id = $request->new_owner_id;
        $document->save();

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $doc = Document::where('id', $id)->first();
        $docs = Document::where('storage_file_id', $doc->storage_file_id)->where('id', '!=', $id)->get();
        if (count($docs) == 0) {
            $path = public_path('projects/1/documents/1740495513_iSchool 2025 - Company Profile.pdf');

            if (file_exists($path)) {
                unlink($path);
            }
        }
        $doc->delete();
        return redirect('/project/all-documents')->with('success', 'Document Deleted successfully.');

    }

    public function deleteSelectedDocs(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
        ]);

        // Update the owner for all selected documents
        foreach ($request->document_ids as $id) {
            $doc = Document::where('id', $id)->first();
            $docs = Document::where('storage_file_id', $doc->storage_file_id)->where('id', '!=', $id)->get();
            if (count($docs) == 0) {
                $path = public_path('projects/1/documents/1740495513_iSchool 2025 - Company Profile.pdf');

                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $doc->delete();
        }

        return response()->json(['success' => true]);
    }

    public function changeOwnerForAll(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'new_owner_id' => 'required|exists:users,id',
        ]);

        // Update the owner for all selected documents
        Document::whereIn('id', $request->document_ids)
                ->update(['user_id' => $request->new_owner_id]);

        return response()->json(['success' => true]);
    }

    public function changeStakeHoldersForAll(Request $request)
    {

        $request->validate([
            'document_ids' => 'required|array',
            'newFromStakeHolderId' => 'nullable|exists:stake_holders,id',
            'newToStakeHolderId' => 'nullable|exists:stake_holders,id',

        ]);
        if ($request->newFromStakeHolderId) {
            Document::whereIn('id', $request->document_ids)
                ->update(['from_id' => $request->newFromStakeHolderId]);
        }
        if ($request->newToStakeHolderId) {
            Document::whereIn('id', $request->document_ids)
                ->update(['to_id' => $request->newToStakeHolderId]);
        }


        return response()->json(['success' => true]);
    }

    public function changeDocTypeForAll(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'doc_type_id' => 'required|exists:doc_types,id',
        ]);

        // Update the owner for all selected documents
        Document::whereIn('id', $request->document_ids)
                ->update(['doc_type_id' => $request->doc_type_id]);

        return response()->json(['success' => true]);
    }

}
