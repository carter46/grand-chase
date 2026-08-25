<?php
if (Auth('admin')->User()->dashboard_style == 'light') {
    $bgmenu = 'blue';
    $bg = 'light';
    $text = 'dark';
} else {
    $bgmenu = 'dark';
    $bg = 'dark';
    $text = 'light';
}

?>
<div class="main-header">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="{{ $bgmenu }}">
        <button class="navbar-toggler sidenav-toggler" type="button"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <i class="icon-menu"></i>
            </span>
        </button>
        <a href="{{ url('/admin/dashboard') }}" class="logo" title="{{ $settings->site_name }}" style="color:#fff;">
            {{ $settings->site_name }}
        </a>

        <div class="admin-mobile-user dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false" aria-label="Account menu">
                <i class="text-white fas fa-user"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right dropdown-user animated fadeIn">
                <li>
                    <a class="dropdown-item" href="{{ url('admin/dashboard/adminprofile') }}">Account Settings</a>
                    <a class="dropdown-item" href="{{ url('admin/dashboard/adminchangepassword') }}">Change Password</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('adminlogout') }}"
                        onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        Logout
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar" type="button" aria-label="Minimize sidebar">
                <i class="icon-menu"></i>
            </button>
        </div>
    </div>
    <!-- End Logo Header -->

    <!-- Navbar Header (desktop) -->
    <nav class="navbar navbar-header navbar-expand-lg" data-background-color="{{ $bgmenu }}">
        <div class="container-fluid">
            <div class="collapse show" id="search-nav">
                <a href="{{ route('manageusers') }}">
                    <form class="navbar-left navbar-form nav-search mr-md-3" action="{{ route('manageusers') }}" method="get">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="submit" class="pr-1 btn btn-search">
                                    <i class="fa fa-search search-icon"></i>
                                </button>
                            </div>
                            <input type="text" placeholder="Manage users"
                                class="form-control text-{{ $text }} ">
                        </div>
                    </form>
                </a>
            </div>
            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                <li class="nav-item dropdown hidden-caret">
                    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="text-white fas fa-user"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <a class="dropdown-item" href="{{ url('admin/dashboard/adminprofile') }}">Account
                                    Settings</a>
                                <a class="dropdown-item" href="{{ url('admin/dashboard/adminchangepassword') }}">Change
                                    Password</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('adminlogout') }}"
                                    onclick="event.preventDefault();
                                document.getElementById('logoutform').submit();">
                                    Logout
                                </a>
                                <form id="logoutform" action="{{ route('adminlogout') }}" method="POST"
                                    style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>
