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

class AccountDashboardController extends ApiController
{

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        $user = auth()->user();
        $user->current_project_id=null;
        $user->save();
        $account = Account::findOrFail($user->current_account_id);
        if(auth()->user()->roles->first()->name =='Super Admin' || auth()->user()->roles->first()->name =='Account Admin'){
            $project_count= Project::where('account_id',$user->current_account_id)->count();
        }else{
            $project_count= 0;
        }

        return view('account_dashboard.home', compact('account','project_count'));
    }

    public function EPS()
    {

        $account = Account::findOrFail(auth()->user()->current_account_id);

        $EPS = Category::where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->with('allChildren')->get();
        //dd($EPS->first()->allChildren->first()->allChildren->first()->allChildren);
        return view('account_dashboard.EPS', compact('EPS', 'account'));
    }

    public function getChildrenEPS(Request $request)
    {
        $EPS = Category::where('parent_id', $request->eps_id)->get();
        return $this->sendResponse($EPS, 'success');

    }
    public function getProjectsEPS(Request $request){
       
        $projects=Project::where('category_id',$request->eps_id)->get();
        return $this->sendResponse($projects, 'success');

    }
    public function deleteChildrenEPS(Request $request)
    {
        Category::where('id', $request->eps_id)->delete();
        return $this->sendResponse(null, 'success');

    }
    public function store_EPS(Request $request)
    {
        $last_Category = Category::orderBy('id', 'desc')->first();

        $last_number = 0;
        if ($last_Category && preg_match('/EPS-(\d+)$/', $last_Category->code, $matches)) {
            $last_number = intval($matches[1]); // Extract the last numeric part safely
        }

        do {
            $process_last_number = 'EPS-' . sprintf('%06d', $last_number + 1);
            $EPSExists = Category::where('code', $process_last_number)->exists();
            $last_number++;
        } while ($EPSExists);
        Category::create(['code' => $process_last_number,'name' => $request->epsName,'account_id' => $request->accountID,'parent_id' => $request->selected_category]);
        return redirect('/account/EPS')->with('success', 'EPS created successfully.');

    }
    public function rename_EPS(Request $request)
    {
        $eps = Category::findOrFail($request->category_id);
        $eps->name = $request->Name;
        $eps->save();
        return redirect('/account/EPS')->with('success', 'EPS Renamed successfully.');

    }

    /////////////////////////////////////////////////////
    public function create_project_view()
    {
        $roles = [
            'Employer',
            'Contractor',
            'Engineer',
            'Project Manager',
            'Consultant',
            'Sub-Contractor',
            'Authority',
            'Another contractor',
            'Lower-Tier subcontractor',
            'Other',
        ];
        $EPS = Category::where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->with('allChildren')->get();
        $route='store_project';
        return view('account_dashboard.projects.create', compact('roles', 'EPS','route'));
    }

    public function store_project(Request $request)
    {
        $result = $this->projectService->createProject($request);

        if (!$result['success']) {
            return redirect()->back()->withInput()->withErrors($result['errors']);
        }

        return redirect('/account')->with('success', 'Project created successfully.');
    }
}
