@php
    if (!config('app.debug')) {
        function removeWhitespace($buffer)
        {
            return preg_replace('/>(\s)+</m', '><', $buffer);
            // return preg_replace('/\s+/', ' ', $buffer); // this has issue in text-area
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
    <link rel="shortcut icon" href="{{ AppSettingsEnum::AdminPanelFavicon->getImageUrl() }}" />
    <title>@yield('title')</title>
    @if (Auth::check())
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endif

    {{-- Necessary:js --}}
    <script src="{{ url('assets/general/js/app.js') . $version }}"></script>
    {{-- END Necessary:js --}}

    <!-- plugins:css -->
    {{-- <link rel="stylesheet" href="{{ url('assets/site/template/vendors/mdi/css/materialdesignicons.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ url('assets/site/template/vendors/ti-icons/css/themify-icons.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ url('assets/site/template/vendors/simple-line-icons/css/simple-line-icons.css') }}"> --}}
    <link rel="stylesheet" href="{{ url('assets/site/template/vendors/feather/feather.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/site/template/vendors/typicons/typicons.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/site/template/vendors/css/vendor.bundle.base.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/general/css/flag-icon-css/css/flag-icon.min.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/general/css/mdi/css/materialdesignicons.min.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/general/css/simple-line-icons/css/simple-line-icons.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/general/css/themify-icons/themify-icons.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/general/css/font-awesome-6.4/css/all.min.css') . $version }}">

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

    <link rel="stylesheet" href="{{ url('assets/general/css/fonts/farsi_fonts.css') . $version }}">
    @if (__('general.locale.direction') == 'rtl')
        <link rel="stylesheet" href="{{ url('assets/general/css/rtl_override.css') . $version }}">
    @endif
    <!-- endinject -->

</head>

{{-- <body class="@lang('general.locale.direction') sidebar-fixed"> --}}

<body class="@lang('general.locale.direction')">

    {{-- translation:js --}}

    {{-- Fallback lang --}}
    <script src="{{ url('assets/general/js/translation/trans-en.js') . $version }}"></script>

    {{-- Page locale lang --}}
    @if (!App::isLocale('en'))
        <script src="{{ url(sprintf('assets/general/js/translation/trans-%s.js', App::getlocale())) . $version }}"></script>
        <script>
            var translation = new Translation('{{ App::getlocale() }}');
        </script>
    @endif

    {{-- END translation:js --}}

    <div class="container-scroller @yield('container-extra')">
        <!-- partial:../../partials/_navbar.html -->
        @include('hhh.BackOffice.partials._navbar')
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:../../partials/_sidebar.html -->
            @include('hhh.BackOffice.partials._sidebar')
            <!-- partial -->
            <div class="main-panel">

                <div class="content-wrapper">
                    {{-- partial:partials/_page-header.blade.php --}}
                    @include('hhh.BackOffice.partials._page-header')
                    {{-- partial --}}

                    {{-- page content --}}
                    @yield('content')
                    {{-- page content END --}}

                </div>
                <!-- content-wrapper ends -->
                <!-- partial:../../partials/_footer.html -->
                @include('hhh.BackOffice.partials._footer')
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ url('assets/site/template/vendors/js/vendor.bundle.base.js') . $version }}"></script>
    <script src="{{ url('assets/site/template/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') . $version }}"></script>
    <script src="{{ url('assets/general/widgets/jquery-toast-plugin/toast_controller.js') . $version }}"></script>
    <!-- endinject -->

    <!-- Plugin js for this page -->
    @yield('extraPlugin_js')
    <!-- End plugin js for this page -->

    <!-- inject:js -->
    <script src="{{ url('assets/site/template/js/off-canvas.js') . $version }}"></script>
    <script src="{{ url('assets/site/template/js/hoverable-collapse.js') . $version }}"></script>
    <script src="{{ url('assets/site/template/js/template.js') . $version }}"></script>
    <script src="{{ url('assets/site/template/js/settings.js') . $version }}"></script>
    <script src="{{ url('assets/site/template/js/todolist.js') . $version }}"></script>
    <!-- endinject -->

    {{-- Javascript required variables  --}}
    <script>
        var locale = '{{ App::getlocale() }}';
        var direction = '{{ __('general.locale.direction') }}';
    </script>
    @include('hhh.widgets.requirements.KeepSessionViewSettings')
    {{-- Javascript required variables END  --}}

    <!-- Custom js for this page-->
    @yield('custom_js')
    <!-- End custom js for this page-->

</body>

</html>


@php
    if (!config('app.debug')) {
        ob_get_flush();
    }
@endphp
