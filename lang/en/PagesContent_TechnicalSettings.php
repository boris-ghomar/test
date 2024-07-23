<?php

return [

    /*
    |--------------------------------------------------------------------------
    | "Technical Settings" Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in "Technical Settings" page
    | for various messages that we need to display to the user. You are free
    | to modify these language lines according to your application's requirements.
    |
    */

    'cardTitle' => 'Technical Settings',
    'cardDescription' => "In this section you can configure your desired settings.",

    'tab' => [

        'BetconstructExternalAdmin' => [
            'title' => 'Betconstruct External Admin',
            'descriptionTitle' => 'Betconstruct External Admin',
            'descriptionText' => 'In this section you can configure the settings related to the "Betconstruct External Admin API".',
        ],
        'BetconstructSwarmApi' => [
            'title' => 'Betconstruct Swarm Api',
            'descriptionTitle' => 'Betconstruct Swarm Api',
            'descriptionText' => 'In this section you can configure the settings related to the "Betconstruct Swarm Api".',
        ],
        'JustCallApi' => [
            'title' => 'JustCall Api',
            'descriptionTitle' => 'JustCall Api',
            'descriptionText' => 'In this section you can configure the settings related to the "<a target="_blank" href="https://justcall.io/">JustCall</a> Api".',
        ],
        'TrustScoreSystem' => [
            'title' => 'Trust Score System',
            'descriptionTitle' => 'Trust Score System',
            'descriptionText' => 'In this section you can configure the settings related to the "Trust Score System".',
        ],
        'DomainsAssignment' => [
            'title' => 'Domains Assignment System',
            'descriptionTitle' => 'Domains Assignment System',
            'descriptionText' => 'In this section you can configure the settings related to the "Domains Assignment System".',
        ],
    ],

    'messages' => [
        'SavedSuccessfully' => 'Technical settings updated successfully.',
    ],

    'form' => [

        /* BetconstructExternalAdmin-tab */
        'BcExAd_ApiName' => [
            'name' => 'API Name',
            'placeholder' => 'API Name',
            'notice' => 'This name is used in the technical logs of the system, so use English letters.',
        ],
        'BcExAd_HashAlgorithm' => [
            'name' => 'Hash Algorithm',
            'placeholder' => 'Hash Algorithm',
            'notice' => 'Hash algorithm for data encryption. (Default: :default)',
        ],
        'BcExAd_ApiUrl' => [
            'name' => 'Api URL',
            'placeholder' => 'Api URL',
            'notice' => 'API access URL.',
        ],
        'BcExAd_PartnerId' => [
            'name' => 'Partner ID',
            'placeholder' => 'Partner ID',
            'notice' => 'This value is your ID on the Betconstruct platform and should be obtained from Betconstruct.',
        ],
        'BcExAd_HashKey' => [
            'name' => 'Hash Key',
            'placeholder' => 'Hash Key',
            'notice' => 'This value is your "Hash Key" on the Betconstruct platform and should be obtained from Betconstruct.',
        ],

        /* BetconstructSwarmApi-tab */
        'BcSwAp_ApiName' => [
            'name' => 'API Name',
            'placeholder' => 'API Name',
            'notice' => 'This name is used in the technical logs of the system, so use English letters.',
        ],
        'BcSwAp_ApiUrl' => [
            'name' => 'Api URL',
            'placeholder' => 'Api URL',
            'notice' => 'API access URL.',
        ],
        'BcSwAp_WebSocketUrl' => [
            'name' => 'WebSocket URL',
            'placeholder' => 'WebSocket URL',
            'notice' => 'WebSocket access URL.',
        ],
        'BcSwAp_WebSocketUrlAlternative' => [
            'name' => 'Alternative WebSocket URL',
            'placeholder' => 'Alternative WebSocket URL',
            'notice' => 'This URL is used as a alternative if the application receives an error accessing the websocket with the original address.',
        ],
        'BcSwAp_SiteId' => [
            'name' => 'Site ID',
            'placeholder' => 'Site ID',
            'notice' => 'This value is your site ID on the Betconstruct platform and should be obtained from Betconstruct.',
        ],

        /* JustCallApi-tab */
        'JuCaAp_ApiName' => [
            'name' => 'API Name',
            'placeholder' => 'API Name',
            'notice' => 'This name is used in the technical logs of the system, so use English letters.',
        ],
        'JuCaAp_ApiUrl' => [
            'name' => 'Api URL',
            'placeholder' => 'Api URL',
            'notice' => 'API access URL.',
        ],
        'JuCaAp_ApiKey' => [
            'name' => 'Api Key',
            'placeholder' => 'Api Key',
            'notice' => 'To get this value, you must visit the <a target="_blank" href="https://justcall.io/app/developers">Just Call website -> API Credentials</a>.',
        ],
        'JuCaAp_ApiSecret' => [
            'name' => 'Api Secret',
            'placeholder' => 'Api Secret',
            'notice' => 'To get this value, you must visit the <a target="_blank" href="https://justcall.io/app/developers">Just Call website -> API Credentials</a>.',
        ],
        'JuCaAp_PhoneNumberForSMS' => [
            'name' => 'Phone number to send SMS',
            'placeholder' => 'Phone number to send SMS',
            'notice' => 'To get this value, you must visit the <a target="_blank" href="https://justcall.io/app/numbers">Just Call website -> Numbers Section</a>.<br>Enter your desired phone number along with the country code. (example: 0016691231234)',
        ],

        /* TrustScoreSystem-tab */
        'TrScSy_NewClientBaseTrustScore' => [
            'name' => 'New Client Base Trust Score',
            'placeholder' => 'New Client Base Trust Score',
            'notice' => 'This basic score is given to a user who has just registered and has no activity criteria. (between 1-100)',
        ],
        'TrScSy_NegativePointValue' => [
            'name' => 'Negative Point Value',
            'placeholder' => 'Negative Point Value',
            'notice' => 'If the user includes receiving a negative score, this number will be deducted from its trust score.',
        ],
        'TrScSy_DepositPerPoint' => [
            'name' => 'Point Per Deposit',
            'placeholder' => 'Point Per Deposit',
            'notice' => 'This number specifies how many deposits you want to add 1 positive point to the user\'s trust score.',
        ],
        'TrScSy_UsdPerPoint' => [
            'name' => 'Point Per USD',
            'placeholder' => 'Point Per USD',
            'notice' => 'This number specifies that for the amount of deposit of this currency, you want to add 1 positive point to the user\'s trust score.',
        ],
        'TrScSy_IrtPerPoint' => [
            'name' => 'Point Per IRT',
            'placeholder' => 'Point Per IRT',
            'notice' => 'This number specifies that for the amount of deposit of this currency, you want to add 1 positive point to the user\'s trust score.',
        ],
        'TrScSy_TomPerPoint' => [
            'name' => 'Point Per TOM',
            'placeholder' => 'Point Per TOM',
            'notice' => 'This number specifies that for the amount of deposit of this currency, you want to add 1 positive point to the user\'s trust score.',
        ],
        'TrScSy_IrrPerPoint' => [
            'name' => 'Point Per IRR',
            'placeholder' => 'Point Per IRR',
            'notice' => 'This number specifies that for the amount of deposit of this currency, you want to add 1 positive point to the user\'s trust score.',
        ],

        /* DomainsAssignmentSystem-tab */
        'DoAsSy_PermanentDomain' => [
            'name' => 'Permanent Domain',
            'placeholder' => 'Permanent Domain',
            'notice' => 'Enter your permanent domain name in this field. (Example: test.com)',
        ],
        'DoAsSy_MinReportCount' => [
            'name' => 'Minimum number of reports of dedicated domains',
            'placeholder' => 'Minimum number of reports of dedicated domains',
            'notice' => 'The minimum number of user problem reports for the dedicated domain after which the new domain will be assigned to users.',
        ],
        'DoAsSy_MinAssignableTrustScore' => [
            'name' => 'Minimum assignable trust score',
            'placeholder' => 'Minimum assignable trust score',
            'notice' => 'Dedicated domain is assigned to users whose trust score is greater than or equal to this number. (between 1 and 100)',
        ],
        'DoAsSy_MaxAssignableDomains' => [
            'name' => 'Maximum number of assignable dedicated domains',
            'placeholder' => 'Maximum number of assignable dedicated domains',
            'notice' => 'Enter the maximum number of assignable dedicated domains between 1 and 100.',
        ],
        'DoAsSy_MinPublicDomainReportsCount' => [
            'name' => 'Minimum number of public domain reports',
            'placeholder' => 'Minimum number of public domain reports',
            'notice' => 'The minimum number of user problem reports for the public domain after which the new domain will be assigned to users.',
        ],
        'DoAsSy_MinPublicDomainHoldMinutes' => [
            'name' => 'Minimum public domain holding time (in minutes)',
            'placeholder' => 'Minimum public domain holding time',
            'notice' => 'If you enter a time in this field, even if the number of user reports reaches the minimum required until this time expires, the system will not assign a new public domain.',
        ],
        'DoAsSy_DaysOfKeepingExipredAssignments' => [
            'name' => 'The days of keeping expired assigned domains',
            'placeholder' => 'The days of keeping assigned domains',
            'notice' => 'After this time (based on the day between 10 and 365 days), the system will delete the unusable domains assigned to clients from the system history.',
        ],

    ],
];
