<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
      </li>
      <!-- <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li> -->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <!-- <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li> -->

      <!-- Notifications Dropdown Menu -->
       @php
            $unreadCount = auth()->user()->unreadNotifications()->count();
            $notifications = auth()->user()->notifications()->latest()->take(5)->get();
        @endphp

        <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>

        @if ($unreadCount > 0)
            <span id="topMenuNotifCount" class="badge badge-warning navbar-badge">{{ $unreadCount }}</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">
            {{ $unreadCount }} Notifications
        </span>

        @forelse ($notifications as $notification)
            <div class="dropdown-divider"></div>

            <a href="{{ route('masterapp.notifications.read', $notification->id) }}"
               class="dropdown-item {{ is_null($notification->read_at) ? 'font-weight-bold' : '' }}">
                <i class="fas fa-bell mr-2"></i>
                {{ $notification->data['message'] ?? 'Notification' }}
                <span class="float-right text-muted text-sm">
                    {{ $notification->created_at->diffForHumans() }}
                </span>
            </a>

        @empty
            <div class="dropdown-divider"></div>
            <span class="dropdown-item text-muted">No notifications found</span>
        @endforelse

        <div class="dropdown-divider"></div>

        <a href="{{ route('masterapp.notifications.index') }}"
           class="dropdown-item dropdown-footer">
            See All Notifications
        </a>
    </div>
</li>

<li class="nav-item">
    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
    </a>
</li>


        <!-- User Account -->
        <li class="nav-item dropdown user-menu">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="fas fa-user-circle user-icon"></i> <span class="user-name"> {{ auth()->user()->first_name }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="user-card bg-primary">
                    <div class="card-body text-center">
                        <h5>{{ Auth::user()->first_name }}</h5>
                        <p class="mb-0">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="dropdown-divider"></div>

                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>

                <a href="{{ route('profile.changepassword') }}" class="dropdown-item">
                    <i class="fas fa-key mr-2"></i> Change Password
                </a>

                <a href="{{ route('masterapp.settings') }}" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> Settings
                </a>

                <div class="dropdown-divider"></div>

                <a href="{{ route('logout') }}"
                   class="dropdown-item"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
  </nav>`