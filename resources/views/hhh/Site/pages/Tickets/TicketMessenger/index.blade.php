@extends('hhh.Site.super_main')

@section('title', __('bo_navbar.Tickets.MyTickets'))
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
    @include('hhh.Site.pages.Tickets.TicketMessenger.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.Tickets.TicketMessenger.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.Tickets.TicketMessenger.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.Tickets.TicketMessenger.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.Tickets.TicketMessenger.custom_js')
@endsection
