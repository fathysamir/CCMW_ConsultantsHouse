<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>
    <nav class="vertnav navbar navbar-light">
        <!-- nav bar -->
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ asset(url('/project')) }}">
                <img id="logo" src="{{ asset('dashboard/assets/images/logo.png') }}" width="100"
                    style="border-radius: 5px;">

            </a>
        </div>




        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item">

                <a class="nav-link pl-3" href="{{ route('project.home') }}"style="padding-left: 0.5rem !important;"><i
                        class="fe fe-home fe-16"></i><span class="ml-1 item-text" style="margin-left: 1rem !important;">
                        Home</span></a>
            </li>
            <li class="nav-item dropdown">
                <a href="#Files" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                    <i class="fe fe-folder fe-16"></i>
                    <span class="ml-3 item-text">Files</span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="Files">
                    @forelse($Folders as $folder)
                        <li class="nav-item">
                            <a class="nav-link pl-3" href="{{ route('switch.folder', $folder->id) }}">
                                <span class="ml-1 item-text">{{ $folder->name }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="nav-item">
                            <a class="nav-link pl-3" href="">
                                <span class="ml-1 item-text">No folders found</span>
                            </a>
                        </li>
                    @endforelse


                </ul>
            </li>
            <li class="nav-item dropdown">
                <a href="#Documents" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                    <i class="fe fe-file fe-16"></i>
                    <span class="ml-3 item-text">Documents</span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="Documents">
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="{{ route('project.upload_single_doc.create') }}"><span
                                class="ml-1 item-text">Upload Single Document</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pl-3" href=""><span class="ml-1 item-text">Upload Group of
                                Documents</span></a>
                    </li>


                </ul>
            </li>
            <li class="nav-item dropdown">
                <a href="#forms" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                    <i class="fe fe-settings fe-16"></i>
                    <span class="ml-3 item-text">Settings</span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="forms">
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="{{ route('project.contract-tags') }}"><span
                                class="ml-1 item-text">Contract Tags</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="{{ route('project.project-folders') }}"><span
                                class="ml-1 item-text">Project Folders</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="{{ route('project.document-types') }}"><span
                                class="ml-1 item-text">Document Types</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="#Contract-Settings" data-toggle="collapse" aria-expanded="false"
                            class="dropdown-toggle nav-link"style="padding-left: 1rem !important;">

                            <span class="ml-1 item-text">Contract Settings</span>
                        </a>
                        <ul class="collapse list-unstyled pl-4 w-100" id="Contract-Settings">
                            <li class="nav-item">
                                <a class="nav-link pl-3"
                                    href="{{ route('project.contract-settings', 'contract_provision_category') }}"><span
                                        class="ml-1 item-text">Contract Provision Categories</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3"
                                    href="{{ route('project.contract-settings', 'contract_document') }}"><span
                                        class="ml-1 item-text">Contract Documents</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3"
                                    href="{{ route('project.contract-settings', 'provision_type') }}"><span
                                        class="ml-1 item-text">Provision Types</span></a>
                            </li>


                        </ul>
                    </li>

                </ul>
            </li>



        </ul>



    </nav>
</aside>
