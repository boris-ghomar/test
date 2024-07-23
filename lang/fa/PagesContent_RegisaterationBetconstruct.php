<?php

return [

    /*
    |--------------------------------------------------------------------------
    | "UserBetconstructProfile" page Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in "UserBetconstructProfile" page for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'cardTitle' => 'ثبت نام',
    'cardDescription' => "",

    'tab' => [

        'GetMobileNumber' => [
            'title' => 'شماره تلفن همراه',
            'descriptionTitle' => 'شماره تلفن همراه',
            'descriptionText' => [
                'withVerification' => 'شماره موبایل شما نیازمند تایید است که با ارسال کد تایید به شماره موبایل شما انجام خواهد شد، لطفا از شماره موبایل صحیحی که به آن دسترسی دارید استفاده کنید.',
                'withoutVerification' => 'این شماره اصلی برای بازیابی رمز عبور و تماس های ضروری است، لطفا از شماره موبایل صحیحی که به آن دسترسی دارید استفاده کنید.',
            ],
        ],
        'VerifyMobileNumber' => [
            'title' => 'تایید شماره موبایل',
            'descriptionTitle' => 'تایید شماره موبایل',
            'descriptionText' => 'کد تایید توسط پیامک به شماره موبایل :mobileNumber ارسال شده است.',
        ],
        'GetEmail' => [
            'title' => 'ایمیل',
            'descriptionTitle' => 'ایمیل',
            'descriptionText' => [
                'withVerification' => 'ایمیل شما نیازمند تایید است که با ارسال کد تایید به ایمیل شما انجام خواهد شد، لطفا از ایمیل صحیحی که به آن دسترسی دارید استفاده کنید.',
                'withoutVerification' => 'این ایمیل برای بازیابی رمز عبور بسیار مهم است، لطفا از ایمیل صحیحی که به آن دسترسی دارید استفاده کنید.',
            ],
        ],
        'VerifyEmail' => [
            'title' => 'تأیید ایمیل',
            'descriptionTitle' => 'تأیید ایمیل',
            'descriptionText' => 'کد تایید به ایمیل :email ارسال شده است.',
        ],
        'AccountData' => [
            'title' => 'اطلاعات حساب کاربری',
            'descriptionTitle' => 'اطلاعات حساب کاربری',
            'descriptionText' => 'لطفا اطلاعات مربوط به حساب کاربری خود را وارد کنید.',
        ],
        'FurtherInformation' => [
            'title' => 'اطلاعات تکمیلی',
            'descriptionTitle' => 'اطلاعات تکمیلی',
            'descriptionText' => 'لطفا اطلاعات تکمیلی حساب کاربری خود را وارد کنید.',
        ],
    ],

    'messages' => [
        'SavedSuccessfully' => 'پروفایل حساب کاربری با موفقیت به روز شد.',
        'verificationMobileNotReceived' => 'پیامک تایید برای شما ارسال شده است، در صورت عدم دریافت پیامک می توانید :remainingTime بعد دوباره اقدام کنید.',
        'verificationSmsSent' => 'پیامک تایید به شماره موبایل شما ارسال شد.',
        'verificationFailed' => 'کد وارد شده صحیح نیست و یا منقضی شده است.',
        'nextVerificationTime' => 'زمان ارسال بعدی کد تایید: :nextVerificationTime',
        'verificationEmailNotReceived' => 'کد تایید به ایمیل شما ارسال شده است، در صورت عدم دریافت ایمیل می توانید :remainingTime بعد دوباره اقدام کنید.',
        'verificationEmailSent' => 'ایمیل تایید به آدرس ایمیل شما ارسال شد، لطفا اگر در صندوق ورودی ایمیلتان آن را مشاهده نکردید، پوشه اسپم را هم بررسی کنید.',

        // 'verificationCode' => "Your verification code: :verificationCode",
    ],

    'form' => [

        /* GetMobileNumber-tab */
        'mobile_phone' => [
            'name' => 'شماره موبایل',
            'placeholder' => 'شماره موبایل',
            'notice' => 'شماره موبایل خود را به همراه پیش شماره کشور وارد کنید. (مثال: 00989121231234)',
        ],

        /* MobileVerification-tab */
        'MobileVerificationCode' => [
            'name' => 'کد تایید موبایل',
            'placeholder' => 'کد تایید موبایل',
            'notice' => 'لطفا کد تایید ارسال شده توسط پیامک را در این قسمت وارد کنید.',
        ],

        /* GetEmail-tab */
        'email' => [
            'name' => 'ایمیل',
            'placeholder' => 'ایمیل',
            'notice' => '',
        ],

        /* EmailVerification-tab */
        'EmailVerificationCode' => [
            'name' => 'کد تایید ایمیل',
            'placeholder' => 'کد تایید ایمیل',
            'notice' => 'لطفا کد تایید ارسال شده توسط ایمیل را در این قسمت وارد کنید.',
        ],

        /* AccountData-tab */
        'login' => [
            'name' => 'نام کاربری',
            'placeholder' => 'نام کاربری',
            'notice' => 'نام کاربری برای ورود به حساب کاربری استفاده میشود، لطفا از حروف انگلیسی و بدون فاصله استفاده کنید.',
        ],
        'regPassword' => [
            'name' => 'رمز عبور',
            'placeholder' => 'رمز عبور',
            'notice' => 'رمز عبور باید ترکیبی از حروف انگلیسی و اعداد و بدون فاصله و حداقل ۸ کاراکتر باشد.',
        ],
        'first_name' => [
            'name' => 'نام',
            'placeholder' => 'نام',
            'notice' => '',
        ],
        'last_name' => [
            'name' => 'نام خانوادگی',
            'placeholder' => 'نام خانوادگی',
            'notice' => '',
        ],
        'currency_id' => [
            'name' => 'واحد پولی',
            'placeholder' => '',
            'notice' => 'واحد پولی که تمایل دارید در حساب کاربری خود استفاده کنید را انتخاب کنید.',
        ],

        /* FurtherInformation-tab */
        'gender' => [
            'name' => 'جنسیت',
            'placeholder' => 'جنسیت',
            'notice' => '',
        ],
        'birth_date_stamp' => [
            'name' => 'تاریخ تولد',
            'placeholder' => 'تاریخ تولد',
            'notice' => 'تاریخ تولد خود را به صورت :calendarType وارد کنید.',
        ],
        'province_internal' => [
            'name' => 'استان',
            'placeholder' => 'استان خود را انتخاب کنید',
            'notice' => '',
        ],
        'city_internal' => [
            'name' => 'شهر',
            'placeholder' => 'شهر خود را انتخاب کنید',
            'notice' => '',
        ],
        'contact_numbers_internal' => [
            'name' => 'شماره های تماس',
            'singleName' => 'شماره تماس',
            'placeholder' => 'شماره تماس',
            'notice' => 'شماره تماس های خود را به ترتیب اولویتی که برایتان دارند وارد کنید.',
        ],
        'contact_methods_internal' => [
            'name' => 'روش ارتباط پشتیبانی با شما',
            'placeholder' => '',
            'notice' => 'روش های برقراری تماسی که تمایل دارید تا پشتیبانی با شما تماس بگیرد را مشخص کنید.',
        ],
        'caller_gender_internal' => [
            'name' => 'جنسیت تماس گیرنده',
            'placeholder' => '',
            'notice' => 'جنسیت پشتیبانی که ترجیح می دهید با شما تماس بگیرد را مشخص کنید، در صورت امکان تلاش خواهد شد تا پشتیان با جنسیت مورد نظر شما با شما تماس بگیرد.',
        ],
    ],
];
