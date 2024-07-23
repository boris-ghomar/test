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
    <link rel="shortcut icon" href="{{ App\Enums\Settings\AppSettingsEnum::CommunityFavicon->getImageUrl() }}" />
    <title>{{ config('app.name') }}</title>

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('assets/site/template/css/vertical-layout-dark/style.css') . $version }}">
    <link rel="stylesheet" href="{{ url('assets/site/resources/css/app.css') . $version }}">

    @if (__('general.locale.direction') == 'rtl')
        <link rel="stylesheet" href="{{ url('assets/general/css/fonts/farsi_fonts.css') . $version }}">
        <link rel="stylesheet" href="{{ url('assets/general/css/rtl_override.css') . $version }}">
    @endif
    <!-- endinject -->

</head>

<body class="">

    <div class="container-scroller">

        <div class="container-fluid page-body-wrapper full-page-wrapper">

            <div class="content-wrapper d-flex align-items-center text-center error-page">

                <div class="row flex-grow">

                    <div class="col-lg-7 mx-auto ">

                        <div class="brand-logo mb-5">
                            <img class="brand-logo-errorpage"
                                src="{{ App\Enums\Settings\AppSettingsEnum::CommunityBigLogo->getImageUrl() }}">
                        </div>

                        <div class="row align-items-center d-flex flex-row text-white-50">
                            <div class="col-lg-6 text-lg-right pr-lg-4">
                                <h1 class="display-1 mb-0">{{ $statusCode }} </h1>
                            </div>
                            <div class="col-lg-6 error-page-divider text-lg-left pl-lg-4">
                                <h2>SORRY!</h2>
                                <h3 class="fw-light text-uppercase">{{ $statusMessage }}</h3>
                            </div>
                        </div>
                        <div class="row mt-5 @lang('general.locale.direction')" style='font-family:"Manrope-regular"'>
                            <div class="col-12 text-center mt-xl-2">
                                <p class="text-white-75 font-weight-medium">
                                    @foreach ($messages as $message)
                                        @php $message = str_replace("\n", "<br>", $message); @endphp
                                        {!! $message !!}
                                        <br>
                                    @endforeach
                                </p>
                            </div>
                        </div>

                    </div>

                    {{-- @include('hhh.BackOffice.partials._footer') --}}
                </div>

            </div>
        </div>
    </div>
    <!-- container-scroller -->

</body>

</html>


@php
    if (!config('app.debug')) {
        ob_get_flush();
    }
@endphp
