@extends('hhh.BackOffice.super_main')

@section('title', config('app.name'))
@section('workSpace_Title', __('bo_sidebar.Dashboard'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.dashboard'))

@section('breadcrumb_items')
    {{-- <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Dashboard')</a></li> --}}
@endsection
@section('breadcrumb_item_active')
    {{-- @lang('bo_sidebar.Dashboard') --}}
@endsection

@section('container-extra', 'dashboard')

@php
    $resourceVersion = '?version=' . config('hhh_config.ResourceVersion');
@endphp

@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Dashboard.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Dashboard.custom_css')
@endsection


@section('content')
    @include('hhh.BackOffice.pages.Dashboard.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Dashboard.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Dashboard.custom_js')
@endsection
