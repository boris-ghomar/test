<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'این ورودی ها با سوابق ما مطابقت ندارد.',
    'password' => 'رمز عبور ارائه شده نادرست است.',
    'throttle' => 'تلاش برای ورود به سیستم بسیار زیاد است. لطفاً دوباره امتحان کنید :seconds ثانیه.',

    /*
    |--------------------------------------------------------------------------
    | HHH custom Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'custom' => [
        'AccountStatusMessage' => "حساب کاربری شما :status شده است.",
        'ContactSupport' => "در صورت نیاز با پشتیبانی از طریق ایمیل :email تماس بگیرید.",
        'AccessDenied' => 'دسترسی ممنوع است!',

        'Login' => 'ورود',
        'SignUp' => 'ثبت نام',

        'SignIn' => 'ورود',
        'SignOut' => 'خروج',

        'Registration_Title' => 'فرم ثبت نام',
        'Registration_Notice' => 'لطفا اطلاعات را بادقت و به صورت صحیح وارد کنید.',
        'Registration_Agreement' => 'من با تمام شرایط و ضوابط موافقم',
        'Registration_HaveAccount' => 'قبلاً حساب دارید؟',

        'placeholder_Username' => 'نام کاربری در سایت بتکارت',
        'placeholder_Email' => 'ایمیل',
        'placeholder_Mobile' => 'شماره موبایل',
        'placeholder_Country' => 'کشور',
        'placeholder_Password' => 'رمز عبور در سایت بتکارت',
        'placeholder_ConfirmPassword' => 'تکرار رمز عبور',

        'Username' => 'نام کاربری در سایت بتکارت',
        'Password' => 'رمز عبور در سایت بتکارت',
        'ConfirmPassword' => 'تکرار رمز عبور',
        'Email' => 'ایمیل',
        'MobileNumber' => 'شماره موبایل',
        'VerificationCode' => 'کد احراز هویت',



        'LoginForm' => [
            'PageTile'    => 'ورود',
            '1thTitle'    => 'ورود به حساب کاربری',
            '2thTitle'    => 'همیشه نام کاربری و رمز عبور خود را به یاد داشته باشید',
            'KeepMeSignedIn' => 'مرا به خاطر داشته باش',
            'ForgotPassword' => 'رمز عبور خود را فراموش کرده اید؟',
            'DoNotHaveAnAccount' => 'حساب کاربری بتکارت ندارید؟',
        ],

        'Registration' => [
            'PageTile'    => 'فرم ثبت نام',
        ],

        'ForgotPasswordForm' => [
            'PageTile'    => 'فراموشی رمز عبور',
            '1thTitle'    => 'رمز عبور خود را فراموش کرده اید؟',
            '2thTitle'    => 'مشکلی نیست، یکی از روش های بازیابی رمز عبور را انتخاب کنید تا رمز عبورتان را بازیابی کنیم.',
            'EmailPasswordResetLink' => 'ارسال ایمیل بازیابی رمز عبور',
            'ReturnToLoginPage' => 'بازگشت به صفحه ورود',
            'PasswordRecoveryEmailSent' => 'ایمیل بازیابی رمز عبور ارسال شد، لطفا به ایمیل خود مراجعه کنید. اگر ایمیل در اینباکس شما نبود، لطفا پوشه اسپم را هم چک کنید.',
            'PasswordRecoveryEmailNotSent' => 'کاربری با این ایمیل یافت نشد.',
            'SendVerificationCode' => 'ارسال کد احراز هویت',
            'Authentication' => 'احراز هویت',
            'ResetPassword' => 'بازنشانی رمز عبور',

            'errors' => [
                'accountNotFound' => 'حساب کاربری با این مشخصات وجود ندارد.',
                'invalidPasswordRecoveryMethod' => 'روش بازیابی رمز عبور نامعتبر است.',
                'verificationMobileNotReceived' => 'کد احراز هویت با پیامک برای شما به شماره موبایل: :verifiable ارسال شده است، در صورت عدم دریافت پیامک می توانید :remainingTime بعد دوباره اقدام کنید.',
                'verificationEmailNotReceived' => 'کد احراز هویت به ایمیل شما :verifiable ارسال شده است، در صورت عدم دریافت ایمیل می توانید :remainingTime بعد دوباره اقدام کنید.',
                'verificationFailed' => 'کد وارد شده صحیح نیست و یا منقضی شده است.',
            ],

            'messages' => [
                'successfullyReset' => 'رمز عبور شما با موفقیت بازنشانی شد.',
            ],

            'index' => [
                'RecoveryMethod' => [
                    'name' => 'روش بازیابی رمز عبور',
                    'placeholder' => '',
                    'notice' => '',
                ],
            ],

            'attempByEmail' => [
                'notice' => 'آدرس ایمیل خود را وارد نمایید و ما یک کد احراز هویت برای شما ارسال خواهیم کرد که به شما امکان می دهد یک رمزعبور جدید را انتخاب کنید.',
            ],

            'attempByMobile' => [
                'notice' => 'شماره موبایل خود را وارد نمایید و ما یک کد احراز هویت برای شما ارسال خواهیم کرد که به شما امکان می دهد یک رمزعبور جدید را انتخاب کنید.',
                'mobileDescr' => 'شماره موبایل خود را به همراه پیش شماره کشور وارد کنید. (مثال: 00989121231234)',
            ],

            'verification' => [
                'notice' => 'کد احراز هویتی که دریافت کرده اید را در این قسمت وارد کنید.',
            ],

            'resetPassword' => [
                'notice' => 'رمز عبور جدید خود را وارد کنید.',
            ],
        ],

        'ResetPasswordForm' => [
            'PageTile'    => 'بازنشانی رمز عبور',
            '1thTitle'    => 'بازنشانی رمز عبور',
            '2thTitle'    => 'رمز عبور جدید خود را تنظیم نمایید',
            'SaveNewPassword' => 'ذخیره رمز ورود جدید',
            'UserNotFound' => 'کاربری با این مشخصات پیدا نشد!',
        ],


    ],

];
