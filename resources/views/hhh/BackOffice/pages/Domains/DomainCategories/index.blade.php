@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Domains.Categories'))
@section('workSpace_Title', __('bo_sidebar.Domains.Categories'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.domains'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Domains.Domains')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Domains.Categories')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Domains.DomainCategories.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Domains.DomainCategories.custom_css')
@endsection


@section('content')
    @include('hhh.BackOffice.pages.Domains.DomainCategories.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Domains.DomainCategories.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Domains.DomainCategories.custom_js')
@endsection
