@extends('hhh.Site.super_main')

@section('title', __('bo_navbar.Referral.ReferralPanel'))
@section('workSpace_Title', __('bo_navbar.Referral.ReferralPanel'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Referral'))

@section('breadcrumb_items')
    {{-- <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Dashboard')</a></li> --}}
@endsection
@section('breadcrumb_item_active')
    {{-- @lang('bo_sidebar.Dashboard') --}}
@endsection

@section('container-extra', 'referral_panel')

@php
    $resourceVersion = '?version=' . config('hhh_config.ResourceVersion');
@endphp

@section('extraPlugin_css')
    @include('hhh.Site.pages.ReferralPanel.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.ReferralPanel.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.ReferralPanel.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.ReferralPanel.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.ReferralPanel.custom_js')
@endsection
