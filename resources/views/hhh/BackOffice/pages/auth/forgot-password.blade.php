@extends('hhh.BackOffice.pages.auth.super_auth')

@section('title', __('auth.custom.ForgotPasswordForm.PageTile'))

@section('extraBodyClasses', '')

@section('extraPlugin_css')
@endsection


@section('custom_css')
@endsection


@section('content')

    {{-- --------- Language ------------ --}}
    @include('hhh.BackOffice.partials._language_dropdown')
    {{-- --------- Language END ------------ --}}

    <div class="container-fluid page-body-wrapper full-page-wrapper" style="min-height:80vh;max-width:1000px;">
        <div class="content-wrapper d-flex align-items-center auth px-0">

            <div class="row w-100 mx-0">

                <div class="col-lg-6 mx-auto">

                    <div class="auth-form-dark text-left py-5 px-sm-5">
                        <div class="brand-logo">
                            <img src="{{ App\Enums\Settings\AppSettingsEnum::AdminPanelBigLogo->getImageUrl() }}">
                        </div>
                        <div class="my-2 d-flex">
                            <h4>@lang('auth.custom.ForgotPasswordForm.1thTitle')</h4>
                        </div>
                        <div class="my-2 d-flex justify-content-start text-justify">
                            <h6 class="font-weight-light">@lang('auth.custom.ForgotPasswordForm.2thTitle')</h6>
                        </div>
                        <form class="pt-3" method="POST" action="{{ App\Enums\Routes\AdminPublicRoutesEnum::ForgotPassword->route() }}">
                            @csrf

                            <div class="form-group">

                                <label for="Email" class="d-flex">@lang('auth.custom.Email')</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                                        <span class="input-group-text bg-transparent border-0">
                                            <i class="mdi mdi-email-outline text-primary"></i>
                                        </span>
                                    </div>
                                    <input class="ltr form-control form-control-lg border-0" id="email"
                                        placeholder="@lang('auth.custom.placeholder_Email')" type="email" name="email" required autofocus
                                        autocomplete="email"
                                        minlength="{{ config('hhh_config.validation.minLength.email') }}"
                                        maxlength="{{ config('hhh_config.validation.maxLength.email') }}"
                                        value="{{ old('email') }}">
                                </div>
                            </div>

                            @include('hhh.widgets.messages.ShowFormResultMessages')

                            <div class="mt-3">
                                <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                    type="submit">@lang('auth.custom.ForgotPasswordForm.EmailPasswordResetLink')</button>
                            </div>

                            <div class="my-2 d-flex">
                                <a class="auth-link text-gray" href="{{ App\Enums\Routes\AdminPublicRoutesEnum::Login->route() }}">
                                    @lang('auth.custom.ForgotPasswordForm.ReturnToLoginPage')
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
