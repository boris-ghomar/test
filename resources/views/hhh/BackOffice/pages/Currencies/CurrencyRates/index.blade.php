@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Currencies.CurrencyRates'))
@section('workSpace_Title', __('bo_sidebar.Currencies.CurrencyRates'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Currencies'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Currencies.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Currencies.CurrencyRates')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Currencies.CurrencyRates.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Currencies.CurrencyRates.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.Currencies.CurrencyRates.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Currencies.CurrencyRates.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Currencies.CurrencyRates.custom_js')
@endsection
