@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Posts.MenuTitle') . '-' . __('bo_sidebar.Posts.Articles') . '-' .
    __('general.buttons.FullEdit'))
@section('workSpace_Title', __('bo_sidebar.Posts.Articles'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Posts'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a
            href="{{ \App\Enums\Routes\AdminRoutesEnum::Posts_Articles->route() }}">@lang('bo_sidebar.Posts.MenuTitle')</a></li>
    <li class="breadcrumb-item"><a
            href="{{ \App\Enums\Routes\AdminRoutesEnum::Posts_Articles->route() }}">@lang('bo_sidebar.Posts.Articles')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('PagesContent_PostForm.cardTitleEdit')
@endsection

@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.FullEdit.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.FullEdit.custom_css')
@endsection


@section('content')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.FullEdit.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.FullEdit.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Posts.ArticlePosts.FullEdit.custom_js')
@endsection
