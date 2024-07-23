@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Referral.ReferralSessions'))
@section('workSpace_Title', __('bo_sidebar.Referral.ReferralSessions'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Referral'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Referral.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Referral.ReferralSessions')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Referral.ReferralSessions.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Referral.ReferralSessions.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.Referral.ReferralSessions.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Referral.ReferralSessions.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Referral.ReferralSessions.custom_js')
@endsection
