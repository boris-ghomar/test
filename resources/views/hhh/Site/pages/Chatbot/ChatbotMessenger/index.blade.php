@extends('hhh.Site.super_main')

@section('title', __('thisApp.Chatbot.Messenger.PageTitle'))
{{-- @section('workSpace_Title', __('bo_sidebar.Site.Chats.NewChat')) --}}
{{-- @section('workSpace_Icon', config('hhh_config.fontIcons.menu.Chats')) --}}
{{--
@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Site.Chats.MenuTitle')</a></li>
@endsection

@section('breadcrumb_item_active')
    @lang('bo_sidebar.Site.Chats.NewChat')
@endsection --}}

{{-- @section('page_header')
    @include('hhh.Site.partials._page-header')
@endsection --}}

@section('container-extra', 'chat-page')

@php
    $resourceVersion = '?version=' . config('hhh_config.ResourceVersion');
@endphp

@section('extraPlugin_css')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.custom_js')
@endsection
