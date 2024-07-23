@extends('hhh.Site.super_main')

@section('title', __('thisApp.SearchResults'))

@section('MetaTags')
    {!! $metaTags !!}
@endsection


{{-- @section('workSpace_Icon', '')

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Market.Market')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Market.Currencies')
@endsection --}}


@section('extraPlugin_css')
    @include('hhh.Site.pages.SearchPage.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.SearchPage.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.SearchPage.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.SearchPage.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.SearchPage.custom_js')
@endsection
