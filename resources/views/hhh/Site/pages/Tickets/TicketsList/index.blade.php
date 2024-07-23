@extends('hhh.Site.super_main')

@section('title', __('bo_navbar.Tickets.MyTickets'))
@section('workSpace_Title', __('bo_navbar.Tickets.MyTickets'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Tickets'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_navbar.Tickets.MenuTitle')</a></li>
@endsection

@section('breadcrumb_item_active')
    @lang('bo_navbar.Tickets.MyTickets')
@endsection

@section('page_header')
    @include('hhh.Site.partials._page-header')
@endsection

@section('extraPlugin_css')
    @include('hhh.Site.pages.Tickets.TicketsList.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.Site.pages.Tickets.TicketsList.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.Tickets.TicketsList.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.Tickets.TicketsList.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.Tickets.TicketsList.custom_js')
@endsection
