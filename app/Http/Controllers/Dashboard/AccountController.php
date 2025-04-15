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
use App\Models\ContractTag;
use App\Models\DocType;
use App\Models\ProjectFolder;
use App\Models\ContractSetting;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AccountController extends ApiController
{
    public function index(Request $request)
    {
        $user=auth()->user();
        $user->current_account_id=null;
        $user->current_project_id=null;
        $user->save();
        $all_accounts = Account::orderBy('id', 'desc');


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
        $search = $request->search;
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
             'name' => $request->name,
             'email' => $request->email,
             'phone_no' => $request->country_code . $request->phone,
             'security_question' => $request->security_question,
             'security_answer' => $request->security_answer,
             'recovery_email' => $request->recovery_email,
             'recovery_phone_no' => $request->recovery_country_code . $request->recovery_phone

         ]);

        if(!$request->active){
            $account->active='0';
            $account->save();
        }
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
        Category::create(['code' => $process_last_number,'name' => 'Projects folder','account_id' => $account->id]);
        $last_Category2 = Category::orderBy('id', 'desc')->first();

        $last_number2 = 0;
        if ($last_Category2 && preg_match('/EPS-(\d+)$/', $last_Category2->code, $matches2)) {
            $last_number2 = intval($matches2[1]); // Extract the last numeric part safely
        }

        do {
            $process_last_number2 = 'EPS-' . sprintf('%06d', $last_number2 + 1);
            $EPSExists2 = Category::where('code', $process_last_number2)->exists();
            $last_number2++;
        } while ($EPSExists2);
        Category::create(['code' => $process_last_number2,'name' => 'Archive','account_id' => $account->id]);
        $last_Category3 = Category::orderBy('id', 'desc')->first();

        $last_number3 = 0;
        if ($last_Category3 && preg_match('/EPS-(\d+)$/', $last_Category3->code, $matches3)) {
            $last_number3 = intval($matches3[1]); // Extract the last numeric part safely
        }

        do {
            $process_last_number3 = 'EPS-' . sprintf('%06d', $last_number3 + 1);
            $EPSExists3 = Category::where('code', $process_last_number3)->exists();
            $last_number3++;
        } while ($EPSExists3);
        Category::create(['code' => $process_last_number3,'name' => 'Recycle Bin','account_id' => $account->id]);
        if($request->file('logo')){
            $logo=getFirstMediaUrl($account,$account->logoCollection);
            if($logo!= null){
                deleteMedia($account,$account->logoCollection);
            }
            uploadMedia($request->logo,$account->logoCollection,$account);
        }

        $all_project_folders=ProjectFolder::where('project_id',null)->where('account_id',null)->get();
        foreach($all_project_folders as $folder){
            ProjectFolder::create(['account_id'=>$account->id,'name'=>$folder->name,'order'=>$folder->order,'ladel3'=>$folder->ladel3,
                            'ladel2'=>$folder->ladel2,
                            'ladel1'=>$folder->ladel1,'potential_impact'=>$folder->potential_impact,'shortcut'=>$folder->shortcut]);
        }
        $all_DocTypes=DocType::where('project_id',null)->where('account_id',null)->get();
        foreach($all_DocTypes as $DocType){
            DocType::create(['account_id'=>$account->id,'name'=>$DocType->name,'order'=>$DocType->order,'description'=>$DocType->description]);
        }
        $all_ContractTags=ContractTag::where('project_id',null)->where('account_id',null)->get();
        foreach($all_ContractTags as $ContractTag){
            ContractTag::create(['account_id'=>$account->id,'name'=>$ContractTag->name,'order'=>$ContractTag->order,
                                'description'=>$ContractTag->description,'is_notice'=>$ContractTag->is_notice,
                                'sub_clause'=>$ContractTag->sub_clause,'for_letter'=>$ContractTag->for_letter,'var_process'=>$ContractTag->var_process]);
        }
        $all_ContractSettings=ContractSetting::where('project_id',null)->where('account_id',null)->get();
        foreach($all_ContractSettings as $ContractSetting){
            ContractSetting::create(['account_id'=>$account->id,'name'=>$ContractSetting->name,'order'=>$ContractSetting->order,'type'=>$ContractSetting->type]);
        }
        return redirect('/accounts')->with('success', 'Account created successfully.');

    }

    // public function edit($id){
    //     $mark=CarMark::where('id',$id)->first();
    //     return view('dashboard.car_marks.edit',compact('mark'));
    // }

    // public function update(Request $request,$id){
    //     $validator = Validator::make($request->all(), [
    //         'en_name' => ['required', 'string', 'max:191'],
    //         'ar_name' => ['required', 'string', 'max:191'],

    //     ]);


    //     if ($validator->fails()) {
    //         return Redirect::back()->withInput()->withErrors($validator);
    //     }

    //     CarMark::where('id',$id)->update([ 'en_name' => $request->en_name,
    //             'ar_name' => $request->ar_name
    //         ]);
    //     return redirect('/admin-dashboard/car-marks')->with('success', 'Car Mark updated successfully.');

    // }

    // public function delete($id){
    //     $mark = CarMark::findOrFail($id);
    //     CarModel::where('car_mark_id',$id)->delete();
    //     // If no employees are assigned to the department, proceed with deleting the department
    //     $mark->delete();

    //     return redirect('/admin-dashboard/car-marks')->with('success', 'Car Mark deleted successfully.');
    // }
}
