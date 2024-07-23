@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.BetconstructClients.ClientTrustScores'))
@section('workSpace_Title', __('bo_sidebar.BetconstructClients.ClientTrustScores'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Clients'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.BetconstructClients.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.BetconstructClients.ClientTrustScores')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientTrustScores.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientTrustScores.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientTrustScores.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientTrustScores.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.ClientsManagement.ClientTrustScores.custom_js')
@endsection
