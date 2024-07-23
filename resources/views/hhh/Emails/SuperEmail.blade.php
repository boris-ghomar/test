<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>@lang('thisApp.AppName')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        body,
        .body-tag {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            position: relative;
            -webkit-text-size-adjust: none;
            background-color: #ffffff;
            color: #718096;
            height: 100%;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100% !important;
            direction: {{ __('general.locale.direction') }}
        }

        .wrapper {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            background-color: #edf2f7;
            margin: 0;
            padding: 0;
            width: 100%;
        }


        .content {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .content .logo {
            padding: 25px 0;
            text-align: center;
        }

        .content .header {
            padding: 25px 0;
            text-align: center;
        }

        .content .header a {
            color: #3d4852;
            font-size: 19px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .content .body {
            background-color: #edf2f7;
            border-bottom: 1px solid #edf2f7;
            border-top: 1px solid #edf2f7;
            margin: 0;
            padding: 0;
            width: 100%;
            border: hidden !important;
        }

        .inner-body {
            -premailer-width: 570px;
            background-color: #ffffff;
            border-color: #e8e5ef;
            border-radius: 2px;
            border-width: 1px;
            box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
            margin: 0 auto;
            padding: 0;
            width: 570px;
        }

        .content-cell {
            max-width: 100vw;
            padding: 32px;
        }

        h1 {
            color: #3d4852;
            font-size: 18px;
            font-weight: bold;
            margin-top: 0;
            text-align: {{ __('general.locale.start') }};
        }

        .inner-body p {
            font-size: 16px;
            line-height: 1.5em;
            margin-top: 0;
            text-align: justify;
        }

        .footer {
            -premailer-width: 570px;
            margin: 0 auto;
            padding: 0;
            text-align: center;
            width: 570px;
        }

        .footer p {
            line-height: 1.5em;
            margin-top: 0;
            color: #b0adc5;
            font-size: 12px;
            text-align: center;
        }

        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 90% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<body class="body-tag"
    style="box-sizing: border-box;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';position: relative;-webkit-text-size-adjust: none;background-color: #ffffff;color: #718096;height: 100%;line-height: 1.4;margin: 0;padding: 0;width: 100% !important;direction: {{ __('general.locale.direction') }}">

    <table class="wrapper" cellpadding="0" cellspacing="0" role="presentation"
        style="-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 100%;background-color: #edf2f7;margin: 0;padding: 0;width: 100%;">
        <tbody>

            <tr>
                <td>
                    <table class="content" cellpadding="0" cellspacing="0" role="presentation"
                        style="margin: 0;padding: 0;width: 100%;">
                        <tbody>
                            <tr>
                                <td class="logo" style="padding: 25px 0;text-align: center;">
                                    <img style='background-color: #3d4852; max-width:80vw;'
                                        src="{{ $message->embed($siteLogo) }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="header" style="padding: 25px 0;text-align: center;">
                                    <a href="{{ config('app.url') }}"
                                        style="color: #3d4852;font-size: 19px;font-weight: bold;text-decoration: none;display: inline-block;">@lang('thisApp.AppName')</a>
                                </td>
                            </tr>

                            <!-- Email Body -->
                            <tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0"
                                    style="background-color: #edf2f7;border-bottom: 1px solid #edf2f7;border-top: 1px solid #edf2f7;margin: 0;padding: 0;width: 100%;border: hidden !important;">
                                    <table class="inner-body" width="570" cellpadding="0" cellspacing="0"
                                        role="presentation"
                                        style="-premailer-width: 570px;background-color: #ffffff;border-color: #e8e5ef;border-radius: 2px;border-width: 1px;box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);margin: 0 auto;padding: 0;width: 570px;">
                                        <!-- Body content -->
                                        <tbody>
                                            <tr>
                                                <td class="content-cell" style="max-width: 100vw;padding: 32px;">
                                                    <h1
                                                        style="color: #3d4852;font-size: 18px;font-weight: bold;margin-top: 0;text-align: {{ __('general.locale.start') }};font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';">
                                                        @yield('greeting')</h1>
                                                    @yield('content')
                                                    <p
                                                        style="font-size: 16px;line-height: 1.5em;margin-top: 0;text-align: justify;font-weight: bold;color:#3d4852;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';">
                                                        @lang('email.regrading', ['appName' => $appName])</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <table class="footer" width="570" cellpadding="0" cellspacing="0"
                                        role="presentation"
                                        style="-premailer-width: 570px;margin: 0 auto;padding: 0;text-align: center;width: 570px;background-color: #edf2f7;">
                                        <tbody>
                                            <tr>
                                                <td class="content-cell" style="max-width: 100vw;padding: 32px;">
                                                    <p
                                                        style="line-height: 1.5em;margin-top: 0;color: #b0adc5;font-size: 12px;text-align: center;direction: ltr;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';">
                                                        © 2023 Betcart Players Club. All rights reserved.</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
