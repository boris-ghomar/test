@extends('hhh.Site.pages.auth.super_auth')

@section('title', __('auth_site.custom.LoginForm.PageTile'))

@section('extraPlugin_css')
@endsection


@section('custom_css')
    <style>
        .grecaptcha-badge {
            visibility: hidden;
        }
    </style>
@endsection


@section('content')

    {{-- --------- Language ------------ --}}
    {{-- @include('hhh.Site.partials._language_dropdown') --}}
    {{-- --------- Language END ------------ --}}

    <div class="container-fluid page-body-wrapper full-page-wrapper" style="min-height:80vh;max-width:1000px;">
        <div class="content-wrapper d-flex align-items-center auth px-0">

            <div class="row w-100 mx-0">

                <div class="col-lg-6 mx-auto">

                    <div class="auth-form-dark text-left py-5 px-sm-5">
                        <div class="brand-logo">
                            <a href="{{ SitePublicRoutesEnum::MainPage->url() }}">
                                <img src="{{ AppSettingsEnum::CommunityBigLogo->getImageUrl() }}">
                            </a>
                        </div>
                        <div class="my-2 d-flex">
                            <h4>@lang('auth_site.custom.LoginForm.1thTitle')</h4>
                        </div>
                        <div class="my-2 d-flex">
                            <h6 class="font-weight-light">@lang('auth_site.custom.LoginForm.2thTitle')</h6>
                        </div>

                        <form id="login_form" class="pt-3" method="POST"
                            action="{{ App\Enums\Routes\SitePublicRoutesEnum::LoginBetconstructApi->route() }}">
                            @csrf

                            <input type="hidden" id="{{ $reCAPTCHAFieldId }}" name="{{ $reCAPTCHAFieldId }}"
                                value="">

                            <div class="form-group">
                                <label for="username" class="d-flex">@lang('auth_site.custom.Username')</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                                        <span class="input-group-text bg-transparent border-0">
                                            <i class="mdi mdi-account-outline text-primary"></i>
                                        </span>
                                    </div>
                                    <input class="ltr form-control form-control-lg border-0" id="username"
                                        placeholder="@lang('auth_site.custom.placeholder_Username')" type="text" name="username" required autofocus
                                        autocomplete="username"
                                        minlength="{{ config('hhh_config.validation.minLength.username') }}"
                                        maxlength="{{ config('hhh_config.validation.maxLength.username') }}"
                                        value="{{ old('username') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="d-flex">@lang('auth_site.custom.Password')</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                                        <span class="input-group-text bg-transparent border-0">
                                            <i id="password_hidden" class="mdi mdi-eye text-primary"
                                                onclick="togglePasswordVisibility('password','password_show','password_hidden')"></i>
                                            <i id="password_show" class="mdi mdi-eye-off text-primary d-none"
                                                onclick="togglePasswordVisibility('password','password_show','password_hidden')"></i>
                                        </span>
                                    </div>
                                    <input class="ltr form-control form-control-lg border-0" id="password"
                                        placeholder="@lang('auth_site.custom.placeholder_Password')" type="password" name="password" required
                                        autocomplete="current-password"
                                        minlength="{{ config('hhh_config.validation.minLength.password') }}"
                                        maxlength="{{ config('hhh_config.validation.maxLength.password') }}"
                                        value="{{ old('password') }}">
                                </div>
                            </div>

                            @include('hhh.widgets.messages.ShowFormResultMessages')

                            <div class="mt-3">
                                <button type="submit"
                                    class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                    onclick="btnSumbitClicked();">@lang('auth_site.custom.SignIn')</button>
                            </div>
                            <div class="my-2 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <label class="form-check-label text-muted">
                                        <input type="checkbox" id="remember" name="remember" class="form-check-input"
                                            @if (old('remember', true)) checked value ='on' @else value ='off' @endif
                                            onchange="javascript: this.value =='on' ? this.value ='off' : this.value ='on';">

                                        @lang('auth_site.custom.LoginForm.KeepMeSignedIn') </label>
                                </div>
                            </div>
                            <div class="my-3 d-flex">
                                <a class="auth-link text-gray"
                                    href="{{ SitePublicRoutesEnum::ForgotPasswordBetconstruct->url() }}">
                                    @lang('auth_site.custom.LoginForm.ForgotPassword')
                                </a>
                            </div>

                            {{--
                            <div class="mb-2">
                                <button type="button" class="btn btn-block btn-facebook auth-form-btn">
                                    <i class="mdi mdi-facebook mr-2"></i>Connect using facebook </button>
                            </div>
                            --}}
                            @if (AppSettingsEnum::CommunityRegistrationIsActive->getValue(false))
                                <div class="text-center mt-4 font-weight-light">
                                    @lang('auth_site.custom.LoginForm.DoNotHaveAnAccount')
                                    <a href="https://www.betcart.com"
                                        class="text-primary">@lang('auth_site.custom.SignUp')</a>
                                </div>
                            @endif

                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->

@endsection


@section('extraPlugin_js')
    <script type="text/javascript">
        var modal_loading = new Modal_loading();
    </script>
    @if (!is_null($googleRecaptchSiteKey))
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render={{ $googleRecaptchSiteKey }}" async
            defer></script>
        <script>
            function btnSumbitClicked() {
                window.event.preventDefault();
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ $googleRecaptchSiteKey }}', {
                        action: 'submit'
                    }).then(function(token) {
                        document.getElementById('{{ $reCAPTCHAFieldId }}').value = token;
                        document.getElementById('login_form').submit();
                        modal_loading.show();
                    });
                });
            }
        </script>
    @else
        <script>
            function btnSumbitClicked() {
                window.event.preventDefault();
                modal_loading.show();
                document.getElementById('login_form').submit();
            }
        </script>
    @endif
@endsection


@section('custom_js')
@endsection
