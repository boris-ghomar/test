@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Referral.ClientCustomSettings'))
@section('workSpace_Title', __('bo_sidebar.Referral.ClientCustomSettings'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Referral'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Referral.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Referral.ClientCustomSettings')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Referral.ClientCustomSettings.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Referral.ClientCustomSettings.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.Referral.ClientCustomSettings.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Referral.ClientCustomSettings.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Referral.ClientCustomSettings.custom_js')
@endsection
