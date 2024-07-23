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
        'SavedSuccessfully' => 'Information saved successfully.',
        'accessDenied' => 'Access denied.',
        'ConfirmImportTitle' => "Warning: Sensitive operation",
        'ConfirmImportMsg' => "Please check the input data table before importing the information and make sure the order of the information is correct.<br><br>Are you sure about the data entry operation?",
        'NoDataMsg' => "There is no data to import.",
        'StoredMsg' => "Stored",
        'UpdatedMsg' => "Updated",
        'IgnoredMsg' => "Item exists and was not overwritten.",
    ],

    'form' => [

        'DomainsListInput' => [
            'name' => 'New Doamins',
            'placeholder' => 'Domains List',
            'notice' => 'Paste the domains you copied from the Excel file here.',
        ],

        'domain_category_id' => [
            'name' => 'Domains Category',
            'placeholder' => '',
            'notice' => '',
        ],

        'domain_holder_account_id' => [
            'name' => 'Domains Holder Account',
            'placeholder' => '',
            'notice' => '',
        ],

        'Overwrite' => [
            'name' => 'Overwrite',
            'placeholder' => '',
            'notice' => 'If this option is on, if the domain exists, the new information will be overwritten.',
        ],
    ],
];
