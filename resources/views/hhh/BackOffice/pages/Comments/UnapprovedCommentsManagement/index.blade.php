@extends('hhh.BackOffice.super_main')

@section('title', __('bo_sidebar.Comments.UnapprovedComments'))
@section('workSpace_Title', __('bo_sidebar.Comments.UnapprovedComments'))
@section('workSpace_Icon', config('hhh_config.fontIcons.menu.Comments'))

@section('breadcrumb_items')
    <li class="breadcrumb-item"><a href="#">@lang('bo_sidebar.Comments.MenuTitle')</a></li>
@endsection
@section('breadcrumb_item_active')
    @lang('bo_sidebar.Comments.UnapprovedComments')
@endsection


@section('extraPlugin_css')
    @include('hhh.BackOffice.pages.Comments.UnapprovedCommentsManagement.plugin_css')
@endsection


@section('custom_css')
    @include('hhh.BackOffice.pages.Comments.UnapprovedCommentsManagement.custom_css')
@endsection

@section('content')
    @include('hhh.BackOffice.pages.Comments.UnapprovedCommentsManagement.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.BackOffice.pages.Comments.UnapprovedCommentsManagement.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.BackOffice.pages.Comments.UnapprovedCommentsManagement.custom_js')
@endsection
