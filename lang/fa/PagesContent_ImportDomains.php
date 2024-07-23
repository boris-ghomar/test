<?php

return [

    /*
    |--------------------------------------------------------------------------
    | "Import DOmains" Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in "Import DOmains" page for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'cardTitle' => '',
    'cardDescription' => "",

    'tab' => [],

    'messages' => [
        'SavedSuccessfully' => 'اطلاعات با موفقیت ذخیره شد.',
        'accessDenied' => 'دسترسی ممنوع است.',
        'ConfirmImportTitle' => "اخطار: عملیات حساس",
        'ConfirmImportMsg' => "لطفا قبل از ورود اطلاعات جدول اطلاعات ورودی را بررسی کنید و از صحت ترتیب اطلاعات اطمینان حاصل کنید.<br><br>آیا از انجام عملیات ورود اطلاعات اطمینان دارید؟",
        'NoDataMsg' => "هیچ داده ای برای وارد کردن وجود ندارد.",
        'StoredMsg' => "ذخیره شد",
        'UpdatedMsg' => "بروزرسانی شد",
        'IgnoredMsg' => "مورد وجود داشت و بازنویسی نشد.",
    ],

    'form' => [

        'DomainsListInput' => [
            'name' => 'دامنه های جدید',
            'placeholder' => 'لیست دامنه ها',
            'notice' => 'دامنه هایی که از فایل اکسل کپی کرده اید را اینجا پیست کنید.',
        ],

        'domain_category_id' => [
            'name' => 'دسته بندی دامنه ها',
            'placeholder' => '',
            'notice' => '',
        ],

        'domain_holder_account_id' => [
            'name' => 'اکانت تامین کننده دامنه ها',
            'placeholder' => '',
            'notice' => '',
        ],

        'Overwrite' => [
            'name' => 'بازنویسی',
            'placeholder' => '',
            'notice' => 'در صورت روشن بودن این گزینه، اگر دامین وجود داشته باشد اطلاعات جدید بازنویسی خواهد شد.',
        ],
    ],
];
