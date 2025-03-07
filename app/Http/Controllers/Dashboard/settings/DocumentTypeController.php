<?php

namespace App\Http\Controllers\Dashboard\settings;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\Account;
use App\Models\Category;
use App\Models\DocType;
use Illuminate\Validation\Rule;

class DocumentTypeController extends ApiController
{
    public function index(Request $request)
    {
        $user=auth()->user();
        $document_types=DocType::where('account_id',$user->current_account_id)->where('project_id',$user->current_project_id)->orderBy('order','asc')->get();
        if(auth()->user()->current_account_id == null){
            return view('dashboard.document_types.index', compact('document_types'));
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return view('account_dashboard.document_types.index', compact('document_types'));
        }else{
            return view('project_dashboard.document_types.index', compact('document_types'));
        }

    }

    public function create()
    {
        if(auth()->user()->current_account_id == null){
            return view('dashboard.document_types.create');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return view('account_dashboard.document_types.create');
        }else{
            return view('project_dashboard.document_types.create');
        }
    }
    public function store(Request $request)
    {
        DocType::create(['project_id'=>auth()->user()->current_project_id,
                            'account_id'=>auth()->user()->current_account_id,
                            'name'=>$request->name,
                            'description'=>$request->description,
                            'order'=>$request->order? intval($request->order) : 0]);
        
        if(auth()->user()->current_account_id == null){
            return redirect('/accounts/document-types')->with('success', 'Document Type created successfully.');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return redirect('/account/document-types')->with('success', 'Document Type created successfully.');
        }else{
            return redirect('/project/document-types')->with('success', 'Document Type created successfully.');
        }

    }
    public function edit($id)
    {
        $document_type=DocType::findOrFail($id);
        if(auth()->user()->current_account_id == null){
            return view('dashboard.document_types.edit',compact('document_type'));
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return view('account_dashboard.document_types.edit',compact('document_type'));
        }else{
            return view('project_dashboard.document_types.edit',compact('document_type'));
        }
    }
    public function update(Request $request,$id)
    {
        DocType::where('id',$id)->update([
                            'name'=>$request->name,
                            'description'=>$request->description,
                            'order'=>$request->order? intval($request->order) : 0]);
        
        if(auth()->user()->current_account_id == null){
            return redirect('/accounts/document-types')->with('success', 'Document Type updated successfully.');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return redirect('/account/document-types')->with('success', 'Document Type updated successfully.');
        }else{
            return redirect('/project/document-types')->with('success', 'Document Type updated successfully.');
        }
    }
    public function delete($id)
    {
        DocType::where('id',$id)->delete();
        if(auth()->user()->current_account_id == null){
            return redirect('/accounts/document-types')->with('success', 'Document Type deleted successfully.');
        }elseif(auth()->user()->current_account_id != null && auth()->user()->current_project_id == null){
            return redirect('/account/document-types')->with('success', 'Document Type deleted successfully.');
        }else{
            return redirect('/project/document-types')->with('success', 'Document Type deleted successfully.');
        }
    }
}
