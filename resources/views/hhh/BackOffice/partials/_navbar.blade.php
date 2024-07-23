<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <div class="me-2">
            {{-- Desktop Menu --}}
            {{-- minimize_sidebar --}}
            <button id="minimize_sidebar-button" class="navbar-toggler align-self-center" type="button"
                data-bs-toggle="minimize">
                <span id="minimize_sidebar_icon" class="icon-menu"></span>
            </button>
            {{-- Desktop Menu END --}}

            {{-- Mobile Menu --}}
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center ms-3" type="button"
                data-bs-toggle="offcanvas">
                {{-- <span class="mdi mdi-menu"></span> --}}
                <span class="icon-menu"></span>
            </button>
            {{-- Mobile Menu END --}}
        </div>
        <div>
            {{-- brand-logo --}}
            <a class="navbar-brand brand-logo" href="{{ \App\Enums\Routes\AdminPublicRoutesEnum::Dashboard->route() }}">
                <img src="{{ AppSettingsEnum::AdminPanelBigLogo->getImageUrl() }}" alt="logo" />
            </a>
            {{-- brand-logo-mini --}}
            <a class="navbar-brand brand-logo-mini"
                href="{{ \App\Enums\Routes\AdminPublicRoutesEnum::Dashboard->route() }}">
                <img src="{{ AppSettingsEnum::AdminPanelMiniLogo->getImageUrl() }}" alt="logo" />
            </a>
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-top">

        <ul class="navbar-nav ms-auto">

            {{-- Language --}}
            @include('hhh.BackOffice.partials._language_dropdown')
            {{-- Language END --}}

            {{-- Category --}}
            {{-- <li class="nav-item dropdown d-none d-lg-block">
                <a class="nav-link dropdown-bordered dropdown-toggle dropdown-toggle-split" id="messageDropdown"
                    href="#" data-bs-toggle="dropdown" aria-expanded="false"> Select Category </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0"
                    aria-labelledby="messageDropdown">
                    <a class="dropdown-item py-3">
                        <p class="mb-0 font-weight-medium float-left">Select category</p>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item preview-item">
                        <div class="preview-item-content flex-grow py-2">
                            <p class="preview-subject ellipsis font-weight-medium text-light">Bootstrap Bundle </p>
                            <p class="fw-light small-text mb-0">This is a Bundle featuring 16 unique dashboards</p>
                        </div>
                    </a>
                    <a class="dropdown-item preview-item">
                        <div class="preview-item-content flex-grow py-2">
                            <p class="preview-subject ellipsis font-weight-medium text-light">Angular Bundle</p>
                            <p class="fw-light small-text mb-0">Everything you’ll ever need for your Angular projects
                            </p>
                        </div>
                    </a>
                    <a class="dropdown-item preview-item">
                        <div class="preview-item-content flex-grow py-2">
                            <p class="preview-subject ellipsis font-weight-medium text-light">VUE Bundle</p>
                            <p class="fw-light small-text mb-0">Bundle of 6 Premium Vue Admin Dashboard</p>
                        </div>
                    </a>
                    <a class="dropdown-item preview-item">
                        <div class="preview-item-content flex-grow py-2">
                            <p class="preview-subject ellipsis font-weight-medium text-light">React Bundle</p>
                            <p class="fw-light small-text mb-0">Bundle of 8 Premium React Admin Dashboard</p>
                        </div>
                    </a>
                </div>
            </li> --}}
            {{-- Category END --}}

            {{-- Search --}}
            <li class="nav-item">
                <form class="search-form" action="{{ SitePublicRoutesEnum::Search->route() }}" method="GET">
                    <i class="icon-search"></i>
                    <input type="search" name="keyword" class="form-control" placeholder="@lang('thisApp.placeholder.SearchHere')" title="Search here">
                </form>
            </li>
            {{-- Search END --}}

            @if (Auth::check())
                @php
                    $user = Auth::user();
                @endphp

                {{-- Messages --}}
                {{-- @include('hhh.General.partials._messagesDropdown') --}}
                {{-- Messages END --}}

                {{-- Notifications --}}
                @include('hhh.General.partials._notificationsDropdown')
                {{-- Notifications END --}}

                {{-- User --}}
                @php
                    $personnel = App\Models\BackOffice\PersonnelProfile::first();
                    $rolesTableEnum = App\Enums\Database\Tables\RolesTableEnum::class;
                    $personnelExtrasTableEnum = App\Enums\Database\Tables\PersonnelExtrasTableEnum::class;

                    $personnelExtra = $personnel
                        ->PersonnelExtra()
                        ->select($personnelExtrasTableEnum::FirstName->dbName(), $personnelExtrasTableEnum::LastName->dbName())
                        ->first();

                    $personnelProfileImage = $personnel->PhotoUrl;

                    $personnelName = ucwords($personnelExtra[$personnelExtrasTableEnum::FirstName->dbName()] . ' ' . $personnelExtra[$personnelExtrasTableEnum::LastName->dbName()]);
                    $personnelRole = strtoupper($personnel->role[$rolesTableEnum::DisplayName->dbName()]);

                @endphp

                <li class="nav-item dropdown d-lg-block user-dropdown">
                    <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img class="img-xs rounded-circle" src="{{ $personnelProfileImage }}" alt="Profile image"> </a>

                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                        <div class="dropdown-header text-center">
                            <img class="img-md rounded-circle" src="{{ $personnelProfileImage }}" alt="Profile image">
                            <p class="mb-1 mt-3 font-weight-semibold">{{ $personnelName }}</p>
                            <p class="fw-light text-muted mb-0">{{ $personnelRole }}</p>
                        </div>

                        <a class="dropdown-item" href="{{ AdminPublicRoutesEnum::Profile->route() }}">
                            <i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>
                            @lang('general.MyProfile')
                        </a>

                        @can('viewAny', App\Policies\BackOffice\AccessControl\UnderConstructionPolicy::class)
                            <a class="dropdown-item"><i
                                    class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i>
                                Messages
                                <span class="badge badge-pill badge-danger">1</span>
                            </a>
                            <a class="dropdown-item"><i
                                    class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i>
                                Activity</a>
                            <a class="dropdown-item"><i
                                    class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
                        @endcan

                        {{-- logout --}}
                        <a class="dropdown-item" href="{{ route('logout') }}"><i
                                class="dropdown-item-icon mdi mdi-logout text-primary me-2"></i> @lang('auth.custom.SignOut')</a>
                        {{-- logout END --}}

                    </div>
                </li>
                {{-- User END --}}
            @else
                {{-- Login Botton --}}
                <li>
                    <a type="button" class="btn btn-outline-primary btn-fw ms-2"
                        href="{{ route('Auth.SSO.Betcart.Redirect', ['sessionId' => Session::getId()]) }}">@lang('bo_navbar.Login')</a>
                </li>
                {{-- Login Botton END --}}
            @endif

        </ul>
    </div>
</nav>
