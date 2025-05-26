<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Mail\SendInvitation;
use App\Models\Account;
use App\Models\AccountUser;
use App\Models\Category;
use App\Models\Invitation;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

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
        $user->current_project_id = null;
        $user->save();
        $account = Account::findOrFail($user->current_account_id);
        if (auth()->user()->roles->first()->name == 'Super Admin') {
            $project_count = Project::where('account_id', $user->current_account_id)->count();
        } else {
            $project_count = $user->assign_projects->where('account_id', $user->current_account_id)->count();
        }

        return view('account_dashboard.home', compact('account', 'project_count'));
    }

    public function EPS()
    {

        $account = Account::findOrFail(auth()->user()->current_account_id);
        if (auth()->user()->roles->first()->name == 'Super Admin') {
            // $project_count= Project::where('account_id',$user->current_account_id)->get();
            $EPS = Category::where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();

        } else {
            $hasRole = auth()->user()->accounts()
                ->where('accounts.id', auth()->user()->current_account_id)
                ->wherePivot('role', 'Admin Account')
                ->exists();
            if ($hasRole) {
                $EPS = Category::where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
            } else {
                $projectsId = auth()->user()->assign_projects->pluck('id')->toArray();
                $categoriesId = Project::whereIn('id', $projectsId)->pluck('category_id')->toArray();
                $subCategories = Category::whereIn('id', $categoriesId)->get();

                // Get top-level parents
                $parentCategoryIds = $subCategories->map(function ($cat) {
                    return $cat->getRootCategory()->id;
                })->unique()->toArray();
                $EPS = Category::whereIn('id', $parentCategoryIds)->where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();

                // $EPS = Category::whereIn('id',$categoriesId)->where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
            }

        }

        // dd($EPS->first()->allChildren->first()->allChildren->first()->allChildren);
        return view('account_dashboard.EPS', compact('EPS', 'account'));
    }

    public function getChildrenEPS(Request $request)
    {
        if (auth()->user()->roles->first()->name == 'Super Admin') {
            // $project_count= Project::where('account_id',$user->current_account_id)->get();
            $EPS = Category::where('parent_id', $request->eps_id)->orderBy('eps_order')->get();

        } else {
            $hasRole = auth()->user()->accounts()
                ->where('accounts.id', auth()->user()->current_account_id)
                ->wherePivot('role', 'Admin Account')
                ->exists();
            if ($hasRole) {
                $EPS = Category::where('parent_id', $request->eps_id)->orderBy('eps_order')->get();
            } else {
                $projectsId = auth()->user()->assign_projects->pluck('id')->toArray();
                $categoriesId = Project::whereIn('id', $projectsId)->pluck('category_id')->toArray();
                $subCategories = Category::whereIn('id', $categoriesId)->get();

                // Get top-level parents
                $eps_id = $request->eps_id;
                $parentCategoryIds = $subCategories->map(function ($cat) use ($eps_id) {
                    return $cat->getRootCategory($eps_id)->id;
                })->unique()->toArray();
                $EPS = Category::whereIn('id', $parentCategoryIds)->where('parent_id', $request->eps_id)->orderBy('eps_order')->get();
            }

        }

        return $this->sendResponse($EPS, 'success');

    }

    public function getProjectsEPS(Request $request)
    {
        if (auth()->user()->roles->first()->name == 'Super Admin') {
            // $project_count= Project::where('account_id',$user->current_account_id)->get();
            $projects = Project::where('category_id', $request->eps_id)->get();
        } else {
            $hasRole = auth()->user()->accounts()
                ->where('accounts.id', auth()->user()->current_account_id)
                ->wherePivot('role', 'Admin Account')
                ->exists();
            if ($hasRole) {
                $projects = Project::where('category_id', $request->eps_id)->get();
            } else {
                $projectsId = auth()->user()->assign_projects->pluck('id')->toArray();
                $projects = Project::whereIn('id', $projectsId)->where('category_id', $request->eps_id)->get();
            }

        }

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
            $process_last_number = 'EPS-'.sprintf('%06d', $last_number + 1);
            $EPSExists = Category::where('code', $process_last_number)->exists();
            $last_number++;
        } while ($EPSExists);
        $last_eps = Category::where('account_id', $request->accountID)->where('parent_id', $request->selected_category)->orderBy('id', 'desc')->first();
        if ($last_eps) {
            $last_eps_order = $last_eps->eps_order;
            Category::create(['code' => $process_last_number, 'name' => $request->epsName, 'account_id' => $request->accountID, 'eps_order' => $last_eps_order + 1, 'parent_id' => $request->selected_category]);

        } else {
            Category::create(['code' => $process_last_number, 'name' => $request->epsName, 'account_id' => $request->accountID, 'eps_order' => 1, 'parent_id' => $request->selected_category]);

        }

        return redirect('/account/EPS')->with('success', 'EPS created successfully.');

    }

    public function reorder_EPS(Request $request)
    {
        $eps = Category::findOrFail($request->id);
        if ($request->type == 'up') {
            $before_eps = Category::where('account_id', $eps->account_id)->where('parent_id', $eps->parent_id)->where('eps_order', '<', $eps->eps_order)->orderBy('eps_order', 'desc')->first();
        } elseif ($request->type == 'down') {
            $before_eps = Category::where('account_id', $eps->account_id)->where('parent_id', $eps->parent_id)->where('eps_order', '>', $eps->eps_order)->orderBy('eps_order')->first();
        }
        $y = $before_eps->eps_order;
        $before_eps->eps_order = $eps->eps_order;
        $before_eps->save();
        $eps->eps_order = $y;
        $eps->save();

        return $this->sendResponse(null, 'success');

    }

    public function rename_EPS(Request $request)
    {
        $eps = Category::findOrFail($request->category_id);
        $eps->name = $request->Name;
        $eps->save();

        return redirect('/account/EPS')->with('success', 'EPS Renamed successfully.');

    }

    // ///////////////////////////////////////////////////
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

        $EPS = Category::whereNotIn('name', ['Recycle Bin', 'Archive'])->where('account_id', auth()->user()->current_account_id)->where('parent_id', null)->orderBy('eps_order')->with('allChildren')->get();
        $route = 'store_project';

        return view('account_dashboard.projects.create', compact('roles', 'EPS', 'route'));
    }

    public function store_project(Request $request)
    {
        $result = $this->projectService->createProject($request);

        if (! $result['success']) {
            return redirect()->back()->withInput()->withErrors($result['errors']);
        }

        return redirect('/account')->with('success', 'Project created successfully.');
    }

    public function send_invitation(Request $request)
    {
        // dd($request->all());
        if ($request->emails && count($request->emails) > 0) {
            foreach ($request->emails as $email) {

                $user = User::where('email', $email)->first();
                do {
                    $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
                } while (Invitation::where('code', $invitation_code)->exists());
                // $permissions=['send_invitations',
                //               'create_projects','edit_projects','delete_projects',
                //               'show_eps','create_eps','edit_eps','delete_eps',
                //               'show_contract_tags','create_contract_tags','edit_contract_tags','delete_contract_tags',
                //               'show_project_folder','create_project_folder','edit_project_folder','delete_project_folder',
                //               'show_document_type','create_document_type','edit_document_type','delete_document_type',
                //               'show_contract_settings','create_contract_settings','edit_contract_settings','delete_contract_settings',
                //               'show_users','assign_users','delete_users','edit_users_permissions'];
                $permissions = ['show_users', 'edit_projects'];
                if (! $user) {
                    do {
                        $code2 = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
                    } while (User::where('code', $code2)->exists());
                    $user = User::create(['email' => $email, 'code' => $code2]);
                    $user_role = Role::where('name', 'User')->first();
                    $user->assignRole([$user_role->id]);
                    $existedUserInAccount = false;
                } else {
                    $x = AccountUser::where('user_id', $user->id)->where('account_id', auth()->user()->current_account_id)->first();
                    if ($x) {
                        $existedUserInAccount = true;
                    } else {
                        $existedUserInAccount = false;
                    }

                }
                if ($existedUserInAccount == false) {

                    $invitation = Invitation::create(['code' => $invitation_code, 'account_id' => auth()->user()->current_account_id, 'email' => $email, 'user_id' => $user->id, 'sender_id' => auth()->user()->id]);

                    AccountUser::create(['user_id' => $user->id, 'account_id' => auth()->user()->current_account_id, 'role' => 'User', 'permissions' => json_encode($permissions)]);
                    $account = Account::find(auth()->user()->current_account_id);
                    Mail::to($email)->send(new SendInvitation($invitation, $email));
                }

            }

            return redirect('/account')->with('success', 'Invitations sent successfully.');
        } else {
            return redirect('/account')->with('error', 'No emails were entered to send invitations.');
        }

    }

    // //////////////////////////
    public function account_users()
    {
        $account = Account::findOrFail(auth()->user()->current_account_id);
        $users = $account->users;

        return view('account_dashboard.users.index', compact('users', 'account'));
    }

    public function edit_user($id)
    {
        $user = User::where('code', $id)->first();
        $account = Account::findOrFail(auth()->user()->current_account_id);

        return view('account_dashboard.users.edit', compact('user', 'account'));

    }

    public function update_user(Request $request, $id)
    {
        // dd($request->all());
        $user = User::where('code', $id)->first();
        if ($request->account_permissions) {
            AccountUser::where('user_id', $user->id)->where('account_id', auth()->user()->current_account_id)->update(['role' => $request->role, 'permissions' => json_encode($request->account_permissions)]);
        } else {
            AccountUser::where('user_id', $user->id)->where('account_id', auth()->user()->current_account_id)->update(['role' => $request->role, 'permissions' => json_encode([])]);
        }
        if ($request->projects_permissions && count($request->projects_permissions) > 0) {
            $IDs = $user->assign_projects()->where('projects.account_id', auth()->user()->current_account_id)->pluck('projects.id')->toArray();
            // dd($IDs);
            foreach ($request->projects_permissions as $key => $project_permissions) {
                ProjectUser::where('user_id', $user->id)->where('project_id', $key)->update(['permissions' => json_encode($project_permissions)]);
                $IDs = array_values(array_diff($IDs, [$key]));
            }
            ProjectUser::where('user_id', $user->id)->whereIn('project_id', $IDs)->update(['permissions' => json_encode([])]);
        }

        return redirect('/account/users')->with('success', 'User Permissions Updated successfully.');
    }

    public function delete_user($id)
    {
        $user = User::where('code', $id)->first();
        ProjectUser::where('user_id', $user->id)->where('account_id', auth()->user()->current_account_id)->delete();
        AccountUser::where('user_id', $user->id)->where('account_id',auth()->user()->current_account_id)->delete();

        return redirect('/account/users')->with('success', 'User deleted from account successfully.');

    }
}
