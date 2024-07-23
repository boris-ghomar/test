@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Tickets.AllTickets'))
@section('workSpace_Title', __('bo_sidebar.Tickets.AllTickets'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Tickets'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Tickets.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Tickets.AllTickets')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Tickets.AllTickets.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Tickets.AllTickets.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.Tickets.AllTickets.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Tickets.AllTickets.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Tickets.AllTickets.custom_js')
@endsection
