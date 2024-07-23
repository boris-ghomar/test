@extends('back_office.main')

@section('title', 'Registration Form')

@section('extraBodyClasses', '')

@section('extraPlugin_css')
@endsection


@section('custom_css')
@endsection


@section('content')

    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
            <div class="row flex-grow">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left p-5">
                        <div class="brand-logo">
                            <img src="{{ url('back_office/assets_hhh/images/logo.svg') }}">
                        </div>
                        <h4>@lang('auth.custom.Registration_Title')</h4>
                        <h6 class="font-weight-light">@lang('auth.custom.Registration_Notice')</h6>
                        <form class="pt-3">
                            <div class="form-group">
                                <input type="text" class="form-control form-control-lg" id="exampleInputUsername1"
                                    placeholder="@lang('auth.custom.placeholder_Username')">
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control form-control-lg" id="exampleInputEmail1"
                                    placeholder="@lang('auth.custom.placeholder_Email')">
                            </div>
                            <div class="form-group">
                                <select class="form-control form-control-lg" id="exampleFormControlSelect2">
                                    <option>@lang('auth.custom.placeholder_Country')</option>
                                    <option>United States of America</option>
                                    <option>United Kingdom</option>
                                    <option>India</option>
                                    <option>Germany</option>
                                    <option>Argentina</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control form-control-lg" id="exampleInputPassword1"
                                    placeholder="@lang('auth.custom.placeholder_Password')">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control form-control-lg"
                                    id="exampleInputConfirmPassword1" placeholder="@lang('auth.custom.placeholder_ConfirmPassword')">
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <label class="form-check-label text-muted">
                                        <input type="checkbox" class="form-check-input"> @lang('auth.custom.Registration_Agreement')
                                    </label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn"
                                    href="../../index.html">@lang('auth.custom.SignUp')</a>
                            </div>
                            <div class="text-center mt-4 font-weight-light"> @lang('auth.custom.Registration_HaveAccount') <a href="login.html"
                                    class="text-primary">@lang('auth.custom.Login')</a>
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
