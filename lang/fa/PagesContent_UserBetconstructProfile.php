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

    'cardTitle' => 'پروفایل من',
    'cardDescription' => "در این قسمت می توانید پروفایل حساب کاربری خود را ویرایش کنید.",

    'tab' => [

        'Account' => [
            'title' => 'حساب کاربری',
            'descriptionTitle' => 'اطلاعات حساب کاربری',
            'descriptionText' => 'در این قسمت می توانید اطلاعات اصلی حساب کاربری خود را مشاهده کنید. در صورت نیاز به تغییر این موارد با پشتیبانی سایت تماس بگیرد.',
        ],

        'FurtherInformation' => [
            'title' => 'اطلاعات تکمیلی',
            'descriptionTitle' => 'اطلاعات تکمیلی',
            'descriptionText' => 'برای اطمینان از پشتیبانی سریع و شرکت در قرعه کشی های آینده، اطلاعات تکمیلی را پر کنید.',
            'incompleteInformation' => 'اطلاعات شما ناقص است، لطفا اطلاعات خود را تکمیل کنید.',
        ],

        'ChangeEmail' => [
            'title' => 'ایمیل',
            'descriptionTitle' => 'تغییر ایمیل',
            'descriptionText' => "در این قسمت می توانید ایمیل حساب کاربری خود را بروزرسانی کنید. \nایمیل در بازیابی حساب کاربری بسیار مفید است، لطفا از ایمیل صحیح و معتبر استفاده کنید.",
        ],

        'Password' => [
            'title' => 'رمز عبور',
            'descriptionTitle' => 'تغییر رمز عبور',
            'descriptionText' => 'در این قسمت می توانید رمز عبور خود را تغییر دهید.',
        ],

        'Photo' => [
            'title' => 'تصویر',
            'descriptionTitle' => 'تصویر پروفایل',
            'descriptionText' => 'در این بخش می توانید عکس پروفایل خود را تغییر دهید.',
        ],

        'Settings' => [
            'title' => 'تنظیمات',
            'descriptionTitle' => 'تنظیمات',
            'descriptionText' => 'در این قسمت می توانید تنظیمات مربوط به خودتان را شخصی سازی کنید.',
        ],
    ],

    'messages' => [
        'SavedSuccessfully' => 'پروفایل حساب کاربری با موفقیت به روز شد.',
        'verificationEmailSent' => 'ایمیل تایید به آدرس ایمیل شما ارسال شد، لطفا اگر در صندوق ورودی ایمیلتان آن را مشاهده نکردید، پوشه اسپم را هم بررسی کنید.',
        'verificationEmailNotReceive' => 'در صورت عدم دریافت ایمیل می توانید :remainingTime بعد دوباره اقدام کنید.',
    ],

    'form' => [

        /* Account-tab */
        'id' => [
            'name' => 'شناسه کاربری',
            'placeholder' => 'شناسه کاربری',
            'notice' => '',
        ],
        'login' => [
            'name' => 'نام کاربری',
            'placeholder' => 'نام کاربری',
            'notice' => '',
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
        /*
        // Disabled: Betconstruct is using the phone number as mobile number
        'phone' => [
            'name' => 'تلفن',
            'placeholder' => 'تلفن',
            'notice' => '',
        ],
        'mobile_phone' => [
            'name' => 'موبایل',
            'placeholder' => 'موبایل',
            'notice' => '',
        ], */
        'phone' => [
            'name' => 'موبایل',
            'placeholder' => 'موبایل',
            'notice' => '',
        ],
        'created_stamp' => [
            'name' => 'تاریخ ثبت نام',
            'placeholder' => 'تاریخ ثبت نام',
            'notice' => '',
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
        'job_field_internal' => [
            'name' => 'حوزه فعالیت شغلی (اختیاری)',
            'placeholder' => 'حوزه فعالیت شغلی خود را انتخاب کنید',
            'notice' => '',
        ],
        'iban' => [
            'name' => 'شماره شبای بانکی (اختیاری)',
            'placeholder' => 'شماره شبای بانکی',
            'notice' => 'فقط اعداد شماره شبا را وارد کنید. این شماره به صورت خودکار در فرم برداشت بانکی شما قرار میگیرد.',
        ],

        /* ChangeEmail-tab */
        'email' => [
            'name' => 'ایمیل',
            'placeholder' => 'ایمیل',
            'notice' => 'برای تغییر ایمیل، یک ایمیل تایید به آدرس ایمیل شما ارسال خواهد شد، لطفا از ایمیلی استفاده کنید که به آن دسترسی دارید.',
        ],
        'emailVerificationCode' => [
            'name' => 'کد تایید ایمیل',
            'placeholder' => 'کد تایید ایمیل',
            'notice' => 'کد تایید ایمیل به آدرس ایمیل شما ارسال شده است. لطفا در صورتیکه ایمیل را در صندوق ورودی خود دریافت نکرده اید، پوشه اسپم را هم بررسی کنید.',
        ],

        /* Password-tab */
        'current_password' => [
            'name' => 'رمز عبور فعلی',
            'placeholder' => 'رمز عبور فعلی',
            'notice' => '',
        ],
        'new_password' => [
            'name' => 'رمز عبور جدید',
            'placeholder' => 'رمز عبور جدید',
            'notice' => '',
        ],
        'new_password_confirmation' => [
            'name' => 'تایید رمز عبور جدید',
            'placeholder' => 'تایید رمز عبور جدید',
            'notice' => '',
        ],

        /* Photo-tab */
        'profile_photo_name' => [
            'name' => 'تصویر پروفایل',
            'placeholder' => 'عکس پروفایل خود را انتخاب کنید',
            'notice' => 'فقط اعداد شماره شبا را وارد نمایید.',
        ],

        /* Settings-tab */
        'CommunityTimeZone' => [
            'name' => 'منطقه زمانی',
            'placeholder' => 'فاصله زمانی خود را از ساعت هماهنگ جهانی (مثال: 05:00- یا 02:30+) وارد کنید',
            'notice' => 'فاصله زمانی از ساعت هماهنگ جهانی 00:00 . تمام تاریخ های مورد نیاز در سایت، بر اساس این منطقه زمانی نمایش داده خواهد شد. در صورت تمایل به استفاده از تنظیمات پیش فرض سایت این قسمت را خالی بگذارید.',
        ],
        'CommunityCalendarType' => [
            'name' => 'نوع تقویم',
            'placeholder' => '',
            'notice' => 'نوع تقویمی که میخواهید تاریخ ها بر اساس آن نمایش داده شوند.',
        ],

    ],
];
