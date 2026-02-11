@auth
<!-- Dashboard -->
<li class="nav-item">
    <a href="{{ route('masterapp.dashboard') }}" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>
<!-- Users -->
@canany(['list-users', 'list-driver', 'list-wordpress-user'])
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-users-cog"></i>
        <p>
            Manage Users
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">

        @can('list-users')
        <li class="nav-item">
            <a href="{{ route('masterapp.users.index') }}" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>Users</p>
            </a>
        </li>
        @endcan
        @can('list-driver')
        <li class="nav-item">
            <a href="{{ route('masterapp.drivers.index') }}" class="nav-link">
                <i class="nav-icon fas fa-truck"></i>
                <p>Drivers</p>
            </a>
        </li>
        @endcan
        @can('list-wordpress-user')
        <li class="nav-item">
            <a href="{{ route('masterapp.wordpress.index') }}" class="nav-link">
                <i class="nav-icon fab fa-wordpress"></i>
                <p>Wordpress Users</p>
            </a>
        </li>
        @endcan
    </ul>
</li>
@endcanany
@can('list-timesheets')
<li class="nav-item">
    <a href="{{ route('masterapp.timesheets.index') }}" class="nav-link">
        <i class="nav-icon fas fa-user-clock"></i>
        <p>Manage Timesheets</p>
    </a>
</li>
@endcan 
@can('list-time-off-requests')
<li class="nav-item">
    <a href="{{ route('masterapp.time-off-requests.index') }}" class="nav-link">
        {{-- <i class="nav-icon fas fa-calendar-times-o"></i> --}}
        <i class="nav-icon fas fa-calendar-times"></i>
        <p>Manage Time-off</p>
    </a>
</li>
@endcan
<li class="nav-item">
    <a href="{{ route('masterapp.contacts.index') }}" class="nav-link">
        <i class="nav-icon fas fa-address-book"></i>
        <p>Manage Contacts</p>
    </a>
</li>
@can('list-role')
<li class="nav-item">
    <a href="{{ route('masterapp.roles.index') }}" class="nav-link">
        <i class="nav-icon fas fa-user-tag"></i>
        <p>Manage Role</p>
    </a>
</li>
@endcan

@can('list-modules')
<li class="nav-item">
    <a href="{{ route('masterapp.modules.index') }}" class="nav-link">
        <i class="nav-icon fas fa-layer-group"></i>
        <p>Manage Modules</p>
    </a>
</li>
@endcan

@can('list-permission')
<li class="nav-item">
    <a href="{{ route('masterapp.permissions.index') }}" class="nav-link">
        <i class="nav-icon fas fa-key"></i>
        <p>Manage Permissions</p>
    </a>
</li>
@endcan
@endauth 
