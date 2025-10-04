<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Account;
use App\Models\AccountUser;
use App\Models\Category;
use App\Models\ContractSetting;
use App\Models\ContractTag;
use App\Models\DocType;
use App\Models\ExportFormate;
use App\Models\Project;
use App\Models\ProjectFolder;
use App\Models\ProjectUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AccountController extends ApiController
{
    public function index(Request $request)
    {
        $user                     = auth()->user();
        $user->current_account_id = null;
        $user->current_project_id = null;
        $user->save();
        if (auth()->user()->roles->first()->name == 'Super Admin') {
            $all_accounts = Account::orderBy('id', 'desc');
        } else {
            $all_accounts = $user->accounts()->orderBy('id');
        }

        if ($request->has('search') && $request->search != null) {
            $all_accounts->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('phone_no', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('recovery_email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('recovery_phone_no', 'LIKE', '%' . $request->search . '%');
            });
        }
        $all_accounts = $all_accounts->paginate(12);
        $search       = $request->search;

        return view('dashboard.home', compact('all_accounts', 'search'));

    }

    public function create()
    {
        return view('dashboard.accounts.create');
    }

    public function store(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     'en_name' => ['required', 'string', 'max:191'],
        //     'ar_name' => ['required', 'string', 'max:191'],

        // ]);

        // if ($validator->fails()) {
        //     return Redirect::back()->withInput()->withErrors($validator);
        // }

        $account = Account::create([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'country_code'          => $request->country_code,
            'phone_no'              => $request->phone,
            'security_question'     => $request->security_question,
            'security_answer'       => $request->security_answer,
            'recovery_email'        => $request->recovery_email,
            'recovery_country_code' => $request->recovery_country_code,
            'recovery_phone_no'     => $request->recovery_phone,

        ]);

        if (! $request->active) {
            $account->active = '0';
            $account->save();
        }
        $last_Category = Category::orderBy('id', 'desc')->first();

        $last_number = 0;
        if ($last_Category && preg_match('/EPS-(\d+)$/', $last_Category->code, $matches)) {
            $last_number = intval($matches[1]); // Extract the last numeric part safely
        }

        do {
            $process_last_number = 'EPS-' . sprintf('%06d', $last_number + 1);
            $EPSExists           = Category::where('code', $process_last_number)->exists();
            $last_number++;
        } while ($EPSExists);
        Category::create(['code' => $process_last_number, 'name' => 'Projects folder', 'eps_order' => 1, 'account_id' => $account->id]);
        $last_Category2 = Category::orderBy('id', 'desc')->first();

        $last_number2 = 0;
        if ($last_Category2 && preg_match('/EPS-(\d+)$/', $last_Category2->code, $matches2)) {
            $last_number2 = intval($matches2[1]); // Extract the last numeric part safely
        }

        do {
            $process_last_number2 = 'EPS-' . sprintf('%06d', $last_number2 + 1);
            $EPSExists2           = Category::where('code', $process_last_number2)->exists();
            $last_number2++;
        } while ($EPSExists2);
        Category::create(['code' => $process_last_number2, 'name' => 'Archive', 'eps_order' => 2, 'account_id' => $account->id]);
        $last_Category3 = Category::orderBy('id', 'desc')->first();

        $last_number3 = 0;
        if ($last_Category3 && preg_match('/EPS-(\d+)$/', $last_Category3->code, $matches3)) {
            $last_number3 = intval($matches3[1]); // Extract the last numeric part safely
        }

        do {
            $process_last_number3 = 'EPS-' . sprintf('%06d', $last_number3 + 1);
            $EPSExists3           = Category::where('code', $process_last_number3)->exists();
            $last_number3++;
        } while ($EPSExists3);
        Category::create(['code' => $process_last_number3, 'name' => 'Recycle Bin', 'eps_order' => 3, 'account_id' => $account->id]);
        if ($request->file('logo')) {
            $logo = getFirstMediaUrl($account, $account->logoCollection);
            if ($logo != null) {
                deleteMedia($account, $account->logoCollection);
            }
            uploadMedia($request->logo, $account->logoCollection, $account);
        }

        $all_project_folders = ProjectFolder::where('project_id', null)->where('account_id', null)->get();
        foreach ($all_project_folders as $folder) {
            ProjectFolder::create(['account_id' => $account->id, 'name'                => $folder->name, 'order'                => $folder->order, 'ladel3' => $folder->ladel3,
                'ladel2'                            => $folder->ladel2,
                'ladel1'                            => $folder->ladel1, 'potential_impact' => $folder->potential_impact, 'shortcut' => $folder->shortcut]);
        }
        $all_DocTypes = DocType::where('project_id', null)->where('account_id', null)->get();
        foreach ($all_DocTypes as $DocType) {
            DocType::create(['account_id' => $account->id, 'name' => $DocType->name, 'order' => $DocType->order, 'description' => $DocType->description, 'relevant_word' => $DocType->relevant_word, 'shortcut' => $DocType->shortcut]);
        }
        $all_ContractTags = ContractTag::where('project_id', null)->where('account_id', null)->get();
        foreach ($all_ContractTags as $ContractTag) {
            ContractTag::create(['account_id' => $account->id, 'name'                   => $ContractTag->name, 'order'             => $ContractTag->order,
                'description'                     => $ContractTag->description, 'is_notice' => $ContractTag->is_notice,
                'sub_clause'                      => $ContractTag->sub_clause, 'for_letter' => $ContractTag->for_letter, 'var_process' => $ContractTag->var_process]);
        }
        $all_ContractSettings = ContractSetting::where('project_id', null)->where('account_id', null)->get();
        foreach ($all_ContractSettings as $ContractSetting) {
            ContractSetting::create(['account_id' => $account->id, 'name' => $ContractSetting->name, 'order' => $ContractSetting->order, 'type' => $ContractSetting->type]);
        }

        $export_formate = ExportFormate::where('project_id', null)->where('account_id', null)->first();
        ExportFormate::create(['account_id' => $account->id, 'value' => $export_formate->value]);

        return redirect('/accounts')->with('success', 'Account created successfully.');

    }

    public function edit()
    {
        $id      = session('current_edit_account');
        $account = Account::where('id', $id)->first();

        return view('dashboard.accounts.edit', compact('account'));
    }

    public function update(Request $request)
    {
        $id = session('current_edit_account');
        Account::where('id', $id)->update([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'country_code'          => $request->country_code,
            'phone_no'              => $request->phone,
            'security_question'     => $request->security_question,
            'security_answer'       => $request->security_answer,
            'recovery_email'        => $request->recovery_email,
            'recovery_country_code' => $request->recovery_country_code,
            'recovery_phone_no'     => $request->recovery_phone,

        ]);
        $account = Account::find($id);
        if ($request->file('logo')) {
            $logo = getFirstMediaUrl($account, $account->logoCollection);
            if ($logo != null) {
                deleteMedia($account, $account->logoCollection);
            }
            uploadMedia($request->logo, $account->logoCollection, $account);
        }
        session()->forget('current_edit_account');

        return redirect('/accounts')->with('success', 'Account updated successfully.');

    }

    public function delete($id)
    {
        $account = Account::findOrFail($id);
        Category::where('account_id', $account->id)->delete();
        ContractSetting::where('account_id', $account->id)->delete();
        ContractTag::where('account_id', $account->id)->delete();
        DocType::where('account_id', $account->id)->delete();
        Project::where('account_id', $account->id)->delete();
        ProjectFolder::where('account_id', $account->id)->delete();
        AccountUser::where('account_id', $account->id)->delete();
        ProjectUser::where('account_id', $account->id)->delete();
        // If no employees are assigned to the department, proceed with deleting the department
        $account->delete();

        return redirect('/accounts')->with('success', 'Account deleted successfully.');
    }
}
