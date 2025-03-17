<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\StorageFile;
use App\Models\User;
use App\Models\ProjectFolder;
use App\Models\ProjectFile;
use App\Models\ContractTag;
use App\Models\FileDocument;
use Illuminate\Validation\Rule;

class FileDocumentController extends ApiController
{
    public function index($id){
        $file=ProjectFile::where('slug',$id)->first();
        $documents=FileDocument::where('file_id',$file->id)->orderBy('sn','asc')->get();
        $specific_file_doc= session('specific_file_doc');
        session()->forget('specific_file_doc');
        return view('project_dashboard.file_documents.index',compact('documents','file','specific_file_doc'));
    }

    public function file_document_first_analyses($id){
        $user=auth()->user();
        session(['specific_file_doc' => $id]);
        $doc=FileDocument::findOrFail($id);
        $tags=ContractTag::where('account_id',$user->current_account_id)->where('project_id',$user->current_project_id)->orderBy('order','asc')->get();
        return view('project_dashboard.file_documents.doc_first_analyses',compact('doc','tags'));
    }

    public function upload_editor_image(Request $request)
    {

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:51200' // 10MB max
        ]);

        $file = $request->file('image');
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
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/images';
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
    private function hasContent($narrative) {
        // Remove all HTML tags except text content
        $text = strip_tags($narrative);
        
        // Remove extra spaces & line breaks
        $text = trim($text);
        
        // Check if there's any actual content
        return !empty($text);
    }
    public function store_file_document_first_analyses(Request $request,$id){
        if ($this->hasContent($request->narrative)) {
            $narrative=$request->narrative;
        } else {
            $narrative=null;
        }

        $doc = FileDocument::findOrFail($id);
        $doc->update([
            'narrative'  => $narrative,
            'notes1'     => $request->notes1,
            'notes2'     => $request->notes2,
            'sn'         => $request->sn,
            'forClaim'   => $request->forClaim ? '1' : '0',
            'forChart'   => $request->forChart ? '1' : '0',
            'forLetter'  => $request->forLetter ? '1' : '0',
        ]);
    
        // Assign tags (assuming many-to-many relationship)
        if ($request->has('tags')) {
            $doc->tags()->sync($request->tags); // Sync tags
        }
        if($request->action=='save'){
            return redirect('/project/file-document-first-analyses/'. $doc->id)->with('success', 'analyses for "' . $doc->document->subject .'" document saved successfully.');
        }else{
            return redirect('/project/file/' . $doc->file->slug . '/documents')->with('success', 'analyses for "' . $doc->document->subject .'" document saved successfully.');
        }
        
    }
}