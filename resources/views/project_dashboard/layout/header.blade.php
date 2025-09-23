<nav class="topnav navbar navbar-light mb-2" style="background-color: #1b68ff">
    <button onclick="toggleWidth()" type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
        <i style="color:white" class="fe fe-menu navbar-toggler-icon"></i>
    </button>
    <div class="row">
        <ul class="nav">
            <li class=" row nav-item dropdown mr-4">
                <a class="nav-link pl-3 pr-0 vibrate-hover"
                    style="color:white; font-size:1.2rem; padding-top:0px !important;padding-bottom:0px !important;"
                    href="{{ route('switch.folder', $Folders->first()->id) }}">
                    <span class="item-text"><b>Files</b></span>
                </a>

                <!-- Dropdown arrow -->
                <a class="nav-link dropdown-toggle pr-0 vibrate-hover" href="#" id="navbarDropdownMenuLink2"
                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="color:white; padding-left: 0.25rem;font-size:1.2rem;padding-top:0px !important;padding-bottom:0px !important;">
                </a>

                <!-- Dropdown menu -->
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink2">
                    @forelse($Folders as $folder)
                        @if ($folder->shortcut == '1')
                            <a class="nav-link pl-3 l-link" style="color:rgb(80, 78, 78);"
                                href="{{ route('switch.folder', $folder->id) }}">
                                <span class="ml-1 item-text">{{ $folder->name }}</span>
                            </a>
                        @endif
                    @empty
                        <a class="nav-link pl-3" href="#">
                            <span class="ml-1 item-text">No folders found</span>
                        </a>
                    @endforelse
                </div>
            </li>
            <li class="row nav-item dropdown mr-3">
                <a class="nav-link pl-3 pr-0 vibrate-hover"
                    style="color:white;font-size:1.2rem;padding-top:0px !important;padding-bottom:0px !important;"
                    href="{{ route('project.all_documents.index') }}"><span
                        class="ml-1 item-text"><b>Documents</b></span></a>
                <a class="nav-link dropdown-toggle pr-0 vibrate-hover" href="#" id="navbarDropdownMenuLink3"
                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="color:white; padding-left: 0.25rem;font-size:1.2rem;padding-top:0px !important;padding-bottom:0px !important;">
                </a>

                <!-- Dropdown menu -->
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink3">
                    @forelse($DocTypes_ as $type)
                        @if ($type->shortcut == '1')
                            <a class="nav-link pl-3 l-link" style="color:rgb(80, 78, 78);"
                                href="{{ url('/project/all-documents?doc_type=' . $type->id) }}">
                                <span class="ml-1 item-text">{{ $type->name }}</span>
                            </a>
                        @endif
                    @empty
                        <a class="nav-link pl-3" href="#">
                            <span class="ml-1 item-text">No folders found</span>
                        </a>
                    @endforelse
                </div>
            </li>
            @php
                $project = \App\Models\Project::findOrFail(auth()->user()->current_project_id);
            @endphp
            <li class="nav-item">
                <a class="nav-link pl-3 nav-item vibrate-hover"
                    style="color:white;font-size:1.2rem;padding-top:0px !important;padding-bottom:0px !important;"
                    href="{{ route('account.edit_project_view', $project->slug) }}"><span
                        class="ml-1 item-text"><b>Project Card</b></span></a>
            </li>
            {{-- <li class="nav-item">
          <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-shortcut">
            <span class="fe fe-grid fe-16"></span>
          </a>
            </li>
            <li class="nav-item nav-notif">
            <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-notif">
                <span class="fe fe-bell fe-16"></span>
                <span class="dot dot-md bg-success"></span>
            </a>
            </li> --}}

        </ul>

    </div>
    <ul class="nav">
        <li class="nav-item">
            <a onclick="toggle2()" class="nav-link text-muted my-2" id="modeSwitcher" data-mode="light"
                style="padding-top:0px !important;padding-bottom:0px !important;">
                <img id="modeIcon" src="{{ asset('/dashboard/assets/selected_images/moon.png') }}" width="20">
            </a>
        </li>
        <li class="nav-item">
            <img src="{{ asset('/dashboard/assets/selected_images/bill.png') }}" width="22"
                style="margin-top: 6px;">
        </li>
        {{-- <li class="nav-item">
      <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-shortcut">
        <span class="fe fe-grid fe-16"></span>
      </a>
        </li>
        <li class="nav-item nav-notif">
        <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-notif">
            <span class="fe fe-bell fe-16"></span>
            <span class="dot dot-md bg-success"></span>
        </a>
        </li> --}}
        <li class="nav-item dropdown">
            <a style="color:white;padding-top:0px !important;padding-bottom:0px !important;"
                class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink"
                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span style="color:white" class="avatar avatar-sm mt-2">
                    <img @if (getFirstMediaUrl(auth()->user(), auth()->user()->avatarCollection) != null) src="{{ getFirstMediaUrl(auth()->user(), auth()->user()->avatarCollection,true) }}" @else src="{{ asset('dashboard/assets/avatars/user_avatar.png') }}" @endif
                        alt="..." class="avatar-img rounded-circle"width=35 height=35>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                <a class="dropdown-item" href="#" id="UpdateUserProfileInfoLink">Profile</a>
                <a class="dropdown-item" href="{{ route('account.home') }}">Account Details</a>
                <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
            </div>
        </li>
    </ul>
</nav>