<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">

    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ asset('/dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ getLogo() }}" alt="logo image " style="max-height:42px; max-width:100%; width:auto; object-fit:contain;">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                @include('layouts.menu-list')
            </ul>
        </div>
        <div class="card pc-user-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        @php
                            $user = Auth::user();
                        @endphp
                        <img src="{{ $user->user_image }}" alt="user-image" class="user-avtar wid-45 rounded-circle">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="dropdown">
                            <a href="#" class="arrow-none dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false" data-bs-offset="0,20">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 me-2">
                                        <h6 class="mb-0">{{ Auth::user()->name}}</h6>
                                        <small>Administrator</small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="btn btn-icon btn-link-secondary avtar">
                                            <i class="ph-duotone ph-windows-logo"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu">
                                <ul>
                                    <li><a href="{{ route('admin.account') }}" class="dropdown-item">
                                            <span class="d-flex align-items-center">
                                                <i class="ph-duotone ph-user"></i>
                                                <span>My Account</span>
                                            </span>
                                        </a></li>
                                    <li><a class="pc-user-links">
                                            <i class="ph-duotone ph-gear"></i>
                                            <span>Settings</span>
                                        </a></li>

                                    <li> <button class="dropdown-item" onclick="logout(this)">
                                            <span class="d-flex align-items-center">
                                                <i class="ph-duotone ph-power"></i>
                                                <span>Logout</span>
                                            </span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
