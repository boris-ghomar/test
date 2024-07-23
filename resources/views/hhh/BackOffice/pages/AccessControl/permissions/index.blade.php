@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.AccessControl.Permissions'))
@section('workSpace_Title', __('bo_sidebar.AccessControl.Permissions'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.AccessControl'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.AccessControl.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.AccessControl.Permissions')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.AccessControl.permissions.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.AccessControl.permissions.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.AccessControl.permissions.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.AccessControl.permissions.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.AccessControl.permissions.custom_js')
@endsection
