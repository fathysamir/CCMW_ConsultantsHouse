<nav class="topnav navbar navbar-light mb-2" style="background-color: #1b68ff;">
    <button onclick="toggleWidth()" type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
        <i style="color:white" class="fe fe-menu navbar-toggler-icon"></i>
    </button>

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
                    <img @if (getFirstMediaUrl(auth()->user(), auth()->user()->avatarCollection) != null) src="{{ getFirstMediaUrl(auth()->user(), auth()->user()->avatarCollection, true) }}" @else src="{{ asset('dashboard/assets/avatars/user_avatar.png') }}" @endif
                        alt="..." class="avatar-img rounded-circle" width=35 height=35>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                <a class="dropdown-item" id="UpdateUserProfileInfoLink" href="#">Profile</a>
                <a class="dropdown-item" href="#">Settings</a>
                <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
            </div>
        </li>
    </ul>
</nav>
