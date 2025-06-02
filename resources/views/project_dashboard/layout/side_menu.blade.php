<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar style="background-color: black !important;">
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>
    <nav class="vertnav navbar navbar-light">
        <!-- nav bar -->
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ asset(url('/project')) }}">
                <img id="logo" src="{{ asset('dashboard/assets/images/logo.png') }}"
                    @if ($sideBarTheme == '0') width="50" @else width="130" @endif style="border-radius: 50%;border: 6px solid #495057 ;">

            </a>
        </div>




        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item">

                <a class="nav-link pl-3 link_kkkkk" href="{{ route('project.home') }}"style="padding-left: 0.5rem !important;"><img src="{{ asset('/dashboard/assets/selected_images/home.ico') }}" width="22"><span class="ml-1 item-text" style="margin-left: 1rem !important;color:whitesmoke;">
                        Home</span></a>
            </li>
            <li class="nav-item dropdown">
                <a href="#Files" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link link_kkkkk">
                    <img src="{{ asset('/dashboard/assets/selected_images/files.png') }}" width="22">
                    <span class="ml-3 item-text" style="color:whitesmoke;">Files</span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="Files">
                    @forelse($Folders as $folder)
                        <li class="nav-item">
                            <a class="nav-link pl-3 link_kkkkk" href="{{ route('switch.folder', $folder->id) }}">
                                <span class="ml-1 item-text" style="color: #e4d125;">{{ $folder->name }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="nav-item">
                            <a class="nav-link pl-3 link_kkkkk" href="">
                                <span class="ml-1 item-text"style="color: #e4d125;">No folders found</span>
                            </a>
                        </li>
                    @endforelse


                </ul>
            </li>
            <li class="nav-item dropdown">
                @php
                    $project = \App\Models\Project::findOrFail(auth()->user()->current_project_id);
                @endphp
                <a href="#Project" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link link_kkkkk">
                    <img src="{{ asset('/dashboard/assets/selected_images/project.png') }}" width="22">
                    <span class="ml-3 item-text" style="color:whitesmoke;">Project</span>
                </a>

                <ul class="collapse list-unstyled pl-4 w-100" id="Project">
                    <li class="nav-item">
                        <a class="nav-link pl-3 link_kkkkk"href="{{ route('account.edit_project_view', $project->slug) }}">
                            <span class="ml-1 item-text"style="color: #e4d125;">Project Card</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pl-3 link_kkkkk"href="{{ route('account.project.stakeholders_view', $project->slug) }}">
                            <span class="ml-1 item-text"style="color: #e4d125;">Stakeholders</span>
                        </a>
                    </li>
                    @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_abbreviations', $Project_Permissions ?? []))
                    <li class="nav-item">
                        <a class="nav-link pl-3 link_kkkkk"href="{{ route('project.index_abbreviations') }}">
                            <span class="ml-1 item-text"style="color: #e4d125;">Abbreviations</span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_project_contacts', $Project_Permissions ?? []))
                    <li class="nav-item">
                        <a class="nav-link pl-3 link_kkkkk"href="{{ route('project.index_contacts') }}">
                            <span class="ml-1 item-text"style="color: #e4d125;">Contacts</span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_users', $Account_Permissions ?? []))
                    <li class="nav-item">
                        <a class="nav-link pl-3 link_kkkkk"href="{{ route('project.index_users') }}">
                            <span class="ml-1 item-text"style="color: #e4d125;">Users</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @php
                if ($Project_Permissions != null) {
                    $itemsToCheck = ['upload_documents', 'upload_group_documents', 'import_documents'];

                    $found2 = false;

                    foreach ($itemsToCheck as $item) {
                        if (in_array($item, $Project_Permissions)) {
                            $found2 = true;
                            break;
                        }
                    }
                } else {
                    $found2 = false;
                }

            @endphp
            @if (auth()->user()->roles->first()->name == 'Super Admin' || $found2 == true)
                <li class="nav-item dropdown">
                    <a href="#Documents" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link link_kkkkk">
                        <img src="{{ asset('/dashboard/assets/selected_images/docs.png') }}" width="22">
                        <span class="ml-3 item-text" style="color:whitesmoke;">Documents</span>
                    </a>
                    <ul class="collapse list-unstyled pl-4 w-100" id="Documents">
                        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('upload_documents', $Project_Permissions ?? []))
                            <li class="nav-item">
                                <a class="nav-link pl-3 link_kkkkk" href="{{ route('project.upload_single_doc.create') }}"><span
                                        class="ml-1 item-text"style="color: #e4d125;">Upload Single Document</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                                in_array('upload_group_documents', $Project_Permissions ?? []))
                            <li class="nav-item">
                                <a class="nav-link pl-3 link_kkkkk" style="font-size: 0.8rem;"
                                    href="{{ route('project.upload-group-documents') }}"><span
                                        class="ml-1 item-text"style="color: #e4d125;">Upload
                                        Group of
                                        Documents</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('import_documents', $Project_Permissions ?? []))
                            <li class="nav-item">
                                <a class="nav-link pl-3 link_kkkkk" style="font-size: 0.8rem;"
                                    href="{{ route('import_docs_view') }}"><span class="ml-1 item-text"style="color: #e4d125;">Import
                                        Documents
                                        from Excel</span></a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif

             <li class="nav-item">

                <a class="nav-link pl-3 link_kkkkk" href="#"style="padding-left: 0.5rem !important;"><img src="{{ asset('/dashboard/assets/selected_images/reports.png') }}" width="22"><span class="ml-1 item-text" style="margin-left: 1rem !important;color:whitesmoke;">
                        Reports</span></a>
            </li>
             <li class="nav-item">

                <a class="nav-link pl-3 link_kkkkk" href="#"style="padding-left: 0.5rem !important;"><img src="{{ asset('/dashboard/assets/selected_images/w_analysis.png') }}" width="22"><span class="ml-1 item-text" style="margin-left: 1rem !important;color:whitesmoke;">
                        Window Analysis</span></a>
            </li>
            @php
                if ($Project_Permissions != null) {
                    $itemsToCheck = [
                        'show_contract_tags',
                        'show_project_folder',
                        'show_document_type',
                        'show_contract_settings',
                    ];

                    $found = false;

                    foreach ($itemsToCheck as $item) {
                        if (in_array($item, $Project_Permissions)) {
                            $found = true;
                            break;
                        }
                    }
                } else {
                    $found = false;
                }

            @endphp
            @if (auth()->user()->roles->first()->name == 'Super Admin' || $found == true)
                <li class="nav-item dropdown">
                    <a href="#forms" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link link_kkkkk">
                        <img src="{{ asset('/dashboard/assets/selected_images/setting.png') }}" width="22">
                        <span class="ml-3 item-text" style="color:whitesmoke;">Settings</span>
                    </a>
                    <ul class="collapse list-unstyled pl-4 w-100" id="forms">
                        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_contract_tags', $Project_Permissions ?? []))
                            <li class="nav-item">
                                <a class="nav-link pl-3 link_kkkkk" href="{{ route('project.contract-tags') }}"><span
                                        class="ml-1 item-text"style="color: #e4d125;">Contract Tags</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                                in_array('show_project_folder', $Project_Permissions ?? []))
                            <li class="nav-item">
                                <a class="nav-link pl-3 link_kkkkk" href="{{ route('project.project-folders') }}"><span
                                        class="ml-1 item-text"style="color: #e4d125;">Project Folders</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_document_type', $Project_Permissions ?? []))
                            <li class="nav-item">
                                <a class="nav-link pl-3 link_kkkkk" href="{{ route('project.document-types') }}"><span
                                        class="ml-1 item-text"style="color: #e4d125;">Document Types</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                                in_array('show_contract_settings', $Project_Permissions ?? []))
                            <li class="nav-item">
                                <a href="#Contract-Settings" data-toggle="collapse" aria-expanded="false"
                                    class="dropdown-toggle nav-link link_kkkkk"style="padding-left: 1rem !important;">

                                    <span class="ml-1 item-text"style="color: #e4d125;">Contract Settings</span>
                                </a>
                                <ul class="collapse list-unstyled pl-4 w-100" id="Contract-Settings">
                                    <li class="nav-item">
                                        <a class="nav-link pl-3 link_kkkkk"
                                            href="{{ route('project.contract-settings', 'contract_provision_category') }}"><span
                                                class="ml-1 item-text" style="color: #00d0ff">Contract Provision Categories</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link pl-3 link_kkkkk"
                                            href="{{ route('project.contract-settings', 'contract_document') }}"><span
                                                class="ml-1 item-text"style="color: #00d0ff">Contract Documents</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link pl-3 link_kkkkk"
                                            href="{{ route('project.contract-settings', 'provision_type') }}"><span
                                                class="ml-1 item-text"style="color: #00d0ff">Provision Types</span></a>
                                    </li>


                                </ul>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif



        </ul>



    </nav>
</aside>
