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
use App\Models\ProjectFolder;
use App\Models\ProjectFile;
use App\Models\StorageFile;
use App\Models\FileDocument;
use Illuminate\Validation\Rule;

class FileDocumentController extends ApiController
{
    public function index($id){
        $file=ProjectFile::where('slug',$id)->first();
        $documents=FileDocument::where('file_id',$file->id)->get();
        return view('project_dashboard.file_documents.index',compact('documents','file'));
    }
}