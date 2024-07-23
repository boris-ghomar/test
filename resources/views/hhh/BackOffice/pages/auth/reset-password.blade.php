@extends('hhh.BackOffice.pages.auth.super_auth')

@section('title', __('auth.custom.ResetPasswordForm.PageTile'))

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
                            <h4>@lang('auth.custom.ResetPasswordForm.1thTitle')</h4>
                        </div>
                        <div class="my-2 d-flex">
                            <h6 class="font-weight-light">@lang('auth.custom.ResetPasswordForm.2thTitle')</h6>
                        </div>
                        <form class="pt-3" method="POST" action="{{ App\Enums\Routes\AdminPublicRoutesEnum::ResetPasswordAttempt->route() }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                            <input type="hidden" name="email" value="{{ $request->email }}">

                            <div class="form-group">
                                <label for="password" class="d-flex">@lang('passwords.newPassword')</label>
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
                                        placeholder="@lang('auth.custom.placeholder_Password')" type="password" name="password" required autofocus
                                        autocomplete="new-password"
                                        minlength="{{ config('hhh_config.validation.minLength.password') }}"
                                        maxlength="{{ config('hhh_config.validation.maxLength.password') }}"
                                        value="{{ old('password') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation" class="d-flex">@lang('passwords.newPasswordConfirmation')</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                                        <span class="input-group-text bg-transparent border-0">

                                            <i id="password_confirm_hidden" class="mdi mdi-eye text-primary"
                                                onclick="togglePasswordVisibility('password_confirmation','password_confirm_show','password_confirm_hidden')"></i>
                                            <i id="password_confirm_show" class="mdi mdi-eye-off text-primary d-none"
                                                onclick="togglePasswordVisibility('password_confirmation','password_confirm_show','password_confirm_hidden')"></i>


                                        </span>
                                    </div>
                                    <input class="ltr form-control form-control-lg border-0" id="password_confirmation"
                                        placeholder="@lang('auth.custom.placeholder_ConfirmPassword')" type="password" name="password_confirmation"
                                        required autocomplete="new-password"
                                        minlength="{{ config('hhh_config.validation.minLength.password') }}"
                                        maxlength="{{ config('hhh_config.validation.maxLength.password') }}"
                                        value="{{ old('password_confirmation') }}">
                                </div>
                            </div>

                            @include('hhh.widgets.messages.ShowFormResultMessages')

                            <div class="mt-3">
                                <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                    type="submit">@lang('auth.custom.ResetPasswordForm.SaveNewPassword')</button>
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
