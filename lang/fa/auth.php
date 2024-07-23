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

        'placeholder_Username' => 'نام کاربری',
        'placeholder_Email' => 'ایمیل',
        'placeholder_Country' => 'کشور',
        'placeholder_Password' => 'رمز عبور',
        'placeholder_ConfirmPassword' => 'تکرار رمز عبور',

        'Username' => 'نام کاربری',
        'Password' => 'رمز عبور',
        'ConfirmPassword' => 'تکرار رمز عبور',
        'Email' => 'ایمیل',



        'LoginForm' => [
            'PageTile'    => 'ورود',
            '1thTitle'    => 'خوش آمدید!',
            '2thTitle'    => 'خوشحالیم که دوباره شما رو میبینیم!',
            'KeepMeSignedIn' => 'مرا به خاطر داشته باش',
            'ForgotPassword' => 'رمز عبور خود را فراموش کرده اید؟',
            'DoNotHaveAnAccount' => 'حساب کاربری ندارید؟',
        ],

        'ForgotPasswordForm' => [
            'PageTile'    => 'فراموشی رمز عبور',
            '1thTitle'    => 'رمز عبور خود را فراموش کرده اید؟',
            '2thTitle'    => 'مشکلی نیست. فقط آدرس ایمیل خود را وارد نمایید و ما یک لینک تنظیم مجدد رمز عبور را برای شما ارسال خواهیم کرد که به شما امکان می دهد یک رمزعبور جدید را انتخاب کنید.',
            'EmailPasswordResetLink' => 'ارسال ایمیل بازیابی رمز عبور',
            'ReturnToLoginPage' => 'بازگشت به صفحه ورود',
            'PasswordRecoveryEmailSent' => 'ایمیل بازیابی رمز عبور ارسال شد، لطفا به ایمیل خود مراجعه کنید. اگر ایمیل در اینباکس شما نبود، لطفا پوشه اسپم را هم چک کنید.',
            'PasswordRecoveryEmailNotSent' => 'کاربری با این ایمیل یافت نشد.',
        ],

        'ResetPasswordForm' => [
            'PageTile'    => 'بازنشانی رمز عبور',
            '1thTitle'    => 'بازنشانی رمز عبور',
            '2thTitle'    => 'رمز عبور جدید خود را تنظیم نمایید',
            'SaveNewPassword' => 'ذخیره رمز ورود جدید',
            'UserNotFound' => 'کاربری با این ایمیل یافت نشد.',
        ],

    ],

];
