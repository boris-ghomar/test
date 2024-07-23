<div class="page-header">

    @hasSection('workSpace_Title')
        <h4 class="page-title">

            @hasSection('workSpace_Icon')
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    {{-- <i class="mdi mdi-home"></i> --}}
                    <i class="@yield('workSpace_Icon')"></i>
                </span>
            @endif

            @yield('workSpace_Title')
        </h4>
    @endif

    @hasSection('breadcrumb_items')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                @yield('breadcrumb_items')
                <li class="breadcrumb-item active" aria-current="page"><span>@yield('breadcrumb_item_active')</span></li>
            </ol>
        </nav>
    @endif

</div>
