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

    'cardTitle' => 'SIGN UP',
    'cardDescription' => "",

    'tab' => [

        'GetMobileNumber' => [
            'title' => 'Mobile Number',
            'descriptionTitle' => 'Mobile Number',
            'descriptionText' => [
                'withVerification' => 'Your mobile number needs to be verified, which will be done by sending a verification code to your mobile number, please use the correct mobile number that you have access to.',
                'withoutVerification' => 'This is the main number for password recovery and necessary calls, please use the correct mobile number you have access to.',
            ],
        ],
        'VerifyMobileNumber' => [
            'title' => 'Verify Mobile Number',
            'descriptionTitle' => 'Verify Mobile Number',
            'descriptionText' => 'The verification code has been sent by SMS to the mobile number :mobileNumber .',
        ],
        'GetEmail' => [
            'title' => 'Email',
            'descriptionTitle' => 'Email',
            'descriptionText' => [
                'withVerification' => 'Your email needs to be verified, which will be done by sending a verification code to your email, please use the correct email that you have access to.',
                'withoutVerification' => 'This email is very important for password recovery, please use the correct email you have access to.',
            ],
        ],
        'VerifyEmail' => [
            'title' => 'Verify Email',
            'descriptionTitle' => 'Verify Email',
            'descriptionText' => 'The verification code has been sent to the email :email .',
        ],
        'AccountData' => [
            'title' => 'Account Data',
            'descriptionTitle' => 'Account Data',
            'descriptionText' => 'Please enter your account information.',
        ],
        'FurtherInformation' => [
            'title' => 'Further Information',
            'descriptionTitle' => 'Further Information',
            'descriptionText' => 'Please enter your account further information.',
        ],
    ],

    'messages' => [
        'SavedSuccessfully' => 'Account profile updated successfully.',
        'verificationMobileNotReceived' => 'A verification text message has been sent to you, if you do not receive the text message, you can try again after :remainingTime.',
        'verificationSmsSent' => 'A verification SMS has been sent to your mobile number.',
        'verificationFailed' => 'The entered code is incorrect or has expired.',
        'nextVerificationTime' => 'When will the next verification code be sent: :nextVerificationTime',
        'verificationEmailNotReceived' => 'A verification code has been sent to your email address, if you do not receive it, you can try again after :remainingTime.',
        'verificationEmailSent' => 'A verification code has been sent to your email address, please check your spam folder if you do not see it in your inbox.',

        // 'verificationCode' => "Your verification code: :verificationCode",
    ],

    'form' => [

        /* GetMobileNumber-tab */
        'mobile_phone' => [
            'name' => 'Mobile number',
            'placeholder' => 'Mobile number',
            'notice' => 'Enter your mobile number along with the country code. (Example: 00989121231234)',
        ],

        /* MobileVerification-tab */
        'MobileVerificationCode' => [
            'name' => 'Mobile Verification Code',
            'placeholder' => 'Mobile Verification Code',
            'notice' => 'Please enter the verification code sent by SMS in this field.',
        ],

        /* GetEmail-tab */
        'email' => [
            'name' => 'Email',
            'placeholder' => 'Email',
            'notice' => '',
        ],

        /* EmailVerification-tab */
        'EmailVerificationCode' => [
            'name' => 'Email Verification Code',
            'placeholder' => 'Email Verification Code',
            'notice' => 'Please enter the verification code sent by email in this field.',
        ],

        /* AccountData-tab */
        'login' => [
            'name' => 'Username',
            'placeholder' => 'Username',
            'notice' => 'The username is used to log into the user account, please use English letters without spaces.',
        ],
        'regPassword' => [
            'name' => 'Password',
            'placeholder' => 'Password',
            'notice' => 'The password must be a combination of English letters and numbers without spaces and at least 8 characters long.',
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
        'currency_id' => [
            'name' => 'Currency',
            'placeholder' => '',
            'notice' => 'Select the currency you want to use in your account.',
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
    ],
];
