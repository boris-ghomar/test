@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Settings.MenuTitle') . '-' . __('bo_sidebar.Settings.GeneralSettings'))
@section('workSpace_Title', __('bo_sidebar.Settings.GeneralSettings'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Settings'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Settings.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Settings.GeneralSettings')
@endsection

@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.custom_css')
@endsection


@section('content')
    @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.custom_js')
@endsection
