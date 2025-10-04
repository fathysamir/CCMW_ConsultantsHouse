<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar
    style="background-color: black !important;">
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>
    <nav class="vertnav navbar navbar-light">
        <!-- nav bar -->
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ asset(url('/')) }}">
                <img id="logo" src="{{ asset('dashboard/assets/images/logo.png') }}"
                    @if ($sideBarTheme == '0') width="50" @else width="130" @endif
                    style="border-radius: 50%;border: 6px solid #495057 ;">

            </a>
        </div>



        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item">

                <a class="nav-link pl-3 link_kkkkk"
                    href="{{ route('home') }}"style="padding-left: 0.5rem !important;"><img
                        src="{{ asset('/dashboard/assets/selected_images/setting.png') }}" width="22"><span
                        class="ml-1 item-text" style="margin-left: 1rem !important;color:whitesmoke;">
                        Accounts</span></a>


            </li>
            @role('Super Admin')
                <li class="nav-item dropdown">
                    <a href="#forms" data-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle nav-link link_kkkkk">
                        <img src="{{ asset('dashboard/assets/selected_images/setting.png') }}" width="22">
                        <span class="ml-3 item-text" style="color:whitesmoke;">Settings</span>
                    </a>
                    <ul class="collapse list-unstyled pl-4 w-100" id="forms">
                        
                        <li class="nav-item">
                            <a class="nav-link pl-3 link_kkkkk" href="{{ route('accounts.export-formate-settings') }}"><span
                                    class="ml-1 item-text" style="color:#e4d125;">Export Formate</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3 link_kkkkk" href="{{ route('accounts.contract-tags') }}"><span
                                    class="ml-1 item-text" style="color:#e4d125;">Contract Tags</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3 link_kkkkk" href="{{ route('accounts.project-folders') }}"><span
                                    class="ml-1 item-text" style="color:#e4d125;">Project Folders</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3 link_kkkkk" href="{{ route('accounts.document-types') }}"><span
                                    class="ml-1 item-text" style="color:#e4d125;">Document Types</span></a>
                        </li>
                        <li class="nav-item">
                            <a href="#Contract-Settings" data-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle nav-link link_kkkkk"style="padding-left: 1rem !important;">

                                <span class="ml-1 item-text"style="color:#e4d125;">Contract Settings</span>
                            </a>
                            <ul class="collapse list-unstyled pl-4 w-100" id="Contract-Settings">
                                <li class="nav-item">
                                    <a class="nav-link pl-3 link_kkkkk"
                                        href="{{ route('accounts.contract-settings', 'contract_provision_category') }}"><span
                                            class="ml-1 item-text" style="color: #00d0ff">Contract Provision
                                            Categories</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link pl-3 link_kkkkk"
                                        href="{{ route('accounts.contract-settings', 'contract_document') }}"><span
                                            class="ml-1 item-text"style="color: #00d0ff">Contract Documents</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link pl-3 link_kkkkk"
                                        href="{{ route('accounts.contract-settings', 'provision_type') }}"><span
                                            class="ml-1 item-text"style="color: #00d0ff">Provision Types</span></a>
                                </li>

                            </ul>
                        </li>

                    </ul>
                </li>
            @endrole


        </ul>



    </nav>
</aside>
