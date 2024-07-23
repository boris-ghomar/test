<?php

return [

    /*
    |--------------------------------------------------------------------------
    | "General Settings" Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in "General Settings" page
    | for various messages that we need to display to the user. You are free
    | to modify these language lines according to your application's requirements.
    |
    */

    'cardTitleCreate' => 'ساخت پست جدید',
    'cardDescriptionCreate' => "در این بخش می توانید پست جدید ایجاد کنید.",

    'cardTitleEdit' => 'ویرایش پست',
    'cardDescriptionEdit' => "در این بخش می توانید پست را ویرایش کنید.",

    'tab' => [

        'Content' => [
            'title' => 'محتوا',
            'descriptionTitle' => 'بخش "محتوا" چه کاری انجام می دهد؟',
            'descriptionText' => 'در این بخش می توانید محتوای پست را ویرایش کنید.',
        ],

        'MainPhoto' => [
            'title' => 'تصویر اصلی',
            'descriptionTitle' => 'بخش "تصویر اصلی" چه کاری انجام می دهد؟',
            'descriptionText' => 'در این بخش می توانید تصویر اصلی پست را ویرایش کنید.',
        ],

        'SEO' => [
            'title' => 'سئو',
            'descriptionTitle' => 'بخش "سئو" چه کاری انجام می دهد؟',
            'descriptionText' => 'در این بخش می توانید موارد مربوط به سئوی پست را ویرایش کنید.',
        ],

    ],

    'messages' => [
        'SavedSuccessfully' => 'پست با موفقیت ذخیره شد.',
        'UpdatesSuccessfully' => 'پست با موفقیت به روز شد.',
        'SavedFailed' => 'Failed to save post.',
    ],

    'form' => [

        /* Content-tab */
        'post_space_id' => [
            'name' => 'فضای پست',
            'placeholder' => '',
            'notice' => "",
        ],
        'is_published' => [
            'name' => 'انتشار پست',
            'placeholder' => '',
            'notice' => 'اگر این گزینه فعال باشد، این پست برای کاربران نمایش داده خواهد شد.<br/>همچنین اگر پست هنوز آماده انتشار نیست و فقط در حال ذخیره سازی مرحله به مرحله هستید این گزینه را خاموش بگذارید تا بررسی نهایی انتشار پست انجام نشود.',
        ],
        'title' => [
            'name' => 'عنوان',
            'placeholder' => 'عنوان',
            'notice' => 'سعی کنید عنوان پست بین ۴۰ تا ۶۰ کاراکتر داشته باشید.',
        ],
        'content' => [
            'name' => 'محتوا',
            'placeholder' => 'محتوای عالی خود را شروع کنید ...',
            'notice' => '',
        ],

        /* MainPhoto-tab */
        'main_photo' => [
            'name' => 'تصویر اصلی پست',
            'placeholder' => 'تصویر را انتخاب کنید',
            'notice' => 'این تصویر شاخص پست است که در لیست پست ها، ابتدای پست و هر قسمتی که نیاز باشد نمایش داده میشود.',
        ],

        /* SEO-tab */

    ],
];
