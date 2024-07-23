<?php

/*
    |--------------------------------------------------------------------------
    | Notes
    |--------------------------------------------------------------------------
    |
    |Option A:
    |   Using helper function (reference link)
    |   config('app.name');
    |   config('app.env');
    |
    |OptionB:
    |   Using Laravel Config:: facade
    |   $name= Config::get(‘app.name’);
    |
    |   And don’t forget to include at the top of the controller this:
    |   use Illuminate\Support\Facades\Config;
    |
    |
    |Note 1:
    |   After making your own config file in config sub-directory don’t forget to do this:
    |   php artisan config:cache
    |
    |Note 2:
    |   If you want to set configuration values at runtime such as value= ‘test’ for
    |   the constant bar in the file config/foo.php, do this:
    |   Config::set('foo.bar', 'test');
    |
    |   or by passing an array of key / value pairs to the helper function:
    |   config(['foo.bar' => 'test']);
    |
    |
    */

return [

    'Domains' => [
        'Canonical' => 'betcart.net',
    ],

    /*
    |--------------------------------------------------------------------------
    | keywords
    |--------------------------------------------------------------------------
    |
    | These are the keywords used in the system to coordinate the
    | exchange of information between different classes and methods.
    |
    */
    'keywords' => [
        "warnings"      =>  "warnings",
        "pageIndex"     =>  "pageIndex",
        "pageSize"      =>  "pageSize",
        "sortField"     =>  "sortField",
        "sortOrder"     =>  "sortOrder",
        "fromDate"      =>  "fromDate",
        "toDate"        =>  "toDate",
        "fromNumber"    =>  "fromNumber",
        "toNumber"      =>  "toNumber",

        // jsGrid table
        "jsGridJavaData"    =>  "jsGridJavaData",

        // Customizable page
        "customizablePage"          =>  "customizablePage",
        "customizablePageSettings"  =>  "customizablePageSettings",
        "requiredColumns"           =>  "requiredColumns",
        "selectableColumns"         =>  "selectableColumns",
        "selectedColumns"           =>  "selectedColumns",
        "displayColumns"            =>  "displayColumns",

        // Excel Export
        "useExcelExport"    =>  "useExcelExport",
    ],

    /*
    |--------------------------------------------------------------------------
    | Api Base Urls
    |--------------------------------------------------------------------------
    |
    | In this section, you can define the base path for the APIs on the site.
    |
    |
    */
    'apiBaseUrls' => [
        //set routs without domain name

        'backoffice' => [
            'javascript' => 'api/admin/javascript/',
        ],

        'site' => [
            'javascript' => 'api/javascript/',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Reserved Words
    |--------------------------------------------------------------------------
    |
    | These are reserved names used by the system.
    | Changing these items may interfere with system performance!!
    | Items in this list cannot be deleted or edited by the user.
    |
    */
    'reserved' => [

        'databaseUnique' => [
            'transactions' => [
                // this item used in Throwable sections
                'previous_id' => 'transactions_previous_id_unique',
            ],

            'duplicateEntry' => 'Duplicate entry',
        ],

        'database' => [
            'transactions' => [
                // this item used in Throwable sections
                'lock_wait_timeout_exceeded' => 'General error: 1205 Lock wait timeout exceeded; try restarting transaction',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Versions
    |--------------------------------------------------------------------------
    |
    | This section is about versions of resources files such as css, js, etc.
    |
    |
    */
    'ResourceVersion' => '102',

    /*
    |--------------------------------------------------------------------------
    | Icons
    |--------------------------------------------------------------------------
    |
    | This section is about naming and defining fixed icon fonts.
    |
    |
    */
    'fontIcons' => [

        'general' => [
            'error'             => 'fa fa-bug',
            'payment'           => 'fa-solid fa-sack-dollar',
        ],

        'menu' => [
            'dashboard'         => 'mdi mdi-grid-large',
            'userProfile'       => 'fa fa-id-card-alt',
            'Notifications'     => 'icon-bell',
            'Personnel'         => 'fa fa-id-card',
            'Clients'           => 'fa fa-users',
            'PostGrouping'      => 'fa fa-album-collection',
            'Posts'             => 'fa fa-newspaper',
            'Comments'          => 'fa fa-comment',
            'Tickets'           => 'fa-solid fa-message-question',
            'Chatbot'           => 'fa-solid fa-message-bot',
            'AccessControl'     => 'fa fa-key',
            'Settings'          => 'fa fa-cogs',
            'SystemReports'     => 'fa-solid fa-file-chart-column',
            'Support'           => 'fa-solid fa-headset',
            'Domains'           => 'fa-brands fa-internet-explorer',
            'Sharing'           => 'fa-solid fa-lock-a',
            'Referral'          => 'fa-duotone fa-person-sign',
            'Currencies'        => 'fa-solid fa-coins',

            'documentation'     => 'fa fa-book',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    |
    | Settings for notifications.
    |
    |
    */
    'notification' => [

        'categories' => [
            'success' => [
                'iconBgClass' => 'bg-success',
            ],

            'info' => [
                'iconBgClass' => 'bg-info',
            ],

            'warning' => [
                'iconBgClass' => 'bg-warning',
            ],

            'danger' => [
                'iconBgClass' => 'bg-danger',
            ],

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Input field validation requirements.
    |
    |
    */
    'validation' => [
        'minLength' => [
            'username'          => 3,
            'password'          => 8,
            'email'             => 8,
            'usernameClient'    => 8,
        ],
        'maxLength' => [
            'username'          => 40,
            'password'          => 40,
            'email'             => 120,
            'usernameClient'    => 30,
        ],
        'min' => [
            // numeric, minimum acceptable value

        ],
        'max' => [
            // numeric, maximum acceptable value

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Translatable Columns Of Tables
    |--------------------------------------------------------------------------
    |
    | Columns of database tables that can be translatable,
    | are located in this section.
    |
    | Structure: add 'DATABASE_TABLE_NAME' => ['COL_NAME'],
    |
    */
    'TranslatableColumnsOfTables' => [

        'workgroups' => [
            'name',
            'descr',
        ],

        'permissions' => [
            'descr',
        ],

        'currencies' => [
            'name',
            'descr',
        ],

        'transaction_types' => [
            'name',
            'descr',
        ],

    ],

];
