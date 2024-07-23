@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Tickets.OpenTickets'))
@section('workSpace_Title', __('bo_sidebar.Tickets.OpenTickets'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Tickets'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Tickets.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Tickets.OpenTickets')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Tickets.OpenTickets.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Tickets.OpenTickets.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.Tickets.OpenTickets.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Tickets.OpenTickets.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Tickets.OpenTickets.custom_js')
@endsection
