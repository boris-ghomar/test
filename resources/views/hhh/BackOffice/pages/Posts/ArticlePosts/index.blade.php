@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Posts.Articles'))
@section('workSpace_Title', __('bo_sidebar.Posts.Articles'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Posts'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Posts.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Posts.Articles')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.custom_js')
@endsection
