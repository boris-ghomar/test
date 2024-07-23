@extends('hhh.Site.pages.auth.super_auth')

@section('title', __('auth_site.custom.Registration.PageTile'))

@php
    $version = '?version=' . config('hhh_config.ResourceVersion');
@endphp

@php
    $resourceVersion = '?version=' . config('hhh_config.ResourceVersion');
@endphp

@section('extraPlugin_css')
    @include('hhh.Site.pages.auth.Betconstruct.Registration.plugin_css')
@endsection

@section('custom_css')
    @include('hhh.Site.pages.auth.Betconstruct.Registration.custom_css')
@endsection


@section('content')
    @include('hhh.Site.pages.auth.Betconstruct.Registration.content')
@endsection


@section('extraPlugin_js')
    @include('hhh.Site.pages.auth.Betconstruct.Registration.plugin_js')
@endsection


@section('custom_js')
    @include('hhh.Site.pages.auth.Betconstruct.Registration.custom_js')
@endsection
