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
use App\Models\Project;
use Illuminate\Validation\Rule;

class FileDocumentController extends ApiController
{
    public function index($id){

    }
}