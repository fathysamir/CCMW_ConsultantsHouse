<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\DocType;
use App\Models\Document;
use App\Models\FileDocument;
use App\Models\GanttChartDocData;
use App\Models\Project;
use App\Models\ProjectFolder;
use App\Models\StorageFile;
use App\Models\TestDocument;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\Fpdi;

class UploadGroupDocumentController extends ApiController
{
    public function index()
    {
        session()->forget('path');
        session()->forget('testDocumentsIDs');
        $folders         = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');
        $project         = Project::findOrFail(auth()->user()->current_project_id);
        $users           = $project->assign_users;
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->orderBy('order', 'asc')->get();
        $stake_holders   = $project->stakeHolders;

        return view('project_dashboard.upload_group_documents.index', compact('folders', 'users', 'project', 'documents_types', 'stake_holders'));
    }

    public function upload_multi_files(Request $request)
    {
        $uploadedFiles = [];
        ini_set('upload_max_filesize', '250M');
        ini_set('post_max_size', '250M');
        ini_set('max_file_uploads', '100');
        foreach ($request->file('files') as $file) {
            $name = $file->getClientOriginalName();
            $size = $file->getSize();
            $type = $file->getMimeType();

            $storageFile = StorageFile::where('user_id', auth()->user()->id)->where('project_id', auth()->user()->current_project_id)->where('file_name', $name)->where('size', $size)->where('file_type', $type)->first();
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
            }
            $nameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);

