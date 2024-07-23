<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Emails Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during ending email.
    |
    |
    */
    'regrading' => "Regrads<br>:appName",
    'unknownUser' => "user",

    'EmailVerificationNotification' => [
        'subject' => ':appName: Email verification',
        'greeting' => 'Hello, dear :greetingName',
        'actionText' => '',
        'lines' => [
            'receivingReason' => "You have revceived this email to verify your email address in the \":appName\".",
            'ignoreEmail' => "If this request is not from you, please ignore this email and take no action.",
            'emailNotRequested' => "If this was not your request, please change your password as soon as possible and contact site support if you need help.",
            'verificationCode' => "Your email verification code: :verificationCode",
        ],
    ],

];
