@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Settings.MenuTitle') . '-' . __('bo_sidebar.Settings.TechnicalSettings'))
@section('workSpace_Title', __('bo_sidebar.Settings.TechnicalSettings'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Settings'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Settings.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Settings.TechnicalSettings')
@endsection

@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.custom_css')
@endsection


@section('content')
    @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.custom_js')
@endsection
