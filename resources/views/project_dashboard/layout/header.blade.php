<nav class="topnav navbar navbar-light">
    <button onclick="toggleWidth()" type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
        <i class="fe fe-menu navbar-toggler-icon"></i>
    </button>
    <div class="row">
        <ul class="nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle  pr-0 l-link" href="#" id="navbarDropdownMenuLink2"
                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"style="color:rgb(80, 78, 78)" >
                    
                        <span class="ml-1 item-text"><b>Files</b></span>
                    
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink2">
                    
                        @forelse($Folders as $folder)
                            @if($folder->shortcut=='1')
                                <a class="nav-link pl-3" href="{{ route('switch.folder', $folder->id) }}">
                                    <span class="ml-1 item-text">{{ $folder->name }}</span>
                                </a>
                            @endif
                        @empty
                            
                                <a class="nav-link pl-3" href="">
                                    <span class="ml-1 item-text">No folders found</span>
                                </a>
                           
                        @endforelse
    
    
                    
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link pl-3 nav-item l-link" style="color:rgb(80, 78, 78)"
            href="{{ route('project.all_documents.index') }}"><span class="ml-1 item-text"><b>Documents</b></span></a>
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
            <a class="nav-link text-muted my-2" href="#" id="modeSwitcher" data-mode="light">
                <i class="fe fe-sun fe-16"></i>
            </a>
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
            <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink"
                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="avatar avatar-sm mt-2">
                    <img @if (getFirstMediaUrl(auth()->user(), auth()->user()->avatarCollection) != null) src="{{ getFirstMediaUrl(auth()->user(), auth()->user()->avatarCollection) }}" @else src="{{ asset('dashboard/assets/avatars/user_avatar.png') }}" @endif
                        alt="..." class="avatar-img rounded-circle">
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                <a class="dropdown-item" href="#">Profile</a>
                <a class="dropdown-item" href="#">Settings</a>
                <a class="dropdown-item" href="{{ route('project.home') }}">Project Details</a>
                <a class="dropdown-item" href="{{ route('account.home') }}">Account Details</a>
                <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
            </div>
        </li>
    </ul>
</nav>
