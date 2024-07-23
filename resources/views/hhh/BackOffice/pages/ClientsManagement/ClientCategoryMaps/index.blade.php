@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.BetconstructClients.CategoriesMaps'))
@section('workSpace_Title', __('bo_sidebar.BetconstructClients.CategoriesMaps'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Clients'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.BetconstructClients.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.BetconstructClients.CategoriesMaps')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientCategoryMaps.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientCategoryMaps.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientCategoryMaps.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientCategoryMaps.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientCategoryMaps.custom_js')
@endsection
