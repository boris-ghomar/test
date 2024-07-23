@php
    if (!config('app.debug')) {
        function removeWhitespace($buffer)
        {
            return preg_replace('/\s+/', ' ', $buffer);
        }
        ob_start('removeWhitespace');
    }

    $version = '?version='. config('hhh_config.ResourceVersion');
@endphp

<!DOCTYPE html>
<html lang="{{ App::getlocale() }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ App\Enums\Settings\AppSettingsEnum::AdminPanelFavicon->getImageUrl() }}" />
    <title>@yield('title')</title>

    {{-- Necessary:js --}}
    <script src="{{ url('assets/general/js/app.js') . $version }}"></script>
    {{-- END Necessary:js --}}


    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ url('assets/site/template/vendors/feather/feather.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/site/template/vendors/mdi/css/materialdesignicons.min.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/site/template/vendors/ti-icons/css/themify-icons.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/general/css/flag-icon-css/css/flag-icon.min.css') . $version }}">
    <!-- endinject -->

    {{-- Plugin css for this page --}}
    @yield('extraPlugin_css')
    {{-- End plugin css for this page --}}

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('assets/site/template/css/vertical-layout-dark/style.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/site/resources/css/app.css') . $version }}">

    {{-- custom css for this page --}}
    @yield('custom_css')
    {{-- End custom css for this page --}}

    @if (__('general.locale.direction') == 'rtl')
        <link rel="stylesheet" href="{{ url('assets/general/css/fonts/farsi_fonts.css') . $version }}">
        <link rel="stylesheet" href="{{ url('assets/general/css/rtl_override.css') . $version }}">
    @endif
    <!-- endinject -->

</head>

{{-- <body class="@lang('general.locale.direction') sidebar-fixed"> --}}

<body class="@lang('general.locale.direction')">

    <div class="container-scroller">
        <!-- partial -->
        <div class="container-fluid page-body-wrapper full-page-wrapper flex-column">
            <!-- partial -->
            <div class="content-wrapper ">
                @yield('content')
            </div>
            <!-- content-wrapper ends -->

            <!-- partial:../../partials/_footer.html -->
            @include('hhh.BackOffice.partials._footer')
            <!-- partial -->

            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ url('assets/site/template/vendors/js/vendor.bundle.base.js') . $version }}"></script>
    <!-- endinject -->

    <!-- Plugin js for this page -->
    @yield('extraPlugin_js')
    <!-- End plugin js for this page -->

    <!-- inject:js -->
    <script src="{{ url('assets/site/template/js/template.js') . $version }}"></script>
    <!-- endinject -->

    <!-- Custom js for this page-->
    @yield('custom_js')
    <!-- End custom js for this page-->

    {{-- Javascript required variables  --}}
    <script>
        var locale = '{{ App::getlocale() }}';
        var direction = '{{ __('general.locale.direction') }}';
    </script>
    {{-- Javascript required variables END  --}}

</body>

</html>


@php
    if (!config('app.debug')) {
        ob_get_flush();
    }
@endphp
