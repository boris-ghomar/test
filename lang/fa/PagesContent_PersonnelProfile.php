<?php

return [

    /*
    |--------------------------------------------------------------------------
    | "Personnel Profile" Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in "Personnel Profile" page for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'cardTitle' => 'پروفایل من',
    'cardDescription' => "در این قسمت می توانید پروفایل حساب کاربری خود را ویرایش کنید.",

    'tab' => [

        'Personal' => [
            'title' => 'شخصی',
            'descriptionTitle' => 'اطلاعات شخصی',
            'descriptionText' => 'در این قسمت می توانید اطلاعات شخصی خود را ویرایش کنید.',
        ],

        'Photo' => [
            'title' => 'تصویر',
            'descriptionTitle' => 'تصویر پروفایل',
            'descriptionText' => 'در این بخش می توانید عکس پروفایل خود را تغییر دهید.',
        ],

        'Password' => [
            'title' => 'رمز عبور',
            'descriptionTitle' => 'تغییر رمز عبور',
            'descriptionText' => 'در این قسمت می توانید رمز عبور خود را تغییر دهید.',
        ],

        'Settings' => [
            'title' => 'تنظیمات',
            'descriptionTitle' => 'تنظیمات',
            'descriptionText' => 'در این قسمت می توانید تنظیمات مربوط به خودتان را شخصی سازی کنید.',
        ],
    ],

    'messages' => [
        'SavedSuccessfully' => 'پروفایل حساب کاربری با موفقیت به روز شد.',
    ],

    'form' => [

        /* Personal-tab */
        'email' => [
            'name' => 'ایمیل',
            'placeholder' => 'ایمیل',
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
        'alias_name' => [
            'name' => 'نام مستعار',
            'placeholder' => 'نام مستعار',
            'notice' => 'این نام مستعار در قسمت های عمومی که نیاز است به جای نام واقعی شما نمایش داده میشود.',
        ],
        'gender' => [
            'name' => 'جنسیت',
            'placeholder' => 'جنسیت',
            'notice' => '',
        ],

        /* Photo-tab */
        'profile_photo_name' => [
            'name' => 'تصویر پروفایل',
            'placeholder' => 'عکس پروفایل خود را انتخاب کنید',
            'notice' => '',
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

        /* Settings-tab */
        'AdminPanelTimeZone' => [
            'name' => 'منطقه زمانی',
            'placeholder' => 'فاصله زمانی خود را از ساعت هماهنگ جهانی (مثال: 05:00- یا 02:30+) وارد کنید',
            'notice' => 'فاصله زمانی از ساعت هماهنگ جهانی 00:00 . تمام تاریخ های مورد نیاز در پنل مدیریت، بر اساس این منطقه زمانی نمایش داده خواهد شد. در صورت تمایل به استفاده از تنظیمات پیش فرض پنل مدیریت این قسمت را خالی بگذارید.',
        ],
        'AdminPanelCalendarType' => [
            'name' => 'نوع تقویم',
            'placeholder' => '',
            'notice' => 'نوع تقویمی که میخواهید تاریخ ها بر اساس آن نمایش داده شوند.',
        ],
    ],
];
