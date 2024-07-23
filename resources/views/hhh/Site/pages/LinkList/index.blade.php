@extends('hhh.Site.super_main')

@section('title', __('thisApp.linkList.pageTitle', ['title' => $pageTitle]))
@section('workSpace_Icon', '')
@section('MetaTags')
    {!! $metaTags !!}
@endsection

{{-- @section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Market.Market')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Market.Currencies')
@endsection --}}


@section('extraPlugin_css')
    @include('hhh.Site.pages.LinkList.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.LinkList.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.LinkList.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.LinkList.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.LinkList.custom_js')
@endsection
