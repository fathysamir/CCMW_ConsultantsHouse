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
use App\Models\StakeHolder;
use App\Models\FileDocument;
use App\Models\TestDocument;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DateTime;

class UploadGroupDocumentController extends ApiController
{
    public function index(){
        session()->forget('path');
        session()->forget('testDocumentsIDs');
        $folders = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive','Recycle Bin'])->pluck('name', 'id');
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->get();
        $stake_holders = $project->stakeHolders;
        return view('project_dashboard.upload_group_documents.index',compact('folders','users','project','documents_types','stake_holders'));
    }
    public function upload_multi_files(Request $request){
        $uploadedFiles = [];
        ini_set('upload_max_filesize', '250M');
        ini_set('post_max_size', '250M');
        ini_set('max_file_uploads', '100');
        foreach ($request->file('files') as $file) {
            $name = $file->getClientOriginalName();
            $size = $file->getSize();
            $type = $file->getMimeType();
    
            $storageFile = StorageFile::where('user_id', auth()->user()->id)->where('project_id', auth()->user()->current_project_id)->where('file_name', $name)->where('size', $size)->where('file_type', $type)->first();
            if ($storageFile) {
                $nameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);

                $uploadedFiles[$nameWithoutExtension]=$storageFile->id;
            }else{
                $nameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $cleanedName = preg_replace('/[^a-zA-Z0-9]/', '-', $nameWithoutExtension);
                $fileName = time() . '_' . $cleanedName . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
    
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
            }
            $nameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);

            $uploadedFiles[$nameWithoutExtension]=$storageFile->id;
        }
        $html = view('project_dashboard.upload_group_documents.documents_list', compact('uploadedFiles'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Files uploaded successfully',
            'html' => $html
        ]);
    }

    public function saveDocuments(Request $request){
       // dd($request->all());
       session()->forget('path');
        $documents = $request->input('documents');
        $testDocumentsIDs = [];
        foreach ($documents as $docData) {
            $doc = TestDocument::create([
                'doc_type_id' => $docData['type'] ? intval($docData['type']) : null,
                'user_id' => $docData['analyzed_by'] ? intval($docData['analyzed_by']) : null,
                'project_id' => auth()->user()->current_project_id,
                'subject' => $docData['subject'],
                'start_date' => $docData['date'],
                'end_date' => null,
                'from_id' => $docData['from'] ? intval($docData['from']) : null,
                'to_id' => $docData['to'] ? intval($docData['to']) : null,
                'reference' => $docData['reference'],
                'revision' => $docData['revision'],
                'status' => null,
                'notes' => $docData['notes'],
                'storage_file_id' => intval($docData['doc_id']),
                'threads' => null,
                'file_id' => $docData['assign_to_file_id'] ? intval($docData['assign_to_file_id']) : null
    
            ]);
            if($doc->doc_type_id!=null && $doc->user_id!=null && $doc->subject!=null && $doc->start_date!=null && $doc->reference!=null){
                $doc->confirmed='1';
                $doc->save();
            }
            $testDocumentsIDs[]=$doc->id;
        }
        session(['testDocumentsIDs' => $testDocumentsIDs]);
        $all_documents=TestDocument::whereIn('id',$testDocumentsIDs)->get();
        $html = view('project_dashboard.upload_group_documents.table', compact('all_documents'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Documents saved successfully',
            'html' => $html
        ]);
    }


    public function view_doc($id){
        session()->forget('path');
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->get();
        $stake_holders = $project->stakeHolders;
        $document = TestDocument::where('id', $id)->first();
        $threads = Document::where('project_id', auth()->user()->current_project_id)->pluck('reference');
        $folders = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive','Recycle Bin'])->pluck('name', 'id');
        session(['path' => $document->storageFile->path]);
        return view('project_dashboard.upload_group_documents.test_doc_view', compact('documents_types', 'users', 'stake_holders', 'document', 'threads','folders'));
    }

    public function update_test_document(Request $request,$id){
        //dd($request->all());
        TestDocument::where('id', $id)->update([

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
        if($doc->doc_type_id!=null && $doc->user_id!=null && $doc->subject!=null && $doc->start_date!=null && $doc->reference!=null){
            $doc->confirmed='1';
            
        }
        $doc->save();
        session()->forget('path');
        return response()->json(['message' => 'Document updated successfully.']);
    }

    public function check_test_documents(){
        $docs=[];
        if(session('testDocumentsIDs')){
            $docs=session('testDocumentsIDs');
        }
       
        $IDs=TestDocument::whereIn('id',$docs)->where('confirmed','1')->pluck('id');
        
        return response()->json(['IDs' => $IDs]);
    }

    public function import_group_documents(){
        $docs=session('testDocumentsIDs');
        $mistakes=[];
        $notes=[];
        foreach($docs as $doc){
            $testDoc=TestDocument::find($doc);
            $document=Document::where('project_id',auth()->user()->current_project_id)->where('storage_file_id',$testDoc->storage_file_id)->first();
            if($document){
                $mistakes[]='Document "' . $testDoc->storageFile->file_name . '" is existed in CMW';
            }else{
                do {
                    $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
                } while (Document::where('slug', $invitation_code)->exists());
        
                $new_doc = Document::create([
                    'slug' => $invitation_code,
                    'doc_type_id' => $testDoc->doc_type_id,
                    'user_id' => $testDoc->user_id,
                    'project_id' => auth()->user()->current_project_id,
                    'subject' => $testDoc->subject,
                    'start_date' => $testDoc->start_date,
                    'end_date' => $testDoc->end_date,
                    'from_id' => intval($testDoc->from_id),
                    'to_id' => intval($testDoc->to_id),
                    'reference' => $testDoc->reference,
                    'revision' => $testDoc->revision,
                    'status' => $testDoc->status,
                    'notes' => $testDoc->notes,
                    'storage_file_id' => intval($testDoc->storage_file_id),
                    'threads' => $testDoc->threads,
                    'analyzed' => $testDoc->analyzed,
                    'analysis_complete' => $testDoc->analysis_complete,
        
                ]);
        
                
                if($testDoc->file_id){
                    FileDocument::create(['user_id' => auth()->user()->id,'file_id' => $testDoc->file_id,'document_id' => $new_doc->id]);
                }
                $notes[]='Document "' . $testDoc->storageFile->file_name . '" with Ref : "' .$testDoc->reference. '" imported successfully in CMW';
            }
        }
        TestDocument::whereIn('id',$docs)->delete();
        session()->forget('testDocumentsIDs');
        $html = view('project_dashboard.upload_group_documents.report', compact('notes','mistakes'))->render();

        return response()->json([
            'success' => true,
            'message' => 'successfully',
            'html' => $html,
           
        ]);
    }

    public function formate_date(Request $request){
       
        $date = $request->date;
        $cleanedDate = preg_replace('/[^a-zA-Z0-9]/', '.', $date); // Replace any non-alphanumeric character with space
        // Create DateTime object from the original format (y/m/d)
        $dateTime = DateTime::createFromFormat($request->formate, $cleanedDate);

        if ($dateTime) {
            $formattedDate1 = $dateTime->format('d-M-y');
            $formattedDate2 = $dateTime->format('Y-m-d');
            return response()->json([
                'success' => true,
                'message' => 'successfully',
                'parsedDate' => $formattedDate1,
                'formattedDate' => $formattedDate2
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format!',
                
            ]);
        }
    }
}