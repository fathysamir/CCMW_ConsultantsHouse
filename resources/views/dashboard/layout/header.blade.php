<nav class="topnav navbar navbar-light mb-2" style="background-color: gray;">
  <button onclick="toggleWidth()" type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
    <i style="color:white" class="fe fe-menu navbar-toggler-icon"></i>
  </button>
 
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link text-muted my-2" href="#" id="modeSwitcher" data-mode="light">
        <i style="color:white" class="fe fe-sun fe-16"></i>
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
      <a  style="color:white" class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span style="color:white" class="avatar avatar-sm mt-2">
          <img @if(getFirstMediaUrl(auth()->user(),auth()->user()->avatarCollection)!=null) src="{{getFirstMediaUrl(auth()->user(),auth()->user()->avatarCollection)}}" @else src="{{asset('dashboard/assets/avatars/user_avatar.png')}}" @endif alt="..." class="avatar-img rounded-circle">
        </span>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
        <a class="dropdown-item" href="#">Profile</a>
        <a class="dropdown-item" href="#">Settings</a>
        <a class="dropdown-item" href="{{route('logout')}}">Logout</a>
      </div>
    </li>
  </ul>
</nav>