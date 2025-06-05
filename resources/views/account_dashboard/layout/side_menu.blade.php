<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar
    style="background-color: black !important;">>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>
    <nav class="vertnav navbar navbar-light">
        <!-- nav bar -->
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ asset(url('/account')) }}">
                <img id="logo" src="{{ asset('dashboard/assets/images/logo.png') }}"
                    @if ($sideBarTheme == '0') width="50" @else width="130" @endif
                    style="border-radius: 50%;border: 6px solid #495057 ;">

            </a>
        </div>




        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item">

                <a class="nav-link pl-3 link_kkkkk"
                    href="{{ route('account.projects') }}"style="padding-left: 0.5rem !important;"><img
                        src="{{ asset('/dashboard/assets/selected_images/setting.png') }}" width="22"><span
                        class="ml-1 item-text" style="margin-left: 1rem !important;color:whitesmoke;">
                        Projects</span></a>


            </li>
            @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_users', $Account_Permissions ?? []))
                <li class="nav-item">

                    <a class="nav-link pl-3 link_kkkkk"
                        href="{{ route('account.users') }}"style="padding-left: 0.5rem !important;"><img
                            src="{{ asset('/dashboard/assets/selected_images/setting.png') }}" width="22"><span
                            class="ml-1 item-text" style="margin-left: 1rem !important;color:whitesmoke;">
                            Users</span></a>


                </li>
            @endif
            @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_eps', $Account_Permissions ?? []))
                <li class="nav-item">

                    <a class="nav-link pl-3 link_kkkkk"
                        href="{{ route('account.EPS') }}"style="padding-left: 0.5rem !important;"><img
                            src="{{ asset('/dashboard/assets/selected_images/setting.png') }}" width="22"><span
                            class="ml-1 item-text" style="margin-left: 1rem !important;color:whitesmoke;">
                            EPS</span></a>


                </li>
            @endif
            @php
                if ($Account_Permissions != null) {
                    $itemsToCheck = [
                        'show_contract_tags',
                        'show_project_folder',
                        'show_document_type',
                        'show_contract_settings',
                    ];

                    $found = false;

                    foreach ($itemsToCheck as $item) {
                        if (in_array($item, $Account_Permissions)) {
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
                    <a href="#forms" data-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle nav-link link_kkkkk">
                        <img src="{{ asset('/dashboard/assets/selected_images/setting.png') }}" width="22">
                        <span class="ml-3 item-text" style="color:whitesmoke;">Settings</span>
                    </a>
                    <ul class="collapse list-unstyled pl-4 w-100" id="forms">
                        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_contract_tags', $Account_Permissions ?? []))
                            <li class="nav-item">
                                <a class="nav-link pl-3 link_kkkkk" href="{{ route('account.contract-tags') }}"><span
                                        class="ml-1 item-text" style="color: #e4d125;">Contract Tags</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                                in_array('show_project_folder', $Account_Permissions ?? []))
                            <li class="nav-item">
                                <a class="nav-link pl-3 link_kkkkk" href="{{ route('account.project-folders') }}"><span
                                        class="ml-1 item-text" style="color: #e4d125;">Project Folders</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_document_type', $Account_Permissions ?? []))
                            <li class="nav-item">
                                <a class="nav-link pl-3 link_kkkkk" href="{{ route('account.document-types') }}"><span
                                        class="ml-1 item-text" style="color: #e4d125;">Document Types</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                                in_array('show_contract_settings', $Account_Permissions ?? []))
                            <li class="nav-item">
                                <a href="#Contract-Settings" data-toggle="collapse" aria-expanded="false"
                                    class="dropdown-toggle nav-link link_kkkkk"style="padding-left: 1rem !important;">

                                    <span class="ml-1 item-text" style="color: #e4d125;">Contract Settings</span>
                                </a>
                                <ul class="collapse list-unstyled pl-4 w-100" id="Contract-Settings">
                                    <li class="nav-item">
                                        <a class="nav-link pl-3 link_kkkkk"
                                            href="{{ route('account.contract-settings', 'contract_provision_category') }}"><span
                                                class="ml-1 item-text"style="color: #00d0ff">Contract Provision
                                                Categories</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link pl-3 link_kkkkk"
                                            href="{{ route('account.contract-settings', 'contract_document') }}"><span
                                                class="ml-1 item-text"style="color: #00d0ff">Contract
                                                Documents</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link pl-3 link_kkkkk"
                                            href="{{ route('account.contract-settings', 'provision_type') }}"><span
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
