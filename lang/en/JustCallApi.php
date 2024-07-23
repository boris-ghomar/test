<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JustCall Api errors Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during using JustCall Api for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'UnknownError' => 'Unknown Error',
    'UnknownPartnerError' => 'Unknown partner Api error',

    'errorKeys' => [

        /**
         * Since the Justcall API does not provide an error number,
         * enter the exact text received here to find the appropriate message to display to the user.
         *
         * Notice:
         * 1. Uppercase and lowercase letters do not matter.
         * 2. No need to add to other language files.
         */

        'IncorrectNumber' => 'One of the numbers is incorrect. Please check the number.',
        'UsaRegionRestriction' => 'Messaging from an unregistered 10DLC phone number to US region is restricted',
        'TryingToSendLandline' => 'Trying to send an SMS to Landline Number, please check the number and try again.',
    ],

    // To shows to the client
    'IncorrectNumber' => 'The phone number entered is incorrect. Please check the phone number.',
    'UsaRegionRestriction' => 'It is not possible to send SMS to this number, please check the number.',
    'TryingToSendLandline' => 'Trying to send an SMS to Landline Number, please check the number and try again.',

];
