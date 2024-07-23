@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.PostGrouping.PostSpacesPermissions'))
@section('workSpace_Title', __('bo_sidebar.PostGrouping.PostSpacesPermissions'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.PostGrouping'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.PostGrouping.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.PostGrouping.PostSpacesPermissions')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.PostGrouping.PostSpacesPermissions.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.PostGrouping.PostSpacesPermissions.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.PostGrouping.PostSpacesPermissions.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.PostGrouping.PostSpacesPermissions.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.PostGrouping.PostSpacesPermissions.custom_js')
@endsection
