@extends('account_dashboard.layout.app')
@section('title', 'Admin Account Home - Update User')
@section('content')
    <style>
        .custom-fieldset {
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 8px;

            width: 100%;
            background-color: #fefefe;
            position: relative;
        }

        .custom-legend {
            font-weight: bold;
            font-size: 1.2rem;
            padding: 0 10px;
            color: #333;
            width: auto;
            max-width: 100%;
        }
    </style>
    <h2 class="page-title">Update User Permissions</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('account.update-user', $user->code) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="simpleinputName">Name</label>
                            <input type="text" readonly value="{{ $user->name }}" id="simpleinputName"
                                class="form-control" placeholder="Name">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-email">Email</label>
                            <input type="email" readonly value="{{ $user->email }}" id="example-email"
                                class="form-control" placeholder="Email">

                        </div>
                        <div class="form-group mb-3">
                            <label for="phone">Phone Num.</label>
                            <input type="text" readonly value="{{ $user->phone }}" id="phone" class="form-control"
                                placeholder="Phone Number">
                        </div>
                        <div class="form-group mb-3">
                            <label for="phone">Role</label>
                           
                                <select class="form-control" name="role">
                                    <option value="User" @if($user
                                    ->accounts()
                                    ->where('account_id', auth()->user()->current_account_id)
                                    ->first()->pivot->role == 'User') selected @endif>User</option>
                                    <option value="Admin Account"@if($user
                                    ->accounts()
                                    ->where('account_id', auth()->user()->current_account_id)
                                    ->first()->pivot->role == 'Admin Account') selected @endif>Account Admin</option>

                                </select>
                        </div>
                        <div class="form-group" style="display: flex; align-items: center;margin-bottom:0px;">
                            <h5 style="margin-right: 10px;">Account Permissions</h5>
                            <hr style="flex: 1; margin: 0;">
                        </div>
                        @php
                            $account_permission = $user
                                ->accounts()
                                ->where('account_id', auth()->user()->current_account_id)
                                ->first()->pivot->permissions;
                            $account_permission = json_decode($account_permission);
                        @endphp
                        <div class="row" style="margin-left: 50px;">
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="show_users"id="show_users" @if (in_array('show_users', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="show_users">Show Users</label>
                            </div>
                            <div class="custom-control custom-checkbox"style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="assign_users" id="assign_users"@if (in_array('assign_users', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="assign_users">Assign Users To Projects</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="edit_user_permissions"id="edit_user_permissions"
                                    @if (in_array('edit_user_permissions', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="edit_user_permissions">Edit Users
                                    Permissions</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="delete_users"id="delete_users" @if (in_array('delete_users', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="delete_users">Delete Users From Accounts</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="send_invitations"id="send_invitations"
                                    @if (in_array('send_invitations', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="send_invitations">Send User Invitations</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="create_projects"id="create_projects"
                                    @if (in_array('create_projects', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="create_projects">Create Projects</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="edit_projects"id="edit_projects"
                                    @if (in_array('edit_projects', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="edit_projects">Edit Projects</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="delete_projects"id="delete_projects"
                                    @if (in_array('delete_projects', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="delete_projects">Delete Projects</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="show_eps"id="show_eps" @if (in_array('show_eps', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="show_eps">Show EPSs</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="create_eps"id="create_eps" @if (in_array('create_eps', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="create_eps">Create EPSs</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="edit_eps"id="edit_eps" @if (in_array('edit_eps', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="edit_eps">Edit EPSs</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="delete_eps"id="delete_eps" @if (in_array('delete_eps', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="delete_eps">Delete EPSs</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="show_contract_tags"id="show_contract_tags"
                                    @if (in_array('show_contract_tags', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="show_contract_tags">Show Contract Tags</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="create_contract_tags"id="create_contract_tags"
                                    @if (in_array('create_contract_tags', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="create_contract_tags">Create Contract
                                    Tags</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="edit_contract_tags"id="edit_contract_tags"
                                    @if (in_array('edit_contract_tags', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="edit_contract_tags">Edit Contract Tags</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="delete_contract_tags"id="delete_contract_tags"
                                    @if (in_array('delete_contract_tags', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="delete_contract_tags">Delete Contract
                                    Tags</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="show_project_folder"id="show_project_folder"
                                    @if (in_array('show_project_folder', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="show_project_folder">Show Project Folder</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="create_project_folder"id="create_project_folder"
                                    @if (in_array('create_project_folder', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="create_project_folder">Create Project
                                    Folder</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="edit_project_folder"id="edit_project_folder"
                                    @if (in_array('edit_project_folder', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="edit_project_folder">Edit Project Folder</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="delete_project_folder"id="delete_project_folder"
                                    @if (in_array('delete_project_folder', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="delete_project_folder">Delete Project
                                    Folder</label>
                            </div>

                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="show_document_type"id="show_document_type"
                                    @if (in_array('show_document_type', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="show_document_type">Show Document Type</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="create_document_type"id="create_document_type"
                                    @if (in_array('create_document_type', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="create_document_type">Create Document
                                    Type</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="edit_document_type"id="edit_document_type"
                                    @if (in_array('edit_document_type', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="edit_document_type">Edit Document Type</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="delete_document_type"id="delete_document_type"
                                    @if (in_array('delete_document_type', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="delete_document_type">Delete Document
                                    Type</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="show_contract_settings"id="show_contract_settings"
                                    @if (in_array('show_contract_settings', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="show_contract_settings">Show Contract
                                    Settings</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="create_contract_settings"id="create_contract_settings"
                                    @if (in_array('create_contract_settings', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="create_contract_settings">Create Contract
                                    Settings</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="edit_contract_settings"id="edit_contract_settings"
                                    @if (in_array('edit_contract_settings', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="edit_contract_settings">Edit Contract
                                    Settings</label>
                            </div>
                            <div class="custom-control custom-checkbox" style="width:25%">

                                <input type="checkbox" class="custom-control-input" name="account_permissions[]"
                                    value="delete_contract_settings"id="delete_contract_settings"
                                    @if (in_array('delete_contract_settings', $account_permission ?? [])) checked @endif>
                                <label class="custom-control-label" for="delete_contract_settings">Delete Contract
                                    Settings</label>
                            </div>

                        </div>
                        <div class="form-group"
                            style="display: flex; align-items: center;margin-bottom:0px;margin-top:20px;">
                            <h5 style="margin-right: 10px;">Projects Permissions</h5>
                            <hr style="flex: 1; margin: 0;">
                        </div>
                        <div class="form-group">
                            @foreach ($user->assign_projects()->where('projects.account_id', auth()->user()->current_account_id)->get() as $project)
                                @php
                                    $project_permission = $user
                                        ->assign_projects()
                                        ->where('project_id', $project->id)
                                        ->first()->pivot->permissions;

                                    $project_permission = json_decode($project_permission);
                                @endphp
                                <fieldset class="custom-fieldset">
                                    <legend class="custom-legend">{{ $project->name }} - {{ $project->code }}</legend>
                                    <div class="row" style="margin-left: 50px;">
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="show_contract_tags"id="show_contract_tags{{ $project->id }}"
                                                @if (in_array('show_contract_tags', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="show_contract_tags{{ $project->id }}">Show Contract
                                                Tags</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="create_contract_tags"id="create_contract_tags{{ $project->id }}"
                                                @if (in_array('create_contract_tags', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="create_contract_tags{{ $project->id }}">Create Contract
                                                Tags</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="edit_contract_tags"id="edit_contract_tags{{ $project->id }}"
                                                @if (in_array('edit_contract_tags', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="edit_contract_tags{{ $project->id }}">Edit Contract
                                                Tags</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="delete_contract_tags"id="delete_contract_tags{{ $project->id }}"
                                                @if (in_array('delete_contract_tags', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="delete_contract_tags{{ $project->id }}">Delete Contract
                                                Tags</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="show_project_folder"id="show_project_folder{{ $project->id }}"
                                                @if (in_array('show_project_folder', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="show_project_folder{{ $project->id }}">Show Project
                                                Folders</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="create_project_folder"id="create_project_folder{{ $project->id }}"
                                                @if (in_array('create_project_folder', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="create_project_folder{{ $project->id }}">Create Project
                                                Folders</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="edit_project_folder"id="edit_project_folder{{ $project->id }}"
                                                @if (in_array('edit_project_folder', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="edit_project_folder{{ $project->id }}">Edit Project
                                                Folders</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="delete_project_folder"id="delete_project_folder{{ $project->id }}"
                                                @if (in_array('delete_project_folder', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="delete_project_folder{{ $project->id }}">Delete Project
                                                Folders</label>
                                        </div>

                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="show_document_type"id="show_document_type{{ $project->id }}"
                                                @if (in_array('show_document_type', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="show_document_type{{ $project->id }}">Show Document
                                                Types</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="create_document_type"id="create_document_type{{ $project->id }}"
                                                @if (in_array('create_document_type', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="create_document_type{{ $project->id }}">Create Document
                                                Types</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="edit_document_type"id="edit_document_type{{ $project->id }}"
                                                @if (in_array('edit_document_type', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="edit_document_type{{ $project->id }}">Edit Document
                                                Types</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="delete_document_type"id="delete_document_type{{ $project->id }}"
                                                @if (in_array('delete_document_type', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="delete_document_type{{ $project->id }}">Delete Document
                                                Types</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="show_contract_settings"id="show_contract_settings{{ $project->id }}"
                                                @if (in_array('show_contract_settings', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="show_contract_settings{{ $project->id }}">Show Contract
                                                Settings</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="create_contract_settings"id="create_contract_settings{{ $project->id }}"
                                                @if (in_array('create_contract_settings', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="create_contract_settings{{ $project->id }}">Create
                                                Contract
                                                Settings</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="edit_contract_settings"id="edit_contract_settings{{ $project->id }}"
                                                @if (in_array('edit_contract_settings', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="edit_contract_settings{{ $project->id }}">Edit Contract
                                                Settings</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="delete_contract_settings"id="delete_contract_settings{{ $project->id }}"
                                                @if (in_array('delete_contract_settings', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="delete_contract_settings{{ $project->id }}">Delete
                                                Contract
                                                Settings</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="upload_documents"id="upload_documents{{ $project->id }}"
                                                @if (in_array('upload_documents', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="upload_documents{{ $project->id }}">Upload Single
                                                Document</label>
                                        </div>
                                        <div class="custom-control custom-checkbox"style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="upload_group_documents"
                                                id="upload_group_documents{{ $project->id }}"@if (in_array('upload_group_documents', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="upload_group_documents{{ $project->id }}">Upload Group
                                                Of Documents</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="import_documents"id="import_documents{{ $project->id }}"
                                                @if (in_array('import_documents', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="import_documents{{ $project->id }}">Import Documents
                                                From Excel Sheet</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="edit_documents"id="edit_documents{{ $project->id }}"
                                                @if (in_array('edit_documents', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="edit_documents{{ $project->id }}">Edit
                                                Documents</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="delete_documents"id="delete_documents{{ $project->id }}"
                                                @if (in_array('delete_documents', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="delete_documents{{ $project->id }}">Delete
                                                Documents</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="analysis"id="analysis{{ $project->id }}"
                                                @if (in_array('analysis', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="analysis{{ $project->id }}">Analysis Documents</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="create_file"id="create_file{{ $project->id }}"
                                                @if (in_array('create_file', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="create_file{{ $project->id }}">Create Files</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="edit_file"id="edit_file{{ $project->id }}"
                                                @if (in_array('edit_file', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="edit_file{{ $project->id }}">Edit Files</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="delete_file"id="delete_file{{ $project->id }}"
                                                @if (in_array('delete_file', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="delete_file{{ $project->id }}">Delete Files</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="cope_move_file"id="cope_move_file{{ $project->id }}"
                                                @if (in_array('cope_move_file', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="cope_move_file{{ $project->id }}">Copy And Move
                                                Files</label>
                                        </div>

                                          <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="show_abbreviations"id="show_abbreviations{{ $project->id }}"
                                                @if (in_array('show_abbreviations', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="show_abbreviations{{ $project->id }}">Show Abbreviations</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="create_abbreviation"id="create_abbreviation{{ $project->id }}"
                                                @if (in_array('create_abbreviation', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="create_abbreviation{{ $project->id }}">Create Abbreviations</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="edit_abbreviation"id="edit_abbreviation{{ $project->id }}"
                                                @if (in_array('edit_abbreviation', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="edit_abbreviation{{ $project->id }}">Edit Abbreviations</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="delete_abbreviation"id="delete_abbreviation{{ $project->id }}"
                                                @if (in_array('delete_abbreviation', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="delete_abbreviation{{ $project->id }}">Delete Abbreviations</label>
                                        </div>

                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="show_project_contacts"id="show_project_contacts{{ $project->id }}"
                                                @if (in_array('show_project_contacts', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="show_project_contacts{{ $project->id }}">Show Project Contacts</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="create_project_contact"id="create_project_contact{{ $project->id }}"
                                                @if (in_array('create_project_contact', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="create_project_contact{{ $project->id }}">Create Project Contact</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="edit_project_contact"id="edit_project_contact{{ $project->id }}"
                                                @if (in_array('edit_project_contact', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="edit_project_contact{{ $project->id }}">Edit Project Contact</label>
                                        </div>
                                        <div class="custom-control custom-checkbox" style="width:25%">

                                            <input type="checkbox" class="custom-control-input"
                                                name="projects_permissions[{{ $project->id }}][]"
                                                value="delete_project_contact"id="delete_project_contact{{ $project->id }}"
                                                @if (in_array('delete_project_contact', $project_permission ?? [])) checked @endif>
                                            <label class="custom-control-label" for="delete_project_contact{{ $project->id }}">Delete Project Contact</label>
                                        </div>




                                    </div>

                                </fieldset>
                            @endforeach

                        </div>






                        <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary"
                            style="margin-top: 10px;">Update Permissions</button>
                    </form>
                </div> <!-- /.col -->

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
        });
    </script>
@endpush
