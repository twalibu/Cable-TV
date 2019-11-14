<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ url('dashboard') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>C</b>A</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Cable</b> Alert</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ Gravatar::src(Sentinel::getUser()->email) }}" class="user-image" alt="User Image"/>
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ Gravatar::src(Sentinel::getUser()->email) }}" class="img-circle" alt="User Image" />
                            <p>
                                {{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }} - {{ Sentinel::getUser()->tenant->name }}
                                <small>{{ config('app.name', 'Bima Alert') }}</small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right">
                                <a href="{{ url('logout') }}" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>