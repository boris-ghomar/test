@extends('hhh.Site.pages.auth.super_auth')

@section('title', __('auth_site.custom.ForgotPasswordForm.PageTile') . '-' .
    __('auth_site.custom.ForgotPasswordForm.Authentication'))

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
                            <h6 class="font-weight-light">@lang('auth_site.custom.ForgotPasswordForm.verification.notice')</h6>
                        </div>
                        <form class="pt-3" method="POST"
                            action="{{ SitePublicRoutesEnum::ForgotPasswordVerifiyAttemp->route() }}">
                            @csrf

                            <div class="form-group">

                                <label for="VerificationCode" class="d-flex">@lang('auth_site.custom.VerificationCode')</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                                        <span class="input-group-text bg-transparent border-0">
                                            <i class="fa-solid fa-badge-check text-primary" style="font-size: 1.2rem"></i>
                                        </span>
                                    </div>
                                    <input class="ltr form-control form-control-lg border-0 no-arrows" id="VerificationCode"
                                        placeholder="@lang('auth_site.custom.VerificationCode')" type="number" name="VerificationCode" required
                                        autofocus value="{{ old('VerificationCode') }}">
                                </div>
                            </div>

                            <p class="text-success">{{ $nextVerificationTime }}</p>

                            @include('hhh.widgets.messages.ShowFormResultMessages')

                            <div class="mt-3">
                                <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                    type="submit">@lang('general.OK')</button>
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
