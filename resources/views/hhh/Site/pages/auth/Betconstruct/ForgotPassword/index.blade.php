@extends('hhh.Site.pages.auth.super_auth')

@section('title', __('auth_site.custom.ForgotPasswordForm.PageTile'))

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
                            <a href="{{ SitePublicRoutesEnum::MainPage->url() }}">
                                <img src="{{ AppSettingsEnum::CommunityBigLogo->getImageUrl() }}">
                            </a>
                        </div>
                        <div class="my-2 d-flex">
                            <h4>@lang('auth_site.custom.ForgotPasswordForm.1thTitle')</h4>
                        </div>
                        <div class="my-2 d-flex justify-content-start text-justify">
                            <h6 class="font-weight-light">@lang('auth_site.custom.ForgotPasswordForm.2thTitle')</h6>
                        </div>
                        <form class="pt-3" method="GET"
                            action="{{ SitePublicRoutesEnum::ForgotPasswordRecoveryMethod->route() }}">

                            @php $attrName = "RecoveryMethod" @endphp
                            @include('hhh.widgets.form.radio_group', [
                                'attrName' => $attrName,
                                'label' => trans(
                                    'auth_site.custom.ForgotPasswordForm.index.' . $attrName . '.name'),
                                'notice' => trans(
                                    'auth_site.custom.ForgotPasswordForm.index.' . $attrName . '.notice'),
                                'collection' => $recoveryMethods,
                                'selectedItem' => $defaultRecoveryMethod,
                            ])

                            @include('hhh.widgets.messages.ShowFormResultMessages')

                            <div class="mt-3">
                                <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                    type="submit">@lang('general.buttons.ok')</button>
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