            $uploadedFiles[$nameWithoutExtension]['storageFile_id'] = $storageFile->id;
            if ($request->use_ai == '1') {
                $path2 = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf');
                if (file_exists($path2)) {
                    unlink($path2);
                }
                $sourcePath = public_path($storageFile->path);

                $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
                $path          = public_path($projectFolder);
                if (! file_exists($path)) {

                    mkdir($path, 0755, true);
                }

                $imagick = new \Imagick();
                $imagick->setResolution(300, 300); // زيادة الدقة
                $imagick->readImage($sourcePath . '[0-1]');

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
                $sourceId        = $data['sourceId'] ?? null;
                $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->orderBy('order', 'asc')->pluck('description')->toArray();
                $project         = Project::findOrFail(auth()->user()->current_project_id);
                $stake_holders   = $project->stakeHolders;
                $message         = 'Provided that we have the following list of document types: \n ';
                foreach ($documents_types as $des) {
                    $message .= '■ ' . $des . '\n';
                }
                $message .= ' provided that we have the following list of document types:. Do **NOT** consider or extract document type of any referenced threads mentioned in the body text such as that : example of threads =>"document type ref. no. xxxx/xxxx/xxxx/xx". or answer with “No Match” if the type not exist in this list. \n Please limit your answer to the needed information without additional words and put result in key Document_type (Document_type:.....).';
                ///////////////////////////////////////////////////////////////////////////////////////////
                $message .= 'then \n';
                $message .= 'Provided that we have the following list of stakeholders: \n';
                foreach ($stake_holders as $stake_holder) {
                    $message .= $stake_holder->name ? '■ ' . $stake_holder->name . '\n' : '■ ' . $stake_holder->narrative . '\n';
                }
                $message .= 'For any letter, normally the sender’s name is provided in the letter’s head and / or within the signature of the letter ie exist. Based on that and provided that we have the following list of stakeholders. \n';
                $message .= ' Please select from this list the document sender for that PDF or answer with “No Match” if the stakeholder not exist in this list. \n Please limit your answer to the needed information without additional words and put result in key Document_sender (Document_sender:.....).';
                $message .= 'then \n';
                /////////////////////////////////////////////////////////////////////////////////////////
                $message .= 'Provided that we have the following list of stakeholders: \n';
                foreach ($stake_holders as $stake_holder) {
                    $message .= $stake_holder->name ? '■ ' . $stake_holder->name . '\n' : '■ ' . $stake_holder->narrative . '\n';
                }
                $message .= 'Please select from the list to whom this letter was addressed or answer with “No Match” if the stakeholder not exist in this list. \n Please note that the document sender is not be the stakeholder to whom the letter was addressed. \n Please limit your answer to the needed information without additional words and put result in key Document_receiver (Document_receiver:.....).';
                $message .= 'then \n';
                /////////////////////////////////////////////////////////////////////////////////////////
                $message .= 'Please provide the Document date in the format “yyyy-mm-dd”. \n';
                $message .= ' Please limit your answer to the needed information without additional words and put result in key Document_date (Document_date:.....). \n';
                $message .= 'then \n';
                /////////////////////////////////////////////////////////////////////////////////////////
                $message .= ' Please extract the main document reference from the top part of the PDF (e.g. near "REF. NO") that follows the format of sections separated by "/" or "-" such as(“xxx/xxx/xxx/...”). Return only this in the key:
                            Document_reference: ...';

                $message .= ' then, \n';
                $message .= 'Please provide the Subject of the PDF . \n Please limit your answer to the needed information without additional words. extract subject and Return only this in the key:
                            Document_subject: ...';

                $message .= 'please please please Don\'t make the sender the receiver or vice versa, For any letter, normally the sender’s name is provided in the letter head and / or within the signature of the letter. Based on that and provided that we have the following list of stakeholders:';
                //  $message .= ' Please limit your answer to the needed information without additional words and put result in key Document_reference (Document_reference:....). \n';
                //  $message .= 'then \n';
                //  $message .= ' Extract other references mentioned in this PDF without Document_reference if exist other references and Please limit your answer to the needed information without additional words and put result in key Document_threads separated by ",,"  (Document_threads:....). \n';
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
                if ($answer != 'No answer found') {

                    $lines  = explode("\n", trim($answer));
                    $result = [];

                    foreach ($lines as $line) {
                        $parts = explode(':', $line, 2);
                        if (count($parts) == 2) {
                            $key          = trim($parts[0]);
                            $value        = trim($parts[1]);
                            $result[$key] = $value;
                        }
                    }
                    if (array_key_exists('Document_type', $result) && $result['Document_type'] != 'No Match') {
                        $documents_type = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->where('description', trim($result['Document_type']))->first();
                        if ($documents_type) {
                            $type_id = $documents_type->id;
                        } else {
                            $type_id = '';
                        }

                    } else {
                        $type_id = '';
                    }
                    if ($documents_type->from) {
                        $sender_id = $documents_type->from;
                        $sender    = $stake_holders->where('id', $sender_id)->first();
                    } else {
                        if (array_key_exists('Document_sender', $result) && $result['Document_sender'] != 'No Match') {
                            $sender = $stake_holders->where('name', $result['Document_sender'])->first();
                            if ($sender) {
                                $sender_id = $sender->id;
                            } else {
                                $sender = $stake_holders->where('narrative', $result['Document_sender'])->first();
                                if ($sender) {
                                    $sender_id = $sender->id;
                                } else {
                                    $sender_id = '';
                                }
                            }
                        } else {
                            $sender_id = '';
                        }
                    }
                    if ($documents_type->to) {
                        $receiver_id = $documents_type->to;
                        $receiver    = $stake_holders->where('id', $receiver_id)->first();

                    } else {
                        if (array_key_exists('Document_receiver', $result) && $result['Document_receiver'] != 'No Match') {
                            $receiver = $stake_holders->where('name', $result['Document_receiver'])->first();
                            if ($receiver) {
                                $receiver_id = $receiver->id;
                            } else {
                                $receiver = $stake_holders->where('narrative', $result['Document_receiver'])->first();
                                if ($receiver) {
                                    $receiver_id = $receiver->id;
                                } else {
                                    $receiver_id = '';
                                }
                            }
                        } else {
                            $receiver_id = '';
                        }
                    }
                    if (array_key_exists('Document_date', $result) && $result['Document_date'] != 'No Match') {
                        $start_date = $result['Document_date'];
                    } else {
                        $start_date = '';
                    }

                    if (array_key_exists('Document_reference', $result) && $result['Document_reference'] != 'No Match' && $result['Document_subject'] != '') {
                        $reference = $result['Document_reference'];
                    } else {
                        $reference = '';
                    }
                    if (array_key_exists('Document_subject', $result) && $result['Document_subject'] != 'No Match' && $result['Document_subject'] != '') {
                        $subject = $result['Document_subject'];
                    } else {
                        $subject = '';
                    }
                } else {
                    $type_id     = '';
                    $sender_id   = '';
                    $receiver_id = '';
                    $start_date  = '';
                    $reference   = '';
                    $subject     = '';
                }
                $uploadedFiles[$nameWithoutExtension]['type_id']          = $type_id;
                $uploadedFiles[$nameWithoutExtension]['type_name']        = $type_id != null && $type_id != '' ? $documents_type->name : '';
                $uploadedFiles[$nameWithoutExtension]['reference']        = $reference;
                $uploadedFiles[$nameWithoutExtension]['subject']          = $subject;
                $uploadedFiles[$nameWithoutExtension]['sender_id']        = $sender_id;
                $uploadedFiles[$nameWithoutExtension]['sender_text']      = $sender_id != null && $sender_id != '' ? $sender->narrative . ' - ' . $sender->role : '';
                $uploadedFiles[$nameWithoutExtension]['receiver_id']      = $receiver_id;
                $uploadedFiles[$nameWithoutExtension]['receiver_text']    = $receiver_id != null && $receiver_id != '' ? $receiver->narrative . ' - ' . $receiver->role : '';
                $uploadedFiles[$nameWithoutExtension]['start_date_value'] = $start_date;
                $uploadedFiles[$nameWithoutExtension]['start_date']       = $start_date != '' ? date('d-M-y', strtotime($start_date)) : '';

            } else {
                $uploadedFiles[$nameWithoutExtension]['type_id']          = '';
                $uploadedFiles[$nameWithoutExtension]['type_name']        = '';
                $uploadedFiles[$nameWithoutExtension]['reference']        = '';
                $uploadedFiles[$nameWithoutExtension]['subject']          = '';
                $uploadedFiles[$nameWithoutExtension]['sender_id']        = '';
                $uploadedFiles[$nameWithoutExtension]['sender_text']      = '';
                $uploadedFiles[$nameWithoutExtension]['receiver_id']      = '';
                $uploadedFiles[$nameWithoutExtension]['receiver_text']    = '';
                $uploadedFiles[$nameWithoutExtension]['start_date_value'] = '';
                $uploadedFiles[$nameWithoutExtension]['start_date']       = '';

            }

        }

        $html = view('project_dashboard.upload_group_documents.documents_list', compact('uploadedFiles'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Files uploaded successfully',
            'html'    => $html,
        ]);
    }

    public function saveDocuments(Request $request)
    {
        // dd($request->all());
        session()->forget('path');
        $documents        = $request->input('documents');
        $testDocumentsIDs = [];
        foreach ($documents as $docData) {
            $doc = TestDocument::create([
                'doc_type_id'     => $docData['type'] ? intval($docData['type']) : null,
                'user_id'         => $docData['analyzed_by'] ? intval($docData['analyzed_by']) : null,
                'project_id'      => auth()->user()->current_project_id,
                'subject'         => $docData['subject'],
                'start_date'      => $docData['date'],
                'end_date'        => null,
                'from_id'         => $docData['from'] ? intval($docData['from']) : null,
                'to_id'           => $docData['to'] ? intval($docData['to']) : null,
                'reference'       => $docData['reference'],
                'revision'        => $docData['revision'],
                'status'          => null,
                'notes'           => $docData['notes'],
                'storage_file_id' => intval($docData['doc_id']),
                'threads'         => null,
                'file_id'         => $docData['assign_to_file_id'] ? intval($docData['assign_to_file_id']) : null,

            ]);
            if ($doc->doc_type_id != null && $doc->user_id != null && $doc->subject != null && $doc->start_date != null && $doc->reference != null) {
                $doc->confirmed = '1';
                $doc->save();
            }
            $testDocumentsIDs[] = $doc->id;
        }
        session(['testDocumentsIDs' => $testDocumentsIDs]);
        $all_documents = TestDocument::whereIn('id', $testDocumentsIDs)->get();
        $html          = view('project_dashboard.upload_group_documents.table', compact('all_documents'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Documents saved successfully',
            'html'    => $html,
        ]);
    }

    public function view_doc($id)
    {
        session()->forget('path');
        $project         = Project::findOrFail(auth()->user()->current_project_id);
        $users           = $project->assign_users;
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->orderBy('order', 'asc')->get();
        $stake_holders   = $project->stakeHolders;
        $document        = TestDocument::where('id', $id)->first();
        $threads         = Document::where('project_id', auth()->user()->current_project_id)->pluck('reference');
        $folders         = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');
        session(['path' => $document->storageFile->path]);

        return view('project_dashboard.upload_group_documents.test_doc_view', compact('documents_types', 'users', 'stake_holders', 'document', 'threads', 'folders'));
    }

    public function update_test_document(Request $request, $id)
    {
        // dd($request->all());
        TestDocument::where('id', $id)->update([

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
        $doc = TestDocument::where('id', $id)->first();
        if ($request->file_id) {
            $doc->file_id = $request->file_id;
        }
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
        if ($doc->doc_type_id != null && $doc->user_id != null && $doc->subject != null && $doc->start_date != null && $doc->reference != null) {
            $doc->confirmed = '1';

        }
        $doc->save();
        session()->forget('path');

        return response()->json(['message' => 'Document updated successfully.']);
    }

    public function check_test_documents()
    {
        $docs = [];
        if (session('testDocumentsIDs')) {
            $docs = session('testDocumentsIDs');
        }

        $IDs = TestDocument::whereIn('id', $docs)->where('confirmed', '1')->pluck('id');

        return response()->json(['IDs' => $IDs]);
    }

    public function import_group_documents()
    {
        $docs     = session('testDocumentsIDs');
        $mistakes = [];
        $notes    = [];
        foreach ($docs as $doc) {
            $testDoc  = TestDocument::find($doc);
            $document = Document::where('project_id', auth()->user()->current_project_id)->where('storage_file_id', $testDoc->storage_file_id)->first();
            if ($document) {
                $mistakes[] = 'Document "' . $testDoc->storageFile->file_name . '" is existed in CMW';
            } else {
                do {
                    $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
                } while (Document::where('slug', $invitation_code)->exists());

                $new_doc = Document::create([
                    'slug'              => $invitation_code,
                    'doc_type_id'       => $testDoc->doc_type_id,
                    'user_id'           => $testDoc->user_id,
                    'project_id'        => auth()->user()->current_project_id,
                    'subject'           => $testDoc->subject,
                    'start_date'        => $testDoc->start_date,
                    'end_date'          => $testDoc->end_date,
                    'from_id'           => intval($testDoc->from_id),
                    'to_id'             => intval($testDoc->to_id),
                    'reference'         => $testDoc->reference,
                    'revision'          => $testDoc->revision,
                    'status'            => $testDoc->status,
                    'notes'             => $testDoc->notes,
                    'storage_file_id'   => intval($testDoc->storage_file_id),
                    'threads'           => $testDoc->threads,
                    'analyzed'          => $testDoc->analyzed,
                    'analysis_complete' => $testDoc->analysis_complete,

                ]);

                if ($testDoc->file_id) {
                    $fileDoc     = FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $testDoc->file_id, 'document_id' => $new_doc->id]);
                    $start_date  = $fileDoc->note->start_date;
                    $end_date    = $fileDoc->note->end_date;
                    $gantt_chart = GanttChartDocData::create(['file_document_id' => $fileDoc->id]);

                    $gantt_chart->lp_sd = $start_date;
                    $gantt_chart->lp_fd = $end_date;
                    $sections           = [];
                    $sections[]         = [
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
                $notes[] = 'Document "' . $testDoc->storageFile->file_name . '" with Ref : "' . $testDoc->reference . '" imported successfully in CMW';
            }
        }
        TestDocument::whereIn('id', $docs)->delete();
        session()->forget('testDocumentsIDs');
        $html = view('project_dashboard.upload_group_documents.report', compact('notes', 'mistakes'))->render();

        return response()->json([
            'success' => true,
            'message' => 'successfully',
            'html'    => $html,

        ]);
    }

    public function formate_date(Request $request)
    {

        $date        = $request->date;
        $cleanedDate = preg_replace('/[^a-zA-Z0-9]/', '.', $date); // Replace any non-alphanumeric character with space
                                                                   // Create DateTime object from the original format (y/m/d)
        $dateTime = DateTime::createFromFormat($request->formate, $cleanedDate);

        if ($dateTime) {
            $formattedDate1 = $dateTime->format('d-M-y');
            $formattedDate2 = $dateTime->format('Y-m-d');

            return response()->json([
                'success'       => true,
                'message'       => 'successfully',
                'parsedDate'    => $formattedDate1,
                'formattedDate' => $formattedDate2,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format!',

            ]);
        }
    }
}
