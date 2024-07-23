@extends('hhh.Site.super_main')

@section('title', __('thisApp.CustomPages.IpRestriction.Title'))


@section('extraPlugin_css')
    @include('hhh.Site.pages.CustomPages.IpRestriction.ShowLink.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.CustomPages.IpRestriction.ShowLink.custom_css')
@endsection

@section('content')
    @include('hhh.Site.pages.CustomPages.IpRestriction.ShowLink.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.CustomPages.IpRestriction.ShowLink.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.CustomPages.IpRestriction.ShowLink.custom_js')
@endsection
