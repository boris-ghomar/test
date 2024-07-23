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

    'cardTitle' => 'My Profile',
    'cardDescription' => "In this section you can edit your account profile.",

    'tab' => [

        'Account' => [
            'title' => 'Account',
            'descriptionTitle' => 'Account Details',
            'descriptionText' => 'In this section, you can see the main information of your user account. If you need to change these items, contact the site support.',
        ],

        'FurtherInformation' => [
            'title' => 'Further Information',
            'descriptionTitle' => 'Further Information',
            'descriptionText' => 'Fill out additional information to ensure prompt support and entry into future sweepstakes.',
            'incompleteInformation' => 'Your information is incomplete, please complete your information.',
        ],

        'ChangeEmail' => [
            'title' => 'Email',
            'descriptionTitle' => 'Change Email',
            'descriptionText' => "In this section, you can update your account email.\nEmail is very useful in account recovery, please use correct and valid email.",
        ],

        'Password' => [
            'title' => 'Password',
            'descriptionTitle' => 'Change Password',
            'descriptionText' => 'In this section you can change your password.',
        ],

        'Photo' => [
            'title' => 'Photo',
            'descriptionTitle' => 'Profile Photo',
            'descriptionText' => 'In this section you can change your profile picture.',
        ],

        'Settings' => [
            'title' => 'Settings',
            'descriptionTitle' => 'Settings',
            'descriptionText' => 'In this section, you can customize your settings.',
        ],
    ],

    'messages' => [
        'SavedSuccessfully' => 'Account profile updated successfully.',
        'verificationEmailSent' => 'A verification email has been sent to your email address, please check your spam folder if you do not see it in your inbox.',
        'verificationEmailNotReceive' => 'If you do not receive the email, you can try again after :remainingTime .',
    ],

    'form' => [

        /* Account-tab */
        'id' => [
            'name' => 'ID',
            'placeholder' => 'ID',
            'notice' => '',
        ],
        'login' => [
            'name' => 'Username',
            'placeholder' => 'Username',
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
        /*
        // Disabled: Betconstruct is using the phone number as mobile number
        'phone' => [
            'name' => 'Phone',
            'placeholder' => 'Phone',
            'notice' => '',
        ],
        'mobile_phone' => [
            'name' => 'Mobile Phone',
            'placeholder' => 'Mobile Phone',
            'notice' => '',
        ], */
        'phone' => [
            'name' => 'Mobile Phone',
            'placeholder' => 'Mobile Phone',
            'notice' => '',
        ],
        'created_stamp' => [
            'name' => 'Date of registration',
            'placeholder' => 'Date of registration',
            'notice' => '',
        ],

        /* FurtherInformation-tab */
        'gender' => [
            'name' => 'Gender',
            'placeholder' => 'Gender',
            'notice' => '',
        ],
        'birth_date_stamp' => [
            'name' => 'Date of birth',
            'placeholder' => 'Date of birth',
            'notice' => 'Enter your date of birth as a :calendarType.',
        ],
        'province_internal' => [
            'name' => 'Province',
            'placeholder' => 'Select Province',
            'notice' => '',
        ],
        'city_internal' => [
            'name' => 'City',
            'placeholder' => 'Select City',
            'notice' => '',
        ],
        'contact_numbers_internal' => [
            'name' => 'Contact Numbers',
            'singleName' => 'Contact Number',
            'placeholder' => 'Contact Number',
            'notice' => 'Enter the number of your contacts in the order of their priority for you.',
        ],
        'contact_methods_internal' => [
            'name' => 'How support communicates with you',
            'placeholder' => '',
            'notice' => 'Specify the contact methods you would like support to contact you with.',
        ],
        'caller_gender_internal' => [
            'name' => 'Caller Gender',
            'placeholder' => '',
            'notice' => 'Specify the gender of the support you prefer to contact you, if possible, an attempt will be made to have the supporter contact you with your desired gender.',
        ],
        'job_field_internal' => [
            'name' => 'Job Field (optional)',
            'placeholder' => 'Select Job Field',
            'notice' => '',
        ],
        'iban' => [
            'name' => 'IBAN (optional)',
            'placeholder' => 'IBAN',
            'notice' => 'Just enter the numbers of IBAN number. This number is automatically included in your bank withdrawal form.',
        ],

        /* ChangeEmail-tab */
        'email' => [
            'name' => 'Email',
            'placeholder' => 'Email',
            'notice' => 'To change your email, a verification email will be sent to your email address, please use an email that you have access to.',
        ],
        'emailVerificationCode' => [
            'name' => 'Email Verification Code',
            'placeholder' => 'Email Verification Code',
            'notice' => 'The email verification code has been sent to your email address. Please check your spam folder if you have not received the email in your inbox.',
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

        /* Photo-tab */
        'profile_photo_name' => [
            'name' => 'Profile Photo',
            'placeholder' => 'Choose your profile photo',
            'notice' => '',
        ],

        /* Settings-tab */
        'CommunityTimeZone' => [
            'name' => 'Time Zone',
            'placeholder' => 'Enter your time interval from UTC. Example: -05:00 OR +02:30',
            'notice' => 'Time distance from UTC 00:00. All required dates in the site will be displayed based on this time zone. If you want to use the default settings of the site, leave this section blank.',
        ],
        'CommunityCalendarType' => [
            'name' => 'Calendar Type',
            'placeholder' => '',
            'notice' => 'The type of calendar you want the dates to be displayed on.',
        ],
    ],
];
