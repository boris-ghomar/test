@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Domains.HoldersAccounts'))
@section('workSpace_Title', __('bo_sidebar.Domains.HoldersAccounts'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.domains'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Domains.Domains')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Domains.HoldersAccounts')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Domains.DomainsHoldersAccounts.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Domains.DomainsHoldersAccounts.custom_css')
@endsection


@section('content')
    @include('hhh.BackOffice.pages.Domains.DomainsHoldersAccounts.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Domains.DomainsHoldersAccounts.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Domains.DomainsHoldersAccounts.custom_js')
@endsection
