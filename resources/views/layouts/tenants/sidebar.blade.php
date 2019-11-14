<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ Gravatar::src(Sentinel::getUser()->email) }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">{{ config('app.name', 'Bima Alert') }} Menu</li>
            <!-- Dashboard -->
            <li class="{{ active_class(if_uri(['dashboard'])) }}"><a href="{{ url('dashboard') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

            <!-- Cables Links -->
            <li class="{{ active_class(if_uri(['cables', 'cables/create'])) }}"><a href="{{ url('cables') }}"><i class="fa fa-arrows-alt"></i> <span>Cables</span></a></li>

            <!-- Clients Links -->
            <li class="{{ active_class(if_uri(['clients', 'clients/create'])) }}"><a href="{{ url('clients') }}"><i class="fa fa-users"></i> <span>Clients</span></a></li>

            <!-- Communications Links -->
            <li class="{{ active_class(if_uri(['communications'])) }}"><a href="{{ url('communications') }}"><i class="fa fa-envelope"></i> <span>SMS Portal</span></a></li>

            <!-- Settings Links -->
            <li class="treeview {{ active_class(if_uri(['types', 'types/create', 'schedules', 'users', 'users/create', 'roles', 'roles/create'])) }}">
                <a href="#"><i class="fa fa-cogs"></i> <span>Settings</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">                   
                    <li><a href="{{ url('types') }}">Cable Types</a></li>
                    <li><a href="{{ url('schedules') }}">Notification Schedule</a></li>
                    <li><a href="{{ url('users') }}">System Users</a></li>
                    <li><a href="{{ url('roles') }}">System Roles</a></li> 
                </ul>
            </li>

            <!-- Tools Links -->
            <li class="header">{{ config('app.name', 'Bima Alert') }} Tools</li>
            {{-- Fast Track --}}            
            <li class="{{ active_class(if_uri(['fastTrack'])) }}"><a href="{{ url('fastTrack') }}"><i class="fa fa-circle-o text-green"></i> <span>Fast Track</span></a></li>
            {{-- Bulk SMS --}}
            <li class="{{ active_class(if_uri(['bulk'])) }}"><a href="{{ url('bulk') }}"><i class="fa fa-circle-o text-blue"></i> <span>Bulk SMS</span></a></li>
            {{-- Reports --}}
            <li class="{{ active_class(if_uri(['reports'])) }}"><a href="{{ url('reports') }}"><i class="fa fa-circle-o text-yellow"></i> <span>Executive Summary</span></a></li>
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>