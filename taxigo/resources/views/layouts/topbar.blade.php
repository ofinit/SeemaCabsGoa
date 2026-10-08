<!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper">
        <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ph-duotone ph-sun-dim"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
                            <i class="ph-duotone ph-moon"></i>
                            <span>Dark</span>
                        </a>
                        <a href="#!" class="dropdown-item" onclick="layout_change('light')">
                            <i class="ph-duotone ph-sun-dim"></i>
                            <span>Light</span>
                        </a>
                        <a href="#!" class="dropdown-item" onclick="layout_change_default()">
                            <i class="ph-duotone ph-cpu"></i>
                            <span>Default</span>
                        </a>
                    </div>
                </li>
                <li class="pc-h-item">
                    <a class="pc-head-link pct-c-btn" href="#" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas_pc_layout">
                        <i class="ph-duotone ph-gear-six"></i>
                    </a>
                </li>
                 @php
                    $notificationList = getNotificationList();
                @endphp
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ph-duotone ph-bell"></i>
                        <span class="badge bg-success pc-h-badge">{{ count($notificationList) }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header d-flex align-items-center justify-content-between">
                            <h5 class="m-0">Notifications</h5>
                            <ul class="list-inline ms-auto mb-0">
                                <li class="list-inline-item">
                                    <a href="{{ route('admin.notifications.index') }}"
                                        class="avtar avtar-s btn-link-hover-primary">
                                        <i class="ti ti-link f-18"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="dropdown-body text-wrap header-notification-scroll position-relative"
                            style="max-height: calc(100vh - 235px)">
                            <ul class="list-group list-group-flush">
                                @if (isset($notificationList) &&  count($notificationList)>0)
                                    @foreach ($notificationList as $list)
                                        <li class="list-group-item">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="avtar avtar-xl">
                                                        <img src="{{{ URL::asset('build/images/logo-dark.svg') }}}"
                                                            alt="user-image" class="avtar avtar-xl object-fit-contain border" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1 me-3 position-relative">
                                                            <h6 class="mb-0 text-truncate">{{ $list->title??'--' }}</h6>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <span class="text-sm">
                                                                {{ $list->created_at ? $list->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') : '--' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="position-relative mt-1 mb-1">{{ $list->text??'--' }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="dropdown-footer">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-grid"><a href="{{ route('admin.notifications.index') }}"
                                            class="btn btn-primary">View All Notifications</a></div>
                                </div>
                                <div class="col-6">
                                    <div class="d-grid"><button class="btn btn-outline-secondary markAsAllRead" onclick="markAsAllRead(this)">Mark all as
                                            read</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        @php
                            $user = Auth::user();
                        @endphp
                        <img src="{{ $user->user_image }}" alt="user-image"
                            class="user-avtar">
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header d-flex align-items-center justify-content-between">
                            <h5 class="m-0">Profile</h5>
                        </div>
                        <div class="dropdown-body">
                            <div class="profile-notification-scroll position-relative"
                                style="max-height: calc(100vh - 225px)">
                                <ul class="list-group list-group-flush w-100">
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $user->user_image }}"
                                                    alt="user-image" class="wid-50 rounded-circle" />
                                            </div>
                                            <div class="flex-grow-1 mx-3">
                                                <h5 class="mb-0">{{$user->name??''}}</h5>
                                                <a class="link-primary"
                                                    href="mailto:carson.darrin@company.io">{{$user->email??''}}</a>
                                            </div>
                                            <!-- <span class="badge bg-primary">PRO</span> -->
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="{{ route('admin.account') }}" class="dropdown-item">
                                            <span class="d-flex align-items-center">
                                                <i class="ph-duotone ph-user"></i>
                                                <span>My Account</span>
                                            </span>
                                        </a>
                                        <a href="{{route('admin.security')}}" class="dropdown-item">
                                            <span class="d-flex align-items-center">
                                                <i class="ph-duotone ph-lock-key"></i>
                                                <span>Security</span>
                                            </span>
                                        </a>
                                        <button class="dropdown-item" onclick="logout(this)">
                                            <span class="d-flex align-items-center">
                                                <i class="ph-duotone ph-power"></i>
                                                <span>Logout</span>
                                            </span>
                                        </button>
                                        {{-- <a href="{{ asset('auth/login') }}" class="dropdown-item">
                                            <span class="d-flex align-items-center">
                                                <i class="ph-duotone ph-power"></i>
                                                <span>Logout</span>
                                            </span>
                                        </a> --}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->

<!-- Modal -->
<!-- <div class="modal fade login-modal" id="login-modal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card mb-0 user-card">
            <div class="modal-body">
                <div class="mb-4 logout-logo">
                    <img src="{{ URL::asset('build/images/logo.svg') }}" alt="images">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Password">
                </div>
                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-primary">Login</button>
                </div>
            </div>
        </div>
    </div>
</div> -->
