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
        'CopiedToClipboard' => 'The list of domains was copied to clipboard.',
        'ErrorGeneratingNewDomain' => 'Error generating new domain: Please increase the number of letters in the domain name.',
    ],

    'form' => [

        'DomainCount' => [
            'name' => 'Domain Count',
            'placeholder' => 'Domain Count',
            'notice' => 'Enter the number of domains you need.',
        ],

        'DomainLettersCount' => [
            'name' => 'Domain Letters Count',
            'placeholder' => 'Domain Letters Count',
            'notice' => 'Enter the number of characters for each domain.',
        ],

        'ExcludeLetters' => [
            'name' => 'Exclude Letters',
            'placeholder' => 'Exclude Letters',
            'notice' => 'Enter the letters or phrase that you want not to be used in domain names and separate them with plus (.). Example: xx+bm',
        ],

        'DomainExtension' => [
            'name' => 'Domain Extension',
            'placeholder' => 'Domain Extension',
            'notice' => '',
        ],

    ],
];
