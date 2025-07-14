<?php
namespace App\Http\Controllers\Dashboard\settings;

use App\Http\Controllers\ApiController;
use App\Models\DocType;
use App\Models\Project;
use Illuminate\Http\Request;

class DocumentTypeController extends ApiController
{
    public function index(Request $request)
    {
        $user           = auth()->user();
        $document_types = DocType::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->orderBy('order', 'asc')->get();
        if (auth()->user()->current_account_id == null) {
            return view('dashboard.document_types.index', compact('document_types'));
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return view('account_dashboard.document_types.index', compact('document_types'));
        } else {
            return view('project_dashboard.document_types.index', compact('document_types'));
        }

    }

    public function create()
    {
        if (auth()->user()->current_account_id == null) {
            return view('dashboard.document_types.create');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return view('account_dashboard.document_types.create');
        } else {
            $project       = Project::findOrFail(auth()->user()->current_project_id);
            $stake_holders = $project->stakeHolders;
            return view('project_dashboard.document_types.create', compact('stake_holders'));
        }
    }

    public function store(Request $request)
    {
        $type = DocType::create(['project_id' => auth()->user()->current_project_id,
            'account_id'                          => auth()->user()->current_account_id,
            'name'                                => $request->name,
            'description'                         => $request->description,
            'from'                                => $request->from,
            'to'                                  => $request->to,
            'relevant_word'                       => $request->relevant_word,
            'order'                               => $request->order ? intval($request->order) : 0]);
        if ($request->shortcut) {
            $type->shortcut = '1';
        }
        $type->save();
        if (auth()->user()->current_account_id == null) {
            return redirect('/accounts/document-types')->with('success', 'Document Type created successfully.');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return redirect('/account/document-types')->with('success', 'Document Type created successfully.');
        } else {
            return redirect('/project/document-types')->with('success', 'Document Type created successfully.');
        }

    }

    public function edit($id)
    {
        $document_type = DocType::findOrFail($id);
        if (auth()->user()->current_account_id == null) {
            return view('dashboard.document_types.edit', compact('document_type'));
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return view('account_dashboard.document_types.edit', compact('document_type'));
        } else {
            $project       = Project::findOrFail(auth()->user()->current_project_id);
            $stake_holders = $project->stakeHolders;
            return view('project_dashboard.document_types.edit', compact('document_type', 'stake_holders'));
        }
    }

    public function update(Request $request, $id)
    {
        DocType::where('id', $id)->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'from'          => $request->from,
            'to'            => $request->to,
            'relevant_word' => $request->relevant_word,
            'order'         => $request->order ? intval($request->order) : 0]);
        $type = DocType::findOrFail($id);
        if (! $request->shortcut) {
            $type->shortcut = '0';
        } else {
            $type->shortcut = '1';
        }
        $type->save();
        if (auth()->user()->current_account_id == null) {
            return redirect('/accounts/document-types')->with('success', 'Document Type updated successfully.');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return redirect('/account/document-types')->with('success', 'Document Type updated successfully.');
        } else {
            return redirect('/project/document-types')->with('success', 'Document Type updated successfully.');
        }
    }

    public function delete($id)
    {
        DocType::where('id', $id)->delete();
        if (auth()->user()->current_account_id == null) {
            return redirect('/accounts/document-types')->with('success', 'Document Type deleted successfully.');
        } elseif (auth()->user()->current_account_id != null && auth()->user()->current_project_id == null) {
            return redirect('/account/document-types')->with('success', 'Document Type deleted successfully.');
        } else {
            return redirect('/project/document-types')->with('success', 'Document Type deleted successfully.');
        }
    }
}
