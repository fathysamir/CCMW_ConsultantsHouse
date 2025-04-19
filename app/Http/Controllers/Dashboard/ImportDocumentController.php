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
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ImportDocumentController extends ApiController
{
    public function import_docs_view(){
        session()->forget('extractedDataExcelFile');
        session()->forget('uploadedPDFFiles');
        session()->forget('selected_file');
        $folders = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive','Recycle Bin'])->pluck('name', 'id');
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->get();
        $stake_holders = $project->stakeHolders;
        return view('project_dashboard.import_documents.index',compact('folders','users','project','documents_types','stake_holders'));
    }
    public function upload_import_excel_file(Request $request)
    {

        $request->validate([
            'file' => 'required|file|max:51200' // 10MB max
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());

        $data = [];
        $sheets=[];
        foreach ($spreadsheet->getSheetNames() as $sheetName) {

            $sheet = $spreadsheet->getSheetByName($sheetName);
            $rows = $sheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                continue; // Skip empty or invalid sheets
            }
            $sheets[]=$sheetName;
            $headers = array_map('trim', $rows[0]); // Extract headers
            $sheetData = [];

            foreach ($headers as $header) {
                $sheetData[$header] = [];
            }

            // Extract row data
            for ($i = 1; $i < count($rows); $i++) {
                foreach ($headers as $index => $header) {
                    if (isset($rows[$i][$index])) {
                        $sheetData[$header][] = $rows[$i][$index];
                    }else{
                        $sheetData[$header][] = null;
                    }
                }
            }

            $data[$sheetName] = $sheetData;
        }
        session(['extractedDataExcelFile' => $data]);
        
        $name = $file->getClientOriginalName();
        $size = $file->getSize();
        $type = $file->getMimeType();

        $storageFile = StorageFile::where('user_id', auth()->user()->id)->where('project_id', auth()->user()->current_project_id)->where('file_name', $name)->where('size', $size)->where('file_type', $type)->first();
        if ($storageFile) {
            return response()->json([
                'success' => true,
                'file' => $storageFile,
                'sheets' => $sheets
            ]);
        }
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Create project-specific folder in public path
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/imports/excel';
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
            'file' => $storageFile,
            'sheets' => $sheets
        ]);
    }

    public function upload_multi_files(Request $request){
        $uploadedFiles = [];
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
        session(['uploadedPDFFiles' => $uploadedFiles]);
        
        return response()->json([
            'success' => true,
            'message' => 'Files uploaded successfully',
            'uploadedPDFFiles' => $uploadedFiles
        ]);
    }

    public function getHeaders(Request $request){
        $sheet = $request->input('sheet');
        session(['selected_file' => $request->input('file_id')]);

       
        // if($file){
        //     $docs=session('uploadedPDFFiles');
        //     foreach($docs as $key=>$id){
        //         $fileDoc = FileDocument::where('file_id', $file)->where('document_id', $id)->first();
        //         if (!$fileDoc) {
        //             FileDocument::create(['user_id' => auth()->user()->id,'file_id' => $file,'document_id' => $id]);
        //         }
        //     }
        // }
        $sheets=session('extractedDataExcelFile');
        $headersArray=$sheets[$sheet];
        $headers=[];
        foreach($headersArray as $key=>$val){
            $headers[]=$key;
        }
        return response()->json([
            'success' => true,
            'message' => '',
            'headers' => $headers
            
        ]);
    }

    public function start_import(Request $request){
       
        $sheets=session('extractedDataExcelFile');
        $rowsCount=$sheets[$request->sheet][$request->reference];
        $unImportedRows=[];
        $importedRows=[];
        
        $uploadedFiles=session('uploadedPDFFiles');
        $selected_file=session('selected_file');
        foreach($rowsCount as $index=>$val){
            $success=true;
            $storage_doc_id=null;
            $doc_type=null;
            $subject=null;
            $start_date=null;
            $reference=null;
            $from=null;
            $to=null;
            $return_date=null;
            $revision=null;
            $status=null;
            $notes=null;
            $threads=null;
            if($sheets[$request->sheet][$request->doc_file_name][$index]){
                $x=$sheets[$request->sheet][$request->doc_file_name][$index];
                if (array_key_exists($x, $uploadedFiles)) {
                    $existed_docu=Document::where('project_id',auth()->user()->current_project_id)->where('storage_file_id',$uploadedFiles[$x])->first();
                    if($existed_docu){
                        $unImportedRows['Row '. $index+2][]='PDF File "' . $x . '.pdf" Is Existed In CMW';
                        $success=false;
                    }else{
                        $storage_doc_id = $uploadedFiles[$x];
                    }
                }else{
                    $unImportedRows['Row '. $index+2][]='PDF File "' . $x . '.pdf" Not Uploaded';
                    $success=false;
                }
            }else{
                $unImportedRows['Row '. $index+2][]='"' . $request->doc_file_name . '" is Empty';
                $success=false;
            }
            if($request->typeForAll!=null){
               $doc_type= $request->typeForAll;
            }else{
                if($sheets[$request->sheet][$request->type][$index]){
                    $documents_type = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->whereRaw('LOWER(name) = ?', [strtolower($sheets[$request->sheet][$request->type][$index])])->first();
                    if($documents_type){
                        $doc_type=$documents_type->id;
                    }else{
                        $unImportedRows['Row '. $index+2][]='"' . $request->type . '" Not Found';
                        $success=false;
                    }
                }else{
                    $unImportedRows['Row '. $index+2][]='"' . $request->type . '" is Empty';
                    $success=false;
                }
                
            }
            if($sheets[$request->sheet][$request->subject][$index]==null){
                $unImportedRows['Row '. $index+2][]='"' . $request->subject . '" is Empty';
                $success=false;
            }else{
                $subject=$sheets[$request->sheet][$request->subject][$index];
            }
            if($request->start_dateForAll!=null){
                $start_date=date('Y-m-d',strtotime($request->start_dateForAll));
            }else{
                if($sheets[$request->sheet][$request->start_date][$index]==null){
                    $unImportedRows['Row '. $index+2][]='"' . $request->start_date . '" is Empty';
                    $success=false;
                }else{
                    $start_date=date('Y-m-d',strtotime($sheets[$request->sheet][$request->start_date][$index]));
                }
            }
            
            if($sheets[$request->sheet][$request->reference][$index]==null){
                $unImportedRows['Row '. $index+2][]='"' . $request->reference . '" is Empty';
                $success=false;
            }else{
                $reference=$sheets[$request->sheet][$request->reference][$index];
            }

            if($success==true){
                if($request->fromForAll!=null){
                    $from=$request->fromForAll;
                }else{
                    if($request->from){
                        if($sheets[$request->sheet][$request->from][$index]==null){
                            $importedRows['Row '. $index+2][]='"' . $request->from . '" is Empty';
                        }else{
                            $searchName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $sheets[$request->sheet][$request->from][$index]));
                            $stakeholder1=StakeHolder::where('project_id', auth()->user()->current_project_id)->whereRaw('LOWER(REGEXP_REPLACE(narrative, "[^a-z0-9]", "")) = ?', [$searchName])->first();
                            if($stakeholder1){
                                $from=$stakeholder1->id;
                            }else{
                                $stakeholder2=StakeHolder::where('project_id', auth()->user()->current_project_id)->whereRaw('LOWER(REGEXP_REPLACE(role, "[^a-z0-9]", "")) = ?', [$searchName])->first();
                                if($stakeholder2){
                                    $importedRows['Row '. $index+2][]='"' . $request->from . '" is stored as Stakeholder with Chronology "' .$stakeholder2->narrative. '"';
                                    $from=$stakeholder2->id;
                                }else{
                                    $newStakeholder=StakeHolder::create([
                                        'project_id' => auth()->user()->current_project_id,
                                        'name' => $sheets[$request->sheet][$request->from][$index],
                                        'role' => $sheets[$request->sheet][$request->from][$index],
                                        'narrative' => $sheets[$request->sheet][$request->from][$index],
                                        'article' => null
                                    ]);
                                    $from=$newStakeholder->id;
                                    $importedRows['Row '. $index+2][]='A new stakeholder with chronology ' .$newStakeholder->narrative. ' has been created and store "' . $request->from . '" as it.';
                                }
                            }
                        }
                    }
                    
                }

                if($request->toForAll!=null){
                    $to=$request->toForAll;
                }else{
                    if($request->to){
                        if($sheets[$request->sheet][$request->to][$index]==null){
                            $importedRows['Row '. $index+2][]='"' . $request->to . '" is Empty';
                        }else{
                            $toSearchName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $sheets[$request->sheet][$request->to][$index]));
                            $toStakeholder1=StakeHolder::where('project_id', auth()->user()->current_project_id)->whereRaw('LOWER(REGEXP_REPLACE(narrative, "[^a-z0-9]", "")) = ?', [$toSearchName])->first();
                            if($toStakeholder1){
                                $to=$toStakeholder1->id;
                            }else{
                                $toStakeholder2=StakeHolder::where('project_id', auth()->user()->current_project_id)->whereRaw('LOWER(REGEXP_REPLACE(role, "[^a-z0-9]", "")) = ?', [$toSearchName])->first();
                                if($toStakeholder2){
                                    $importedRows['Row '. $index+2][]='"' . $request->to . '" is stored as Stakeholder with Chronology "' .$toStakeholder2->narrative. '"';
                                    $to=$toStakeholder2->id;
                                }else{
                                    $toNewStakeholder=StakeHolder::create([
                                        'project_id' => auth()->user()->current_project_id,
                                        'name' => $sheets[$request->sheet][$request->to][$index],
                                        'role' => $sheets[$request->sheet][$request->to][$index],
                                        'narrative' => $sheets[$request->sheet][$request->to][$index],
                                        'article' => null
                                    ]);
                                    $to=$toNewStakeholder->id;
                                    $importedRows['Row '. $index+2][]='A new stakeholder with chronology ' .$toNewStakeholder->narrative. ' has been created and store "' . $request->to . '" as it.';
                                }
                            }
                        }
                    }
                }
                if($request->end_date){
                    if($sheets[$request->sheet][$request->end_date][$index]==null){
                        $importedRows['Row '. $index+2][]='"' . $request->end_date . '" is Empty';
                    }else{
                        $return_date=date('Y-m-d',strtotime($sheets[$request->sheet][$request->end_date][$index]));
                    }
                }
               
                if($request->revision){
                    if($sheets[$request->sheet][$request->revision][$index]==null){
                        $importedRows['Row '. $index+2][]='"' . $request->revision . '" is Empty'; 
                    }
                    $revision=$sheets[$request->sheet][$request->revision][$index];
                }
                

                if($request->statusForAll!=null){
                    $status=$request->statusForAll;
                }else{
                    if($request->status){
                        if($sheets[$request->sheet][$request->status][$index]==null){
                            $importedRows['Row '. $index+2][]='"' . $request->status . '" is Empty'; 
                        }
                        $status=$sheets[$request->sheet][$request->status][$index];
                    }
                }

                if($request->noteForAll!=null){
                    $notes=$request->noteForAll;
                }else{
                    if($request->note){
                        if($sheets[$request->sheet][$request->note][$index]==null){
                            $importedRows['Row '. $index+2][]='"' . $request->note . '" is Empty'; 
                        }
                        $notes=$sheets[$request->sheet][$request->note][$index];
                    }
                    
                }

                if($request->threadForAll!=null){
                    $threads=explode(',',$request->noteForAll);
                }else{
                    if($request->thread){
                        if($sheets[$request->sheet][$request->thread][$index]==null){
                            $importedRows['Row '. $index+2][]='"' . $request->thread . '" is Empty'; 
                        }else{
                            $threads=explode(',',$sheets[$request->sheet][$request->thread][$index]);
                        }
                    }
                    
                }
                do {
                    $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
                } while (Document::where('slug', $invitation_code)->exists());
        
                $doc = Document::create([
                    'slug' => $invitation_code,
                    'doc_type_id' => $doc_type,
                    'user_id' => $request->analyzed_By,
                    'project_id' => auth()->user()->current_project_id,
                    'subject' => $subject,
                    'start_date' => $start_date,
                    'end_date' => $return_date,
                    'from_id' => intval($from),
                    'to_id' => intval($to),
                    'reference' => $reference,
                    'revision' => $revision,
                    'status' => $status,
                    'notes' => $notes,
                    'storage_file_id' => intval($storage_doc_id),
                    'threads' => $threads && count($threads) > 0 ? json_encode($threads) : null,
                    'analysis_complete'=> $request->analysis_complete
        
                ]);
                if($selected_file){
                    FileDocument::create(['user_id' => auth()->user()->id,'file_id' => $selected_file,'document_id' => $doc->id]);
                }
                $importedRows['Row '. $index+2][]='File with Ref: ' . $reference . ' is uploaded successfully';
            }
            
        }
        $html = view('project_dashboard.import_documents.report', compact('importedRows', 'unImportedRows'))->render();
        return response()->json([
            'success' => true,
            'message' => 'Files Assigned successfully',
            'html' => $html 
        ]);

    }

}