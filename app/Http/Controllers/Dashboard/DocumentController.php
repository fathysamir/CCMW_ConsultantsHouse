<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\DocType;
use App\Models\Document;
use App\Models\FileDocument;
use App\Models\GanttChartDocData;
use App\Models\Note;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectFolder;
use App\Models\StorageFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\Fpdi;

class DocumentController extends ApiController
{
    public function create_single_doc_view()
    {
        session()->forget('path');

        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users   = $project->assign_users;

        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->orderBy('order', 'asc')->get();
        $stake_holders   = $project->stakeHolders;
        $threads         = Document::where('project_id', auth()->user()->current_project_id)->pluck('reference');
        $folders         = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');

        return view('project_dashboard.upload_documents.upload_single_doc', compact('documents_types', 'users', 'stake_holders', 'threads', 'folders'));
    }

    public function store_single_doc(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'start_date' => 'required',
            'doc_id'     => 'required', // 10MB max
        ]);
        do {
            $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (Document::where('slug', $invitation_code)->exists());

        $doc = Document::create([
            'slug'            => $invitation_code,
            'doc_type_id'     => $request->doc_type,
            'user_id'         => $request->user_id,
            'project_id'      => auth()->user()->current_project_id,
            'subject'         => $request->subject,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'from_id'         => intval($request->from_id),
            'to_id'           => intval($request->to_id),
            'reference'       => $request->reference,
            'revision'        => $request->revision,
            'status'          => $request->status,
            'notes'           => $request->notes,
            'storage_file_id' => intval($request->doc_id),
            'threads'         => $request->threads && count($request->threads) > 0 ? json_encode($request->threads) : null,

        ]);

        if ($request->analyzed) {
            $doc->analyzed = '1';
        }
        if ($request->analysis_complete) {
            $doc->analysis_complete = '1';
        }
        $doc->save();
        if ($request->file_id) {
            FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $request->file_id, 'document_id' => $doc->id]);
        }

        return redirect('/project/all-documents')->with('success', 'Document Created successfully.');

    }

    public function upload_single_doc(Request $request)
    {

        $request->validate([
            'file' => 'required|file|max:512000', // 10MB max
        ]);

        $file            = $request->file('file');
        $name            = $file->getClientOriginalName();
        $size            = $file->getSize();
        $type            = $file->getMimeType();
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->orderBy('order', 'asc')->pluck('description')->toArray();
        $project         = Project::findOrFail(auth()->user()->current_project_id);
        $stake_holders   = $project->stakeHolders;
        $storageFile     = StorageFile::where('user_id', auth()->user()->id)->where('project_id', auth()->user()->current_project_id)->where('file_name', $name)->where('size', $size)->where('file_type', $type)->first();

        if (! $storageFile) {
            $nameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanedName          = preg_replace('/[^a-zA-Z0-9]/', '-', $nameWithoutExtension);
            $fileName             = time() . '_' . $cleanedName . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

            // Create project-specific folder in public path
            $projectFolder = 'projects/' . auth()->user()->current_project_id . '/documents';
            $path          = public_path($projectFolder);
            if (! file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Move file to public folder
            $file->move($path, $fileName);

            // Save file info to database
            $storageFile = StorageFile::create([
                'user_id'    => auth()->user()->id,
                'project_id' => auth()->user()->current_project_id,
                'file_name'  => $name,
                'size'       => $size,
                'file_type'  => $type,
                'path'       => $projectFolder . '/' . $fileName,
            ]);
            session(['path' => $projectFolder . '/' . $fileName]);
        } else {
            session(['path' => $storageFile->path]);
        }
        if ($request->use_ai == '1') {
            $path2 = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf');
            if (file_exists($path2)) {
                unlink($path2);
            }
            $sourcePath    = public_path($storageFile->path);
            $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
            $path          = public_path($projectFolder);
            if (! file_exists($path)) {

                mkdir($path, 0755, true);
            }
            $imagick = new \Imagick();
            $imagick->setResolution(300, 300); // زيادة الدقة
            $imagick->readImage($sourcePath . '[0]');
            $directoryeee = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id);

            if (! file_exists($directoryeee)) {
                mkdir($directoryeee, 0755, true); // true = create nested directories
            }
            $imagick->setImageFormat('pdf');
            $imagick->setImageCompressionQuality(100);
            $imagick->writeImages(public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf'), true);
            $sourcePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf');
            $code       = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            $directory  = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

            if (! file_exists($directory)) {
                mkdir($directory, 0755, true); // true = create nested directories
            }
            $targetPath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/extracted.pdf');
            $pdf        = new Fpdi;
            $pageCount  = $pdf->setSourceFile($sourcePath);

            $templateId = $pdf->importPage(1);
            $size       = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            $pdf->Output('F', $targetPath);
            $path2 = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf');

            if (file_exists($path2)) {
                unlink($path2);
            }
            $apiKey = 'sec_rKlDJdNkUf5wBSQmAqPOlzdmssUuUWJW';
            $url    = url('projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/extracted.pdf');
            //dd($url);
            $payload = json_encode([
                'url' => $url,
            ]);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => 'https://api.chatpdf.com/v1/sources/add-url',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => $payload,
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                // handle error
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("cURL Error: $error");
            }

            curl_close($ch);

            $data = json_decode($response, true);

            // Access sourceId from response
            $sourceId = $data['sourceId'] ?? null;
            $message  = 'Provided that we have the following list of document types: \n ';
            foreach ($documents_types as $des) {
                $message .= '■ ' . $des . '\n';
            }
            $message .= 'Please select from this list the document type for that PDF or answer with “No Match” if the type not exist in this list. \n Please limit your answer to the needed information without additional words and put result in key Document_type (Document_type:.....).';
            $message .= 'and and and';
            $message .= 'Provided that we have the following list of stakeholders: \n';
            foreach ($stake_holders as $stake_holder)
                $message .= '■ ' . $stake_holder->name . '\n';
            }
            $message .= 'Please select from this list the document sender for that PDF or answer with “No Match” if the type not exist in this list. \n Please limit your answer to the needed information without additional words and put result in key Document_sender (Document_sender:.....).';
            $payload = json_encode([
                'sourceId' => $sourceId,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => $message,
                    ],
                ],
            ]);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => 'https://api.chatpdf.com/v1/chats/message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => $payload,
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("cURL Error: $error");
            }

            curl_close($ch);

            $data = json_decode($response, true);

            // Get the response content
            $answer = $data['content'] ?? 'No answer found';
            if ($code != null) {
                $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);
                if (File::exists($filePath)) {
                    File::deleteDirectory($filePath);
                }
            }
            dd($answer);
        } else {
            $type_id = '';
        }
        return response()->json([
            'success' => true,
            'file'    => $storageFile,
            'type_id' => $type_id,
        ]);
    }

    public function all_documents(Request $request)
    {
        session()->forget('path');
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }

        session()->forget('current_file_doc');
        session()->forget('current_view');
        $project         = Project::findOrFail(auth()->user()->current_project_id);
        $users           = $project->assign_users;
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->orderBy('order', 'asc')->get();
        $all_documents   = Document::where('project_id', auth()->user()->current_project_id);
        if ($request->threads) {
            $threadValues = array_filter(array_map('trim', explode(',', $request->threads)));

            $all_documents->where(function ($query) use ($threadValues) {
                foreach ($threadValues as $thread) {

                    $query->orWhere('threads', 'like', '%' . $thread . '%');
                }
            });
        }
        if ($request->slug) {
            $all_documents->where('slug', $request->slug);
        }
        if ($request->doc_type) {
            $all_documents->where('doc_type_id', $request->doc_type);
        }
        $all_documents = $all_documents->orderBy('start_date', 'asc')->orderBy('reference', 'asc')->get();
        // dd($all_documents);
        $stake_holders = $project->stakeHolders;
        $folders       = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');

        return view('project_dashboard.documents', compact('all_documents', 'users', 'stake_holders', 'documents_types', 'folders'));

    }

    public function edit_document($id)
    {
        session()->forget('path');

        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $project         = Project::findOrFail(auth()->user()->current_project_id);
        $users           = $project->assign_users;
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->orderBy('order', 'asc')->get();
        $stake_holders   = $project->stakeHolders;
        $document        = Document::where('slug', $id)->first();
        $threads         = Document::where('project_id', auth()->user()->current_project_id)->where('id', '!=', $document->id)->pluck('reference');
        $folders         = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');
        session(['path' => $document->storageFile->path]);

        return view('project_dashboard.upload_documents.edit_document', compact('documents_types', 'users', 'stake_holders', 'document', 'threads', 'folders'));
    }

    public function update_document(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'start_date' => 'required', // 10MB max
        ]);
        Document::where('slug', $id)->update([

            'doc_type_id'     => $request->doc_type,
            'user_id'         => $request->user_id,
            'subject'         => $request->subject,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'from_id'         => intval($request->from_id),
            'to_id'           => intval($request->to_id),
            'reference'       => $request->reference,
            'revision'        => $request->revision,
            'status'          => $request->status,
            'notes'           => $request->notes,
            'storage_file_id' => intval($request->doc_id),
            'threads'         => $request->threads && count($request->threads) > 0 ? json_encode($request->threads) : null,

        ]);
        $doc = Document::where('slug', $id)->first();
        if ($request->analyzed) {
            $doc->analyzed = '1';
        } else {
            $doc->analyzed = '0';
        }
        if ($request->analysis_complete) {
            $doc->analysis_complete = '1';
        } else {
            $doc->analysis_complete = '0';
        }
        $doc->save();
        if ($request->file_id) {
            $fileDoc = FileDocument::where('file_id', $request->file_id)->where('document_id', $doc->id)->first();
            if (! $fileDoc) {
                $fileDoc     = FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $request->file_id, 'document_id' => $doc->id]);
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
        }

        if (session()->has('current_view') && session('current_view') == 'file') {
            if ($request->action == 'save') {
                return redirect('/project/document/edit/' . $doc->slug)->with('success', 'Document Updated successfully.');
            } else {
                $current_file = session('current_file2');

                session()->forget('current_file2');
                session()->forget('current_view');

                return redirect(route('project.file-documents.index', $current_file))->with('success', 'Document Updated successfully.');
            }
        } elseif (session()->has('current_view') && session('current_view') == 'file_doc') {
            if ($request->action == 'save') {
                return redirect('/project/document/edit/' . $doc->slug)->with('success', 'Document Updated successfully.');
            } else {
                $current_file_doc = session('current_file_doc');
                session()->forget('current_file_doc');
                session()->forget('current_view');

                return redirect('/project/file-document-first-analyses/' . $current_file_doc)->with('success', 'Document Updated successfully.');
            }
        } else {
            if ($request->action == 'save') {
                return redirect('/project/document/edit/' . $doc->slug)->with('success', 'Document Updated successfully.');
            } else {
                return redirect('/project/all-documents')->with('success', 'Document Updated successfully.');
            }
        }

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
            'file_id'     => 'required|exists:project_files,id',
        ]);
        $fileDoc = FileDocument::where('file_id', $request->file_id)->where('document_id', $request->document_id)->first();
        if (! $fileDoc) {
            $fileDoc     = FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $request->file_id, 'document_id' => $request->document_id]);
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

            return response()->json(['message' => 'Document assigned successfully']);

        } else {
            return response()->json(['message' => 'This Document Is Existed In Selected File']);

        }

    }

    public function assignToFileForAll(Request $request)
    {
        $request->validate([
            'document_ids'   => 'required|array',
            'document_ids.*' => 'exists:documents,id', // Validate each document ID
            'file_id'        => 'required|exists:project_files,id',
        ]);
        $userId       = auth()->user()->id; // Get logged-in user ID
        $assignedDocs = [];                 // To track successfully assigned documents
        $skippedDocs  = [];                 // To track already existing documents

        foreach ($request->document_ids as $documentId) {
            $sections = [];
            $fileDoc  = FileDocument::where('file_id', $request->file_id)
                ->where('document_id', $documentId)
                ->first();

            if (! $fileDoc) {
                $fileDoc = FileDocument::create([
                    'user_id'     => $userId,
                    'file_id'     => $request->file_id,
                    'document_id' => $documentId,
                ]);
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
                $assignedDocs[] = $documentId; // Track assigned document
            } else {
                $skippedDocs[] = $documentId; // Track already existing document
            }
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Batch assignment completed.',
            'assigned_count' => count($assignedDocs),
            'skipped_count'  => count($skippedDocs),
            'skipped_docs'   => $skippedDocs, // List of documents that were already assigned
        ]);
    }

    public function changeOwner(Request $request)
    {
        $request->validate([
            'document_id'  => 'required|exists:documents,id',
            'new_owner_id' => 'required|exists:users,id',
        ]);

        $document          = Document::find($request->document_id);
        $document->user_id = $request->new_owner_id;
        $document->save();

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $file_doc_IDs = FileDocument::where('document_id', $id)->pluck('id');
        GanttChartDocData::whereIn('id', $file_doc_IDs)->delete();
        FileDocument::where('document_id', $id)->delete();
        $doc  = Document::where('id', $id)->first();
        $docs = Document::where('storage_file_id', $doc->storage_file_id)->where('id', '!=', $id)->get();
        if (count($docs) == 0) {

            $path = public_path($doc->storageFile->path);

            if (file_exists($path)) {
                unlink($path);
            }
            StorageFile::where('id', $doc->storage_file_id)->delete();
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
            $doc          = Document::where('id', $id)->first();
            $file_doc_IDs = FileDocument::where('document_id', $id)->pluck('id');
            GanttChartDocData::whereIn('id', $file_doc_IDs)->delete();
            FileDocument::where('document_id', $id)->delete();
            $docs = Document::where('storage_file_id', $doc->storage_file_id)->where('id', '!=', $id)->get();
            if (count($docs) == 0) {
                $path = public_path($doc->storageFile->path);

                if (file_exists($path)) {
                    unlink($path);
                }
                StorageFile::where('id', $doc->storage_file_id)->delete();

            }
            $doc->delete();
        }

        return response()->json(['success' => true]);
    }

    public function changeOwnerForAll(Request $request)
    {
        // Update the owner for all selected documents

        foreach ($request->document_ids as $id) {
            $doc = Document::findOrFail($id);

            if ($request->doc_type) {
                $doc->doc_type_id = $request->doc_type;
            }
            if ($request->from) {
                $doc->from_id = $request->from;
            }
            if ($request->to) {
                $doc->to_id = $request->to;
            }
            if ($request->owner) {
                $doc->user_id = $request->owner;
            }
            $doc->save();
        }

        return response()->json(['success' => true]);
    }

    public function changeStakeHoldersForAll(Request $request)
    {

        $request->validate([
            'document_ids'         => 'required|array',
            'newFromStakeHolderId' => 'nullable|exists:stake_holders,id',
            'newToStakeHolderId'   => 'nullable|exists:stake_holders,id',

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
            'doc_type_id'  => 'required|exists:doc_types,id',
        ]);

        // Update the owner for all selected documents
        Document::whereIn('id', $request->document_ids)
            ->update(['doc_type_id' => $request->doc_type_id]);

        return response()->json(['success' => true]);
    }

    public function downloadDocument($id)
    {
        $document = FileDocument::findOrFail($id);
        $filePath = public_path($document->document->storageFile->path);

        if (str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $document->document->docType->name)), 'email') || str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $document->document->docType->description)), 'email')) {
            $sanitizedFilename = $document->document->fromStakeHolder->narrative . "'s e-mail dated ";
            // $date = date('y_m_d', strtotime($document->document->start_date));
            $date2    = date('d-M-y', strtotime($document->document->start_date));
            $fileName = $sanitizedFilename . $date2 . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
        } else {
            $sanitizedFilename = preg_replace('/[\\\\\/:*?"+.<>|{}\[\]`]/', '-', $document->document->reference);
            $sanitizedFilename = trim($sanitizedFilename, '-');
            // $date = date('y_m_d', strtotime($document->document->start_date));
            $fileName = $sanitizedFilename . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
        }

        if (file_exists($filePath)) {
            return response()->download($filePath, $fileName, [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ]);
        }

        return redirect()->back()->with('error', 'File not found.');
    }

    public function get_assigned_files($id)
    {
        $file_doc_type = session('file_doc_type');
        if ($file_doc_type == 'document') {
            $doc      = Document::where('slug', $id)->first();
            $file_ids = FileDocument::where('document_id', $doc->id)->pluck('file_id')->toArray();
        } elseif ($file_doc_type == 'note') {
            $doc      = Note::where('slug', $id)->first();
            $file_ids = FileDocument::where('note_id', $doc->id)->pluck('file_id')->toArray();
        }
        session()->forget('file_doc_type');
        $files = ProjectFile::whereIn('id', $file_ids)->with('folder')->get();

        return response()->json(['files' => $files]);

    }

    public function getDocsByReference(Request $request)
    {

        $reference = $request->reference;

        $documents = Document::where('project_id', auth()->user()->current_project_id)->where('reference', 'like', '%' . $reference . '%')->with('storageFile')->get();

        return response()->json(['documents' => $documents]);
    }

    public function assignDocumentbyslug(Request $request)
    {

        $doc     = Document::where('slug', $request->slug)->first();
        $fileDoc = FileDocument::where('file_id', $request->file_id)->where('document_id', $doc->id)->first();
        if (! $fileDoc) {
            $fileDoc     = FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $request->file_id, 'document_id' => $doc->id]);
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
            return response()->json(['message' => 'Document assigned successfully']);

        } else {
            return response()->json(['message' => 'This Document Is Existed In Selected File']);

        }

    }

    public function ocr_layer($id)
    {
        $doc = Document::where('slug', $id)->first();
        if ($doc) {
            $path = $doc->storageFile->path;
        } else {
            $path = null;
        }

        return view('project_dashboard.upload_documents.ocr_layer', compact('path'));
    }

    public function ocr_with_path()
    {
        $path = session('path');

        return view('project_dashboard.upload_documents.ocr_layer', compact('path'));
    }
}
