<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\Account;
use App\Models\Category;
use App\Models\Project;
use App\Models\StakeHolder;
use App\Models\Milestone;
use App\Services\ProjectService;
use Illuminate\Validation\Rule;

class ProjectDashboardController extends ApiController
{

    public function index()
    {
        $user = auth()->user();
        $project = Project::findOrFail($user->current_project_id); 
        return view('project_dashboard.home', compact('project'));

    }
}