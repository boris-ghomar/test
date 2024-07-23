@extends('hhh.Site.super_main')

@section('title', __('auth_site.custom.AccessDenied'))

@section('extraPlugin_css')
    @include('hhh.Site.pages.PostShow.PrivatePostsError.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.PostShow.PrivatePostsError.custom_css')
@endsection

@section('content')
    @include('hhh.Site.pages.PostShow.PrivatePostsError.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.PostShow.PrivatePostsError.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.PostShow.PrivatePostsError.custom_js')
@endsection
