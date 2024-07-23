@extends('hhh.Site.super_main')

@section('title', __('bo_navbar.Notifications.Notifications'))
@section('workSpace_Title', __('bo_navbar.Notifications.Notifications'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Notifications'))

@section('breadcrumb_items')
    {{-- <li class="breadcrumb-item"><a href="#">@lang('bo_navbar.Notifications.Notifications')</a></li> --}}
@endsection

@section('breadcrumb_item_active')
    {{-- @lang('bo_navbar.Notifications.Notifications') --}}
@endsection

@section('page_header')
    @include('hhh.Site.partials._page-header')
@endsection

@section('extraPlugin_css')
    @include('hhh.Site.pages.Notifications.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.Notifications.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.Notifications.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.Notifications.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.Notifications.custom_js')
@endsection
