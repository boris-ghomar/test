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

    'cardTitle' => 'My Profile',
    'cardDescription' => "In this section you can edit your account profile.",

    'tab' => [

        'Personal' => [
            'title' => 'Personal',
            'descriptionTitle' => 'Personal Details',
            'descriptionText' => 'In this section you can edit your personal information.',
        ],

        'Photo' => [
            'title' => 'Photo',
            'descriptionTitle' => 'Profile Photo',
            'descriptionText' => 'In this section you can change your profile picture.',
        ],

        'Password' => [
            'title' => 'Password',
            'descriptionTitle' => 'Change Password',
            'descriptionText' => 'In this section you can change your password.',
        ],

        'Settings' => [
            'title' => 'Settings',
            'descriptionTitle' => 'Settings',
            'descriptionText' => 'In this section, you can customize your settings.',
        ],
    ],

    'messages' => [
        'SavedSuccessfully' => 'Account profile updated successfully.',
    ],

    'form' => [

        /* Personal-tab */
        'email' => [
            'name' => 'Email',
            'placeholder' => 'Email',
            'notice' => '',
        ],
        'first_name' => [
            'name' => 'First Name',
            'placeholder' => 'First Name',
            'notice' => '',
        ],
        'last_name' => [
            'name' => 'Last Name',
            'placeholder' => 'Last Name',
            'notice' => '',
        ],
        'alias_name' => [
            'name' => 'Alias Name',
            'placeholder' => 'Alias Name',
            'notice' => 'This alias name will be displayed instead of your real name in the public areas where it is needed.',
        ],
        'gender' => [
            'name' => 'Gender',
            'placeholder' => 'Gender',
            'notice' => '',
        ],

        /* Photo-tab */
        'profile_photo_name' => [
            'name' => 'Profile Photo',
            'placeholder' => 'Choose your profile photo',
            'notice' => '',
        ],

        /* Password-tab */
        'current_password' => [
            'name' => 'Current Password',
            'placeholder' => 'Current Password',
            'notice' => '',
        ],
        'new_password' => [
            'name' => 'New Password',
            'placeholder' => 'New Password',
            'notice' => '',
        ],
        'new_password_confirmation' => [
            'name' => 'Confirm Password',
            'placeholder' => 'Confirm Password',
            'notice' => '',
        ],

        /* Settings-tab */
        'AdminPanelTimeZone' => [
            'name' => 'Time Zone',
            'placeholder' => 'Enter your time interval from UTC. Example: -05:00 OR +02:30',
            'notice' => 'Time distance from UTC 00:00. All required dates in the admin panel will be displayed based on this time zone. If you want to use the default settings of the admin panel, leave this section blank.',
        ],
        'AdminPanelCalendarType' => [
            'name' => 'Calendar Type',
            'placeholder' => '',
            'notice' => 'The type of calendar you want the dates to be displayed on.',
        ],
    ],
];
