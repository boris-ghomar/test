@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Personnel.Personnel'))
@section('workSpace_Title', __('bo_sidebar.Personnel.Personnel'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Personnel'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Personnel.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Personnel.Personnel')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.PersonnelManagement.Personnel.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.PersonnelManagement.Personnel.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.PersonnelManagement.Personnel.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.PersonnelManagement.Personnel.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.PersonnelManagement.Personnel.custom_js')
@endsection
