@extends('hhh.Site.pages.auth.super_auth')

@section('title', __('auth_site.custom.ForgotPasswordForm.PageTile') . '-' . __('general.Mobile'))

@section('extraBodyClasses', '')

@section('extraPlugin_css')
@endsection


@section('custom_css')
@endsection


@section('content')

    {{-- --------- Language ------------ --}}
    {{-- @include('hhh.BackOffice.partials._language_dropdown') --}}
    {{-- --------- Language END ------------ --}}

    <div class="container-fluid page-body-wrapper full-page-wrapper" style="min-height:80vh;max-width:1000px;">
        <div class="content-wrapper d-flex align-items-center auth px-0">

            <div class="row w-100 mx-0">

                <div class="col-lg-6 mx-auto">

                    <div class="auth-form-dark text-left py-5 px-sm-5">
                        <div class="brand-logo">
                            <img src="{{ AppSettingsEnum::CommunityBigLogo->getImageUrl() }}">
                        </div>
                        <div class="my-2 d-flex">
                            <h4>@lang('auth_site.custom.ForgotPasswordForm.1thTitle')</h4>
                        </div>
                        <div class="my-2 d-flex justify-content-start text-justify">
                            <h6 class="font-weight-light">@lang('auth_site.custom.ForgotPasswordForm.attempByMobile.notice')</h6>
                        </div>
                        <h6 class="d-flex text-mute">@lang('auth_site.custom.ForgotPasswordForm.attempByMobile.mobileDescr')</h6>
                        <form class="pt-3" method="POST"
                            action="{{ SitePublicRoutesEnum::ForgotPasswordBetconstruct->route() }}">
                            @csrf

                            <div class="form-group">

                                <label for="mobile_number" class="d-flex">@lang('auth_site.custom.MobileNumber')</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                                        <span class="input-group-text bg-transparent border-0">
                                            <i class="fa-solid fa-mobile-screen-button text-primary"
                                                style="font-size: 1.2rem"></i>
                                        </span>
                                    </div>
                                    <input class="ltr form-control form-control-lg border-0 no-arrows" id="Mobile"
                                        placeholder="@lang('auth_site.custom.placeholder_Mobile')" type="number" name="Mobile" required autofocus
                                        autocomplete="mobile"
                                        minlength="{{ config('hhh_config.validation.minLength.email') }}"
                                        maxlength="{{ config('hhh_config.validation.maxLength.email') }}"
                                        value="{{ old('Mobile') }}">

                                </div>
                            </div>


                            @include('hhh.widgets.messages.ShowFormResultMessages')

                            <div class="mt-3">
                                <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                    type="submit">@lang('auth_site.custom.ForgotPasswordForm.SendVerificationCode')</button>
                            </div>

                            <div class="my-4 d-flex">
                                <a class="auth-link text-gray" href="{{ SitePublicRoutesEnum::defaultLogin()->route() }}">
                                    @lang('auth_site.custom.ForgotPasswordForm.ReturnToLoginPage')
                                </a>
                            </div>


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
@endsection


@section('custom_js')
@endsection
