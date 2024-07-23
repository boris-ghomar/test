@extends('hhh.Site.super_main')

@section('title', $post[$TabelEnum::Title->dbName()])
@section('workSpace_Icon', '')
@section('MetaTags')
    {!! SeoMetaTagsEnum::MetaDescription->getHtmlTag($post[$TabelEnum::MetaDescription->dbName()]) !!}
    {!! SeoMetaTagsEnum::MetaRobots->getHtmlTag() !!}
    {!! $post->CanonicalUrl !!}
    {!! $autorMeta !!}
@endsection

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Market.Market')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Market.Currencies')
@endsection


@section('extraPlugin_css')
    @include('hhh.Site.pages.PostShow.Article.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.PostShow.Article.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.PostShow.Article.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.PostShow.Article.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.PostShow.Article.custom_js')
@endsection
