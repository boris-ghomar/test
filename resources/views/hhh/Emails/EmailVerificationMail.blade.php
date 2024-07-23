@extends('hhh.Emails.SuperEmail')

@section('greeting', $greeting)

@php
    $pTagStyle = "font-size: 16px;line-height: 1.5em;margin-top: 0;text-align: justify;color:#3d4852;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';";
@endphp
@section('content')
    <p style="{{ $pTagStyle }}">@lang('email.EmailVerificationNotification.lines.receivingReason', ['appName' => $appName])</p>

    <p style="{{ $pTagStyle }}">@lang('email.EmailVerificationNotification.lines.verificationCode', ['verificationCode' => $verificationCode])</p>
@endsection
