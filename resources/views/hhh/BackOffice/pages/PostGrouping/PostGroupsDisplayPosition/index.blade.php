@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.PostGrouping.GroupsDisplayPosition'))
@section('workSpace_Title', __('bo_sidebar.PostGrouping.GroupsDisplayPosition'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.PostGrouping'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.PostGrouping.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.PostGrouping.GroupsDisplayPosition')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.PostGrouping.PostGroupsDisplayPosition.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.PostGrouping.PostGroupsDisplayPosition.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.PostGrouping.PostGroupsDisplayPosition.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.PostGrouping.PostGroupsDisplayPosition.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.PostGrouping.PostGroupsDisplayPosition.custom_js')
@endsection
