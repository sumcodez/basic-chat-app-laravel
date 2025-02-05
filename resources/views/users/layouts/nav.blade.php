<!-- Navigation Bar -->
<nav class="navbar" id="myTopnav">
    <div class="logo"><a href="{{ route('users.all') }}">Hi ! {{$current_user->first_name}}</a></div>
      <ul class="nav-links" id="top-nav">
        <li class="dropdown">
          <a href="javascript:void(0)" class="dropbtn">Profile</a>
          <div class="dropdown-content">
            <a href="{{ route('users.manage_Profile') }}">Manage</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <a href="route('logout')"
                  onclick="event.preventDefault();
                              this.closest('form').submit();"
                
                class="logout-button"
              >
                  Log Out
            </a>
            </form>
          </div>
            @if($current_user->profile_picture)
                <img src="{{ asset($current_user->profile_picture) }}" alt="Current Profile Picture" class="profile-pic" id="profile-preview">
            @else
                <img src="{{ asset('default_image/defPic.jpg') }}" alt="Default Profile Picture" class="profile-pic" id="profile-preview">
            @endif
        </li>
      </ul>
    {{-- <div class="menu-toggle" onclick="toggleMenu()"><i class="fa fa-bars"></i></div> --}}
    <a href="javascript:void(0);" class="menu-toggle" onclick="toggleMenu()">
      <i class="fa fa-bars"></i>
    </a>
  </nav>