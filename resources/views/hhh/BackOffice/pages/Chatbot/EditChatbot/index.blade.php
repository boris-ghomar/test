@extends('hhh.BackOffice.super_main')

@section('title', __('thisApp.AdminPages.Chatbot.EditChatbot'))
@section('workSpace_Title', __('thisApp.AdminPages.Chatbot.EditChatbot'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Chatbot'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="{{ AdminRoutesEnum::Chatbots_Bots->route() }}">@lang('bo_sidebar.Chatbots.MenuTitle')</a></li>
@endsection

@section('breadcrumb_item_active')
    @lang('thisApp.AdminPages.Chatbot.EditChatbot')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.custom_css')
@endsection


@section('content')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.custom_js')
@endsection
