@extends('hhh.Site.super_main')

@section('title', __('bo_navbar.UserProfile.Profile'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.userProfile'))

@section('breadcrumb_items')
    {{-- <li class="breadcrumb-item"><a href="#">@lang('bo_navbar.UserProfile.Profile')</a></li> --}}
@endsection
@section('breadcrumb_item_active')
    {{-- @lang('bo_sidebar.Edit') --}}
@endsection

@php
    $resourceVersion = '?version=' . config('hhh_config.ResourceVersion');
@endphp


@section('extraPlugin_css')
    @include('hhh.Site.pages.UserBetconstructProfile.edit.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.UserBetconstructProfile.edit.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.UserBetconstructProfile.edit.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.UserBetconstructProfile.edit.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.UserBetconstructProfile.edit.custom_js')
@endsection
