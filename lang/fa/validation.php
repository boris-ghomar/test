<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'آیتم :attribute باید پذیرفته شود.',
    'accepted_if' => 'The :attribute field must be accepted when :other is :value.',
    'active_url' => ':attribute یک آدرس اینترنتی معتبر نیست.',
    'after' => ':attribute باید یک تاریخ بعد از :date باشد.',
    'after_or_equal' => ':attribute باید یک تاریخ بعد یا برابر با  :date باشد.',
    'alpha' => ':attribute فقط باید شامل حروف باشد.',
    'alpha_dash' => ':attribute فقط باید شامل حروف ، اعداد ، خط تیره و زیر خط باشد.',
    'alpha_num' => ':attribute فقط باید شامل حروف و اعداد باشد.',
    'array' => ':attribute باید یک آرایه باشد.',
    'before' => ':attribute باید یک تاریخ قبل از :date باشد.',
    'before_or_equal' => ':attribute باید یک تاریخ قبل یا برابر با  :date باشد.',
    'between' => [
        'array' => 'مورد :attribute باید بین :min و :max آیتم داشته باشد.',
        'file' => 'مورد :attribute باید بین :min و :max کیلو بایت باشد.',
        'numeric' => 'مورد :attribute باید بین :min و :max باشد.',
        'string' => 'مورد :attribute باید بین :min و :max کاراکتر باشد.',
    ],
    'boolean' => 'مورد :attribute باید یکی از مقادیر \'  true | false \' باشد.',
    'confirmed' => 'تایید :attribute مطابقت ندارد.',
    'current_password' => 'رمز عبور نادرست است.',
    'date' => ':attribute یک تاریخ معتبر نیست.',
    'date_equals' => ':attribute باید تاریخی برابر با :date  باشد.',
    'date_format' => ':attribute با قالب :format مطابقت ندارد.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'digits' => 'مورد :attribute باید :digits رقم باشد.',
    'digits_between' => 'تعداد ارقام مورد :attribute باید بین :min و :max رقم باشد.',
    'dimensions' => 'مورد :attribute دارای ابعاد تصویر نامعتبر است.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => ':attribute باید یک آدرس ایمیل معتبر باشد.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'مقدار وارده شده برای :attribute نامعتبر است.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'array' => 'مورد :attribute باید بیشتر از :value آیتم داشته باشد.',
        'file' => 'مورد :attribute باید بزرگتر از :value کیلو بایت باشد.',
        'numeric' => 'مورد :attribute باید بزرگتر از :value باشد.',
        'string' => 'مورد :attribute باید بیشتر از :value کاراکتر باشد.',
    ],
    'gte' => [
        'array' => 'مورد :attribute باید :value آیتم یا بیشتر داشته باشد.',
        'file' => 'مورد :attribute باید بزرگتر یا مساوی :value کیلو بایت باشد.',
        'numeric' => 'مورد :attribute باید بزرگتر یا مساوی :value باشد.',
        'string' => 'مورد :attribute باید :value کاراکتر یا بیشتر باشد.',
    ],
    'image' => 'مورد :attribute باید یک تصویر باشد.',
    'in' => 'مقدار وارده شده برای :attribute نامعتبر است.',
    'in_array' => 'مورد :attribute در :other وجود ندارد.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'array' => 'مورد :attribute باید کمتر از :value آیتم داشته باشد.',
        'file' => 'مورد :attribute باید کوچکتر از :value کیلو بایت باشد.',
        'numeric' => 'مورد :attribute باید کوچکتر از :value باشد.',
        'string' => 'مورد :attribute باید کمتر از :value کاراکتر باشد.',
    ],
    'lte' => [
        'array' => 'مورد :attribute باید :value آیتم یا کمتر داشته باشد.',
        'file' => 'مورد :attribute باید کوچکتر یا مساوی :value کیلو بایت باشد.',
        'numeric' => 'مورد :attribute باید کوچکتر یا مساوی :value باشد.',
        'string' => 'مورد :attribute باید :value کاراکتر یا کمتر باشد.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'array' => ':attribute نباید بیشتر از :max مورد داشته باشد.',
        'file' => ':attribute نباید بزرگتر از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بیشتر از :max باشد.',
        'string' => 'تعداد کاراکترهای :attribute نباید بیشتر از :max کاراکتر باشد.',
    ],
    'mimes' => 'مورد :attribute باید فایلی از نوع :values باشد.',
    'mimetypes' => 'مورد :attribute باید فایلی از نوع :values باشد.',
    'min' => [
        'array' => ':attribute حداقل باید :min مورد داشته باشد.',
        'file' => ':attribute حداقل باید :min کیلوبایت باشد.',
        'numeric' => ':attribute حداقل باید :min باشد.',
        'string' => 'تعداد کاراکترهای :attribute حداقل باید :min کاراکتر باشد.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'مقدار وارده شده برای :attribute نامعتبر است.',
    'not_regex' => 'مورد :attribute دارای فرمت نامعتبر است.',
    'numeric' => ':attribute باید یک عدد باشد.',
    'password' => 'رمز عبور نادرست است.',
    'present' => ':attribute باید وجود داشته باشد.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'مورد :attribute دارای فرمت نامعتبر است.',
    'required' => 'مورد :attribute الزامی است.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => ':attribute زمانی که :other مقدار :value است، لازم است.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => ':attribute درصورت وجود :values لازم است.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => ':attribute و :other باید یکسان باشند.',
    'size' => [
        'array' => 'مورد :attribute باید شامل :size مورد باشد.',
        'file' => 'مورد :attribute باید :size کیلوبایت باشد.',
        'numeric' => 'مورد :attribute باید :size باشد.',
        'string' => 'مورد :attribute باید :size کاراکتر باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از این حروف شروع شود: :values ',
    'string' => ':attribute باید یک رشته از حروف باشد.',
    'timezone' => 'مورد :attribute باید یک منطقه زمانی معتبر باشد.',
    'unique' => 'این :attribute قبلاً استفاده شده و نمی تواند تکراری باشد.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'مورد :attribute باید یک URL معتبر باشد.',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attributes' => [
            'selectedItem' => 'مورد انتخاب شده',
        ],
        'ParentId' => [
            'sameIdAndParentId' => 'یک مجموعه نمی تواند زیر مجموعه خودش باشد.',
        ],
        'collection' => [
            'hasChild' => 'مورد :name دارای زیرمجموعه است، ابتدا زیر مجموعه های این مورد را به مجموعه دیگری منتقل کنید و یا حذف کنید.',
        ],
        'EnglishString' => [
            'EnglishString' => "فقط کاراکترهای انگلیسی باید در :attribute استفاده شود. کاراکترهای مجاز:  [A-Z] , [a-z] , [0-9] , _ , - , . , ( , ) , / ",
            'EnglishStringUsernameFormat' => 'فقط حروف و اعداد انگلیسی بدون استفاده از فاصله باید در :attribute استفاده شود. همچنین میتوانید از "." و "_" هم استفاده کنید.',
        ],
        'PersianString' => [
            'PersianString' => ':attribute باید فارسی باشد ، حروف زبانهای دیگر مجاز نیستند.',
        ],
        'DateTimeFormat' => [
            'IncorrectTimeFormat' => 'فرمت ساعت ورودی برای :attribute صحیح نیست. (فرمت صحیح : hh:mm یا hh:mm:ss)',
            'IncorrectDateFormat' => 'فرمت تاریخ ورودی برای :attribute صحیح نیست. (فرمت صحیح : :correctFormat)',
        ],
        'age' => [
            'lte' => 'سن باید کوچکتر یا مساوی :value سال باشد.',
            'gte' => 'سن باید بزرگتر یا مساوی :value سال باشد.',
        ],
        'String' => [
            'MinOneUppercase' => 'مورد :attribute حداقل باید یک حرف بزرگ انگلیسی داشته باشد.',
            'MinOneLowercase' => 'مورد :attribute حداقل باید یک حرف کوچک انگلیسی داشته باشد.',
            'MinOneNumber' => 'مورد :attribute حداقل باید یک عدد داشته باشد.',
            'MinOneSpecialCharacter' => 'مورد :attribute حداقل باید یک کاراکتر خاص داشته باشد. (:specialCharacter)',
            'NotAllowedCharacter' => 'مورد :attribute نباید شامل کاراکترهای خاص داخل پرانتز باشد. (:notAllowedCharacters)',
        ],
        'unauthorizedStatus' => [
            'use' => 'شما مجاز به استفاده از وضعیت :newStatus برای :attribute نمیباشید.',
            'change' => 'شما مجاز به تغییر وضعیت :attribute از :lastStatus به :newStatus نمیباشید.',
        ],
        'export' => [
            'noRecords' => 'هیچ رکوردی برای صادر کردن وجود ندارد.',
            'maxAllowedExportRecordsCount' => 'حداکثر تعداد رکوردهای مجاز برای صادر کردن :max عدد است و شما :num رکورد را انتخاب کرده اید، لطفا درخواست خود را محدود کنید.',
        ],
        'web' => [
            'Url' => 'فرمت آدرس اینترنتی :attribute صحیح نیست.',
            'Websocket' => 'فرمت آدرس اینترنتی وب سوکت :attribute صحیح نیست.',
        ],
        'number' => [
            'numberPattern' => 'فرمت :attribute صحیح نیست.',
            'MaximumAllowedDecimals' => 'حداکثر اعشار مجاز برای :attribute :maxAllowedDecimals رقم است.',
        ],

        'notExist' => 'این :attribute وجود ندارد.',
        'currentlyExist' => 'این :attribute در حال حاضر وجود دارد.',
        'NotFound' => 'این :attribute یافت نشد.',
        'notDefined' => 'این مقدار برای :attribute تعریف نشده است.',
        'reservedBySystem' => 'این :attribute : :name توسط سیستم رزرو شده است.',
        'editBlocked' => 'امکان ویرایش مورد :name توسط سیستم مسدود شده است.',
        'deleteBlocked' => 'امکان حذف مورد :name توسط سیستم مسدود شده است.',
        'invalidValue' => 'مقدار وارده شده برای :attribute نامعتبر است.',
        'usedInTransactions' => 'مورد :name در معاملات استفاده شده است و امکان حذف آن وجود ندارد.',


    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'username' => "'نام کاربری'",
        'password' => "'رمز عبور'",
    ],

];
