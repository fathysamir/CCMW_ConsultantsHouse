<?php

namespace App\Http\Controllers\Dashboard\settings;

use App\Http\Controllers\ApiController;
use App\Models\ContractTag;
use Illuminate\Http\Request;

class ContractTagController extends ApiController
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $contract_tags = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->orderBy('order', 'asc')->get();
        if (auth()->user()->current_account_id == null) {
            return view('dashboard.contract_tags.index', compact('contract_tags'));
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return view('account_dashboard.contract_tags.index', compact('contract_tags'));
        } else {
            return view('project_dashboard.contract_tags.index', compact('contract_tags'));
        }

    }

    public function create()
    {
        if (auth()->user()->current_account_id == null) {
            return view('dashboard.contract_tags.create');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return view('account_dashboard.contract_tags.create');
        } else {
            return view('project_dashboard.contract_tags.create');
        }
    }

    public function store(Request $request)
    {
        ContractTag::create(['project_id' => auth()->user()->current_project_id,
            'account_id' => auth()->user()->current_account_id,
            'name' => $request->name,
            'description' => $request->description,
            'sub_clause' => $request->sub_clause,
            'var_process' => intval($request->var_process),
            'is_notice' => $request->is_notice ? '1' : '0',
            'for_letter' => $request->for_letter ? '1' : '0',
            'order' => $request->order ? intval($request->order) : 0]);

        if (auth()->user()->current_account_id == null) {
            return redirect('/accounts/contract-tags')->with('success', 'Contract Tag created successfully.');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return redirect('/account/contract-tags')->with('success', 'Contract Tag created successfully.');
        } else {
            return redirect('/project/contract-tags')->with('success', 'Contract Tag created successfully.');
        }

    }

    public function edit($id)
    {
        $contract_tag = ContractTag::findOrFail($id);
        if (auth()->user()->current_account_id == null) {
            return view('dashboard.contract_tags.edit', compact('contract_tag'));
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return view('account_dashboard.contract_tags.edit', compact('contract_tag'));
        } else {
            return view('project_dashboard.contract_tags.edit', compact('contract_tag'));
        }
    }

    public function update(Request $request, $id)
    {
        ContractTag::where('id', $id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'sub_clause' => $request->sub_clause,
            'var_process' => intval($request->var_process),
            'is_notice' => $request->is_notice ? '1' : '0',
            'for_letter' => $request->for_letter ? '1' : '0',
            'order' => $request->order ? intval($request->order) : 0]);

        if (auth()->user()->current_account_id == null) {
            return redirect('/accounts/contract-tags')->with('success', 'Contract Tag updated successfully.');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return redirect('/account/contract-tags')->with('success', 'Contract Tag updated successfully.');
        } else {
            return redirect('/project/contract-tags')->with('success', 'Contract Tag updated successfully.');
        }
    }

    public function delete($id)
    {
        ContractTag::where('id', $id)->delete();
        if (auth()->user()->current_account_id == null) {
            return redirect('/accounts/contract-tags')->with('success', 'Contract Tag deleted successfully.');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return redirect('/account/contract-tags')->with('success', 'Contract Tag deleted successfully.');
        } else {
            return redirect('/project/contract-tags')->with('success', 'Contract Tag deleted successfully.');
        }
    }
}
