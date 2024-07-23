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
        'CopiedToClipboard' => 'لیست دامنه ها در حافظه کپی شد.',
        'ErrorGeneratingNewDomain' => 'خطای تولید دامنه جدید: لطفا تعداد حروف نام دامنه را افزایش دهید.',
    ],

    'form' => [

        'DomainCount' => [
            'name' => 'تعداد دامنه',
            'placeholder' => 'تعداد دامنه',
            'notice' => 'تعداد دامنه های مورد نیاز خود را وارد کنید.',
        ],

        'DomainLettersCount' => [
            'name' => 'تعداد حروف نام دامنه',
            'placeholder' => 'تعداد حروف نام دامنه',
            'notice' => 'تعداد حروف هر دامنه را وارد کنید.',
        ],

        'ExcludeLetters' => [
            'name' => 'حذف حروف',
            'placeholder' => 'حذف حروف',
            'notice' => 'حروف یا عبارتی که میخواهید در نام دامنه ها استفاده نشوند را وارد کنید و با به علاوه (+) از هم جدا کنید. مثال: xx+bm',
        ],

        'DomainExtension' => [
            'name' => 'پسوند دامنه',
            'placeholder' => 'پسوند دامنه',
            'notice' => '',
        ],

    ],
];
