<?php

return [

    /*
    |--------------------------------------------------------------------------
    | "General Settings" Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in "General Settings" page
    | for various messages that we need to display to the user. You are free
    | to modify these language lines according to your application's requirements.
    |
    */

    'cardTitle' => 'General Settings',
    'cardDescription' => "In this section you can configure your desired settings.",

    'BoxLabels' => [
        'CommunityRegistration' => [
            'MobileVerificarionSettings' => 'Mobile number verificarion settings',
            'MobileVerificarionSettingsDescr' => 'Note: The settings of this section are implemented when you have selected the mobile number in the registration fields.',
            'EmailVerificarionSettings' => 'Email verificarion settings',
            'EmailVerificarionSettingsDescr' => 'Note: The settings of this section are implemented when you have selected the email in the registration fields.',
        ],
        'CommunityPasswordRecovery' => [
            'MobileVerificarionSettings' => 'Mobile recovery settings',
            'MobileVerificarionSettingsDescr' => 'Note: The settings in this section are used when you have selected the mobile phone in the password recovery methods.',
            'EmailVerificarionSettings' => 'Email recovery settings',
            'EmailVerificarionSettingsDescr' => 'Note: The settings in this section are used when you have selected email in password recovery methods.',
        ],
    ],

    'tab' => [

        'AdminPanel' => [
            'title' => 'Admin Panel',
            'descriptionTitle' => 'What does the "Admin Panel" section do?',
            'descriptionText' => 'In this section you can configure the settings related to the "Admin Panel".',
        ],

        'Community' => [
            'title' => 'Community',
            'descriptionTitle' => 'What does the "Community" section do?',
            'descriptionText' => 'In this section you can configure the settings related to the "Community".',
        ],

        'CommunityRegistration' => [
            'title' => 'Community Registration',
            'descriptionTitle' => 'What does the "Community Registration" section do?',
            'descriptionText' => 'In this section you can configure the settings related to the "Community Registration".',
        ],

        'CommunityPasswordRecovery' => [
            'title' => 'Community Password Recovery',
            'descriptionTitle' => 'What does the "Community Password Recovery" section do?',
            'descriptionText' => 'In this section you can configure the settings related to the "Community Password Recovery".',
        ],

        'Chatbot' => [
            'title' => 'Chatbot',
            'descriptionTitle' => 'What does the "Chatbot" section do?',
            'descriptionText' => 'In this section you can configure the settings related to the "Chatbot".',
        ],

        'Ticket' => [
            'title' => 'Request',
            'descriptionTitle' => 'What does the "Request" section do?',
            'descriptionText' => 'In this section you can configure the settings related to the "Request".',
        ],

        'Referral' => [
            'title' => 'Referral',
            'descriptionTitle' => 'What does the "Referral" section do?',
            'descriptionText1' => 'In this section you can configure the settings related to the "Referral Program".',
            'descriptionText2' => '* If the reffered client\'s currency is different from the refferer client\'s currency, the system will use the <a href="/admin/currencies/currency_rates" target="_blank">currency rate</a> to deposit the client\'s reward, so this rate must always be updated so that the amounts are calculated correctly.',
        ],

        'Bet' => [
            'title' => 'Bet',
            'descriptionTitle' => 'What does the "Bet" section do?',
            'descriptionText' => 'In this section you can configure the settings related to the "Bets".',
        ],

        'AppRules' => [
            'title' => 'Terms and Conditions',
            'descriptionTitle' => 'What does the "Terms and Conditions" section do?',
            'descriptionText' => 'In this section, you can enter the terms and conditions related to the use of the application, in case of any change in this section, the new rules will be displayed to the user and the user must approve the new rules before using the application.',
        ],


    ],

    'messages' => [
        'SavedSuccessfully' => 'General settings updated successfully.',
    ],

    'form' => [

        /* AdminPanel-tab */
        'IsAdminPanelActive' => [
            'name' => 'Is the admin panel active?',
            'placeholder' => '',
            'notice' => 'If this item is disabled, the admin panel of application will be taken out of service.',
        ],
        'AdminPanelExplanationInactive' => [
            'name' => 'Explanation of admin panel inactivity',
            'placeholder' => 'Explanation of admin panel inactivity',
            'notice' => 'If you disable the admin panel, this explanation will be displayed to personnel.',
        ],
        'AdminPanelTimeZone' => [
            'name' => 'Default Time Zone',
            'placeholder' => 'Enter your time interval from UTC. Example: -05:00 OR +02:30',
            'notice' => 'Time distance from UTC 00:00. All required dates in the admin panel will be displayed based on this time zone.',
        ],
        'canPersonnelChangeTimeZone' => [
            'name' => 'Can personnel change their time zone?',
            'placeholder' => '',
            'notice' => 'If this item is active, it gives permission to personnel to view the dates in the admin panel with their time zone, and this change is only for them and does not affect the performance of the application for others.',
        ],
        'AdminPanelCalendarType' => [
            'name' => 'Default Calendar Type',
            'placeholder' => '',
            'notice' => 'The type of calendar you want the dates to be displayed on.',
        ],
        'canPersonnelChangeCalendarType' => [
            'name' => 'Can personnel change their calendar type?',
            'placeholder' => '',
            'notice' => 'If this item is active, it gives permission to personnel to view the dates in the admin panel with their calendar type, and this change is only for them and does not affect the performance of the application for others.',
        ],
        'AdminPanelDefaultLanguage' => [
            'name' => 'Default Language',
            'placeholder' => '',
            'notice' => 'When a user runs the site for the first time or has not yet logged in and has not been recognized by the system, the application will be displayed in this language.',
        ],
        'AdminPanelBigLogo' => [
            'name' => 'Big Logo',
            'placeholder' => 'Choose your large logo photo',
            'notice' => 'In this section, you can change the large logo image.',
        ],
        'AdminPanelMiniLogo' => [
            'name' => 'Mini Logo',
            'placeholder' => 'Choose your small logo photo',
            'notice' => 'In this section, you can change the small logo image.',
        ],
        'AdminPanelFavicon' => [
            'name' => 'Favicon',
            'placeholder' => 'Choose your favicon photo',
            'notice' => 'In this section, you can change the favicon image.',
        ],

        /* Community-tab */
        'IsCommunityActive' => [
            'name' => 'Is the community active?',
            'placeholder' => '',
            'notice' => 'If this item is disabled, community users will not be able to use the community and the community will be taken out of service.',
        ],
        'CommunityExplanationInactive' => [
            'name' => 'Explanation of community inactivity',
            'placeholder' => 'Explanation of community inactivity',
            'notice' => 'If you disable the community, this explanation will be displayed to clients.',
        ],
        'CommunityTimeZone' => [
            'name' => 'Default Time Zone',
            'placeholder' => 'Enter your time interval from UTC. Example: -05:00 OR +02:30',
            'notice' => 'Time distance from UTC 00:00. All required dates in the community will be displayed based on this time zone.',
        ],
        'canClientChangeTimeZone' => [
            'name' => 'Can client change their time zone?',
            'placeholder' => '',
            'notice' => 'If this item is active, it gives permission to clients to view the dates in the community with their time zone, and this change is only for them and does not affect the performance of the application for others.',
        ],
        'CommunityCalendarType' => [
            'name' => 'Default Calendar Type',
            'placeholder' => '',
            'notice' => 'The type of calendar you want the dates to be displayed on.',
        ],
        'canClientChangeCalendarType' => [
            'name' => 'Can client change their calendar type?',
            'placeholder' => '',
            'notice' => 'If this item is active, it gives permission to clients to view the dates in the community with their calendar type, and this change is only for them and does not affect the performance of the application for others.',
        ],
        'CommunityDefaultLanguage' => [
            'name' => 'Default Language',
            'placeholder' => '',
            'notice' => 'When a user runs the site for the first time or has not yet logged in and has not been recognized by the system, the application will be displayed in this language.',
        ],
        'CommentApproval' => [
            'name' => 'Comment Approval',
            'placeholder' => '',
            'notice' => 'If this option is enabled, the user\'s comment will not be published until you approve it.',
        ],
        'SupportEmail' => [
            'name' => 'Support Email',
            'placeholder' => 'Support Email',
            'notice' => 'This email will be displayed to users when they need support. If you don\'t want to display the email, you can leave this field blank.',
        ],
        'CommunityBigLogo' => [
            'name' => 'Big Logo',
            'placeholder' => 'Choose your large logo photo',
            'notice' => 'In this section, you can change the large logo image.',
        ],
        'CommunityMiniLogo' => [
            'name' => 'Mini Logo',
            'placeholder' => 'Choose your small logo photo',
            'notice' => 'In this section, you can change the small logo image.',
        ],
        'CommunityFavicon' => [
            'name' => 'Favicon',
            'placeholder' => 'Choose your favicon photo',
            'notice' => 'In this section, you can change the favicon image.',
        ],
        'IsCommunityDashboradNoteActive' => [
            'name' => 'Is the dashboard note enabled?',
            'placeholder' => '',
            'notice' => 'If this option is enabled, the note will be displayed in the forum dashboard.',
        ],
        'CommunityDashboradNoteTitle' => [
            'name' => 'The title of the dashboard note',
            'placeholder' => 'The title of the dashboard note',
            'notice' => 'Enter the title of the dashboard note in this section. You can also use HTML codes in this section.',
        ],
        'CommunityDashboradNoteText' => [
            'name' => 'Dashboard note text',
            'placeholder' => 'Dashboard note text',
            'notice' => 'Enter the text of the dashboard note in this section. You can also use HTML codes in this section.',
        ],

        /* CommunityRegistration-tab */
        'CommunityRegistrationIsActive' => [
            'name' => 'Is the community registration active?',
            'placeholder' => '',
            'notice' => 'If this option is enabled, clients can register in the community.',
        ],
        'CommunityRegistrationFields' => [
            'name' => 'Registration Fields',
            'placeholder' => '',
            'notice' => 'Select the items you want to receive from the user at the time of registration.',
        ],
        'CommunityRegistrationAvailableCurrencies' => [
            'name' => 'Available Currencies',
            'placeholder' => '',
            'notice' => 'Select the currencies available for registration.',
        ],
        'CommunityRegistrationDefaultCurrency' => [
            'name' => 'Default Currency',
            'placeholder' => '',
            'notice' => 'Select the default currency you want to use during registration.',
        ],
        'CommunityRegistrationTargetLinkAfterComplete' => [
            'name' => 'Destination link after registration',
            'placeholder' => 'Destination link after registration',
            'notice' => 'Enter the link of the section you want the user to be taken to after completing the registration in this section.',
        ],
        'CommunityRegistrationMobileVerificationIsRequired' => [
            'name' => 'Does the mobile number need to be verified?',
            'placeholder' => '',
            'notice' => 'If this option is active, the client will be required to verify their mobile number via SMS. (Note: Before activating this option, make sure your SMS system is active)',
        ],
        'CommunityRegistrationMobileVerificationPerDay' => [
            'name' => 'Allowed number of mobile phone verifications per 24-hours',
            'placeholder' => 'Numbers per 24-hours',
            'notice' => 'The system will allow the client to receive SMS verifications every 24-hours for the number specified in this section. (Note: due to the fact that the user does not login on the site, this feature identifies the user based on other information such as session, mobile number, etc., so there is a possibility of not recognizing a duplicate client by changing these specifications)',
        ],
        'CommunityRegistrationMobileVerificationExpirationMinutes' => [
            'name' => 'SMS verification expiration time',
            'placeholder' => 'SMS verification expiration time base on minutes',
            'notice' => 'Until this time expires after sending the verification SMS, the user will not be able to request to receive a new mobile verification SMS. (base on minutes)',
        ],
        'CommunityRegistrationMobileVerificationExpirationMinutesCoefficient' => [
            'name' => 'The coefficient of increasing the SMS verification expiration time',
            'placeholder' => 'The coefficient of increasing the SMS verification expiration time',
            'notice' => "In each request to send mobile verification SMS, the set expiration time is multiplied by this number and increases the next verification SMS time. (If you don't need this item, you can set it to 0)",
        ],
        'CommunityRegistrationMobileVerificationText' => [
            'name' => 'Mobile number verification text message',
            'placeholder' => 'Mobile number verification text message',
            'notice' => 'In this section, you can set the text of the mobile number verification SMS. (verification code variable: {verificationCode})',
        ],
        'CommunityRegistrationEmailVerificationIsRequired' => [
            'name' => 'Does the email need to be verified?',
            'placeholder' => '',
            'notice' => 'If this option is active, the client will be required to verify their email.',
        ],
        'CommunityRegistrationEmailVerificationPerDay' => [
            'name' => 'Allowed number of email verifications per 24-hours',
            'placeholder' => 'Numbers per 24-hours',
            'notice' => 'The system will allow the client to receive email verifications every 24-hours for the number specified in this section. (Note: due to the fact that the user does not login on the site, this feature identifies the user based on other information such as session, email, etc., so there is a possibility of not recognizing a duplicate client by changing these specifications)',
        ],
        'CommunityRegistrationEmailVerificationExpirationMinutes' => [
            'name' => 'Email verification expiration time',
            'placeholder' => 'Email verification expiration time base on minutes',
            'notice' => 'Until this time expires after sending the verification email, the user will not be able to request to receive a new email verification. (base on minutes)',
        ],
        'CommunityRegistrationEmailVerificationExpirationMinutesCoefficient' => [
            'name' => 'The coefficient of increasing the email verification expiration time',
            'placeholder' => 'The coefficient of increasing the email verification expiration time',
            'notice' => "In each request to send email verification, the set expiration time is multiplied by this number and increases the next email verification time. (If you don't need this item, you can set it to 0)",
        ],
        'CommunityRegistrationEmailVerificationText' => [
            'name' => 'Email verification text message',
            'placeholder' => 'Email verification text message',
            'notice' => 'In this section, you can set the text of the email verification. (verification code variable: {verificationCode})',
        ],

        /* CommunityPasswordRecovery-tab */
        'CommunityPasswordRecoveryIsActive' => [
            'name' => 'Is the community password recovery active?',
            'placeholder' => '',
            'notice' => 'If this option is enabled, users can recover their forgotten password in the community.',
        ],
        'CommunityPasswordRecoveryMethods' => [
            'name' => 'Password recovery methods',
            'placeholder' => '',
            'notice' => 'Choose the methods you want users to use in password recovery.',
        ],
        'CommunityPasswordRecoveryDefaultMethod' => [
            'name' => 'Default password recovery method',
            'placeholder' => '',
            'notice' => 'Choose the default password recovery method.',
        ],
        'CommunityPasswordRecoveryMobileVerificationPerDay' => [
            'name' => 'Allowed number of recoveries by mobile',
            'placeholder' => 'Number every 24 hours',
            'notice' => 'If mobile password recovery is active, the system will allow the user to use this method every 24 hours for the number of times specified in this section.',
        ],
        'CommunityPasswordRecoveryMobileVerificationExpirationMinutes' => [
            'name' => 'SMS verification expiration time',
            'placeholder' => 'SMS verification expiration time base on minutes',
            'notice' => 'Until this time expires after sending the verification SMS, the user will not be able to request to receive a new mobile verification SMS. (base on minutes)',
        ],
        'CommunityPasswordRecoveryMobileVerificationText' => [
            'name' => 'Mobile number verification text message',
            'placeholder' => 'Mobile number verification text message',
            'notice' => 'In this section, you can set the text of the mobile number verification SMS. (verification code variable: {verificationCode})',
        ],
        'CommunityPasswordRecoveryEmailVerificationPerDay' => [
            'name' => 'Allowed number of recoveries by email',
            'placeholder' => 'Number every 24 hours',
            'notice' => 'If password recovery by e-mail is enabled, the system will allow the user to use this method as many times as specified in this section every 24 hours.',
        ],
        'CommunityPasswordRecoveryEmailVerificationExpirationMinutes' => [
            'name' => 'Email verification expiration time',
            'placeholder' => 'Email verification expiration time base on minutes',
            'notice' => 'Until this time expires after sending the verification email, the user will not be able to request to receive a new email verification. (base on minutes)',
        ],

        /* Chatbot-tab */
        'ChatbotProfileImage' => [
            'name' => 'Chatbot Profil Image',
            'placeholder' => 'Choose your chatbot profil image',
            'notice' => 'In this section, you can change the chatbot profil image.',
        ],
        'ChatbotInactiveChatsExpirationHours' => [
            'name' => 'Time to close inactive chats',
            'placeholder' => 'Time to close inactive chats (based on the hour)',
            'notice' => 'Inactive user chats with the chatbot will be closed after this time (based on the hour between 1 and 72 hours).',
        ],
        'ChatbotClosedChatsDaysOfKeeping' => [
            'name' => 'The days of keeping closed chats',
            'placeholder' => 'The number of days of keeping closed chats',
            'notice' => 'Closed chats of clients with chatbot will be removed from the system after this time (based on the day between 1 and 60 days).',
        ],

        /* Ticket-tab */
        'TicketWaitingClientTicketsExpirationHours' => [
            'name' => 'Time to close requests waiting for the client',
            'placeholder' => 'Time to close requests waiting for the client (based on the hour)',
            'notice' => 'The requests that are waiting for the client\'s response will be closed after this time (based on the time between 1 and 72 hours).',
        ],
        'TicketClosedTicketsDaysOfKeeping' => [
            'name' => 'The days of keeping closed requests',
            'placeholder' => 'The number of days of keeping closed requests',
            'notice' => 'Closed requests will be removed from the system after this time (based on the day between 1 and 365 days).',
        ],

        /* Referral-tab */
        'ReferralIsActive' => [
            'name' => 'Is the referral program active?',
            'placeholder' => '',
            'notice' => 'If this option is enabled, site users in the category of users you have allowed can invite their friends and use the referral program.<br>* Be sure to check the <a href="/admin/clients/clients-categories_permissions" target="_blank">Clients Categories Permissions</a> before activating the program and make sure the authorized accesses are correct.',
        ],
        'ReferralIsActiveForTestClients' => [
            'name' => 'Is the referral program active for test clients?',
            'placeholder' => '',
            'notice' => 'If this option is enabled, test clients can access to referral program. To change the client to a test client, you must go to the <a href="/admin/clients/betconstruct-clients-management" target="_blank">clients management</a> section and mark the test account check box for the desired user.<br>* Note that this option is for testing the app before publicly enabling the app, so if the app is publicly enabled and this option is disabled, test clients will still have access to the referral program.',
        ],
        'ReferralAutoRenewLastSession' => [
            'name' => 'Automatic renewal of the last session of referral',
            'placeholder' => '',
            'notice' => 'If this option is enabled, if the current in-progress session is the last session in the list of time sessions, it will be automatically renewed with the same specifications.',
        ],
        'ReferralPageNote' => [
            'name' => 'Note',
            'placeholder' => 'Note',
            'notice' => 'If needed, you can write a text in this section so that it can be displayed to users at the top of the page related to this section. You can also use HTML codes in this text.',
        ],

        /* Bet-tab */
        'BetDaysOfKeepingHistory' => [
            'name' => 'The days of keeping bets history',
            'placeholder' => 'The number of days of keeping bets history',
            'notice' => 'The history of clients\' bets will be kept in the system for the period specified in this section, and older bets will be deleted from the system. (based on days between 30 and 100 days)',
        ],

        /* AppRules-tab */
        'TermsAndConditions' => [
            'name' => 'Terms and Conditions',
            'placeholder' => 'Terms and Conditions',
            'notice' => '',
        ],
    ],
];
