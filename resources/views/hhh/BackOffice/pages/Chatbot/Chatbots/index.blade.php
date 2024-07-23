@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Chatbots.ChatbotsManagement'))
@section('workSpace_Title', __('bo_sidebar.Chatbots.ChatbotsManagement'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Chatbot'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Chatbots.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Chatbots.ChatbotsManagement')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Chatbot.Chatbots.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Chatbot.Chatbots.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.Chatbot.Chatbots.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Chatbot.Chatbots.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Chatbot.Chatbots.custom_js')
@endsection
