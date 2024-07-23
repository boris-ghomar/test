<?php

return [

    /*
    |--------------------------------------------------------------------------
    | This app  Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in this app for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'AppName' => 'Betcart Players Club',

    'SiteAdmin' => 'Betcart Admin',
    'Betconstruct' => 'Betconstruct',
    'ClientCategory' => 'Client Category',
    'PrivateNote' => 'Private Note',
    'WithoutParent' => 'Without Parent',
    'PostSpace' => 'Post Space',
    'Post' => 'Post',
    'ReadMore' => 'Read more and send questions and comments...',
    'Comments' => 'Comments and Questions',
    'IsApproved' => 'Is Approved?',
    'ApprovedBy' => 'Approved By',
    'UserId' => 'User ID',
    'PostId' => 'Post ID',
    'CommentId' => 'Comment ID',
    'PostLink' => 'Post Link',
    'CommentLink' => 'Comment Link',
    'Owner' => 'Owner',
    'BetconstructId' => 'Betconstruct Id',
    'AttachedFile' => 'Attached File',
    'SearchResults' => 'Search Results',
    'SearchResultsFor' => 'Search results for: ',
    'SearchResultInfo' => ':resultCount results (:time seconds)',
    'Priority' => 'Priority',
    'DisplayPriority' => 'Display Priority',
    'PaymentPriority' => 'Payment Priority',
    'Item' => 'Item',
    'GetBetcartLink' => 'Click on the button to get the latest Betcart site address.',
    'Responder' => 'Responder',
    'GuestUser' => 'Guest User',
    'Views' => 'Views',
    'unverifiedEmail' => 'Your email has not been verified.',
    'unverifiedMobile' => 'Your mobile number has not been verified.',
    'JobField' => 'Job Field',
    'ContactNumbers' => 'Contact Numbers',
    'ContactMethods' => 'Contact Methods',
    'CallerGender' => 'Caller Gender',
    'DisplayName' => 'Display Name',
    'SystemSettings' => 'System Settings',
    'IsoName' => 'ISO Name',
    'StartTime' => 'Start Time',
    'FinishTime' => 'Finish Time',
    'SystemMessage' => 'System Message',

    'ChartUpdatePeriod' => '* Chart data is updated every :updatePeriod. Time left until next update:  :nextCalculationTime',

    'GlobalRoutes' => [
        'MenuTitle' => 'Global Permission',
        'ViewClientEmail' => 'View Client Email',
        'ViewClientPhone' => 'View Client Phone',
    ],

    'Support' => [
        'Support' => 'Support',
        'Chatbot' => 'Chatbot',
    ],

    'Buttons' => [
        'AddNewPost' => 'Add New Post',
        'Send' => 'Send',
        'Search' => 'Search',
        'Answering' => 'Answering',
        'GoToSite' => 'Go to site',
        'GetBetcartAddress' => 'Get Betcart Address',
        'ReportIssue' => 'Report Issue',
    ],

    'placeholder' => [
        'comment' => 'Write your comment or question...',
        'message' => 'Write your message ...',
        'SearchHere' => 'Search Here',
    ],

    'PostActions' => [
        'View' => 'View',
        'Like' => 'Like',
        'Comment' => 'Comment',
        'successCommentTitle' => 'Your comment or question has been sent',
    ],

    'linkList' => [
        'pageTitle' => 'List of :title links'
    ],

    'messages' => [
        'commentRegisteredSuccessfully' => 'Your comment has been registered successfully.',
        'commentRegisteredSuccessfullyApproval' => 'Your comment has been successfully registered and will be published after approval.',
    ],

    'CustomPages' => [
        'IpRestriction' => [
            'Title' => 'IP Restriction',
            'GetSiteURL' => 'Get Site URL',
            'CopySiteURL' => 'Copy Site URL',
            'SiteURLCopied' => 'The site address has been copied to memory, now you can post in your browser.',
            'GetSiteURLDescr' => 'Click the button below to get the site address.',
            'UnsupportedIP' => 'Turn off your VPN and then click the login button.',
            'UnsupportedIPDirectLink' => 'Or you can enter the following address manually in your browser.',
        ],
    ],

    'DynamicData' => [
        'VarName' => 'Variable Name',
        'VarValue' => 'Variable Value',
    ],

    'confirm' => [
        'RemoveFromPinnedPosts' => "Are you sure to remove the ':col_name' post from the pinned posts list?",
    ],

    'Chatbot' => [
        'StepActions' => [
            'Move' => 'Move',
            'Edit' => 'Edit',
            'DeleteStep' => 'Delete this step',
            'DeleteWithChilds' => 'Delete step with childs',
        ],

        'Messenger' => [
            'PageTitle' => 'Chatbot Messenger',

        ],
    ],

    'ReportedDomainReview' => [
        'blocked' => 'Blocked',
        'works' => 'Without problem',
    ],

    'JobFields' => [
        // The keys used in database
        'Job1' => 'Financial and administrative',
        'Job2' => 'Sales, marketing and business',
        'Job3' => 'Education and Research',
        'Job4' => 'service and social services',
        'Job5' => 'Computer and information technology',
        'Job6' => 'Legal and military',
        'Job7' => 'connections',
        'Job8' => 'Healthcare',
        'Job9' => 'Transportation',
        'Job10' => 'Art',
        'Job11' => 'Agriculture and environment',
        'Job12' => 'Real estate and construction',
        'Job13' => 'Engineering technology',
        'Job14' => 'Cosmetics and clothing',
        'Job15' => 'print and advertisement',
        'Job16' => 'Travel and tourism',
        'Job17' => 'Mines and metals',
        'Job18' => 'Foodstuffs',
        'Job19' => 'Industries and factories',
    ],

    /*
    |--------------------------------------------------------------------------
    | This app  Errors
    |--------------------------------------------------------------------------
    */

    'Errors' => [
        'accessDenied' => 'Access denied.',
        'Forbidden' => 'You are not allowed to access this page.',
        'loginRequired' => 'You must login to do this.',
        'likeBlock' => 'You are not allowed to like in this section.',
        // 'commentBlock' => 'You are not allowed to comment in this section.',
        'commentBlock' => 'For support requests, please refer to the support chat system.',
        'emptyComment' => 'Your comment is empty!',
        'repeatedcomment' => 'Your comment is repetitive!',
        'WrongInputData' => 'The input information is not correct.',

        'rules' => [
            'IsUserExists'      => 'This user is not exists.',
            'IsUserPersonnel'   => 'This user is not Personnel.',
            'IsUserActive'      => 'This user is not Active.',
        ],

        'Settings' => [
            'AppIsInactive' => 'The application is currently inactive.',
        ],

        'Rules' => [
            'HasUser' => 'The :name has user, first move the users of this group to another group or delete them.',
        ],

        'PostGrouping' => [
            'PostCategoryHasSubCategory' => 'The :name has sub-category, first move them to another category or delete them.',
            'PostCategoryHasSpace' => 'The :name has content space, first move them to another category or delete them.',
            'PostSpaceHasPost' => 'The :name sapce has post, first move them to another space or delete them.',
        ],

        'Posts' => [
            'Unpublishable' => 'This post is publishable due to the following errors.',
            'MainPhotoRequired' => 'Post must have main photo to be published.',
            'MinRequiredWords' => 'To be published, the post must have at least :minWordsCount words in the post content, but your post content has :wordsCount words.',
            'AlreadyPinned' => 'This post has already been pinned.',
            'PostNotExist' => 'No posts were found with this ID.',
        ],

        'Comments' => [
            'PostNotFound' => 'Post with this post ID was not found.',
            'UserNotFound' => 'User with this user ID was not found.',
            'CommentDeleted' => 'This comment has been deleted.',
        ],


        'Chatbot' => [
            'ChatbotNotFound' => 'Chatbot was not found.',
            'chatbotMoveTypeRequired' => 'The move type is required.',
            'chatbotMoveTargetIdRequired' => 'The target step ID is required.',
            'chatbotMoveTargetStepNotFound' => 'There is no step with the ID: :targetStepId',
            'chatbotMoveTargetStepIsFinalStep' => 'The target step is a final step and it is not possible to add a step.',
            'chatbotMoveSameId' => 'The id of the current step and the target step should not be the same.',
            'chatbotMoveToSubset' => 'It is not possible to move a step to its own sub-step.',
        ],

        'ChatbotMessenger' => [

            'accessDenied' => 'Access denied.',
            'loginRequired' => 'You must login to do this.',
            'activeChatbotNotFound' => 'There is currently no active chatbot to respond, please try again later.',
            'chatClosed' => 'This chat has been closed.',
            'imageRemoved' => 'The image has been removed.',
            'imageNotUploaded' => 'Image failed to upload properly, please try again.',
            'FailedToMakeTicket' => 'Sorry, there was a problem while creating your request. Your problem has been reported and will be checked and fixed by the technical team soon.',
            'clientHasSameOpenTicket' => 'You have open request on the same topic, please wait for a reply first.',
            'clientReachedToTicketLimit' => 'In the last :hourLimit hours, :numberLimit requests similar to yours have been answered, and you can send a new request :remainingTime later.',
        ],

        'TicketMessenger' => [

            'accessDenied' => 'Access denied.',
            'ticketNotFound' => 'Request not found.',
            'ticketClosed' => 'This request has been closed.',
            'imageRemoved' => 'The image has been removed.',
            'imageNotUploaded' => 'Image failed to upload properly, please try again.',
        ],

        'Tickets' => [
            'TicketDeleted' => 'This request has been deleted.',
        ],

        'DomainHolder' => [
            'HasAccount' => 'The :name has account, first move the accounts of this domain holder to another domain holder or delete them.',
        ],

        'DedicatedDomains' => [
            'DomainNotFound' => 'A domain was not found in the database to assign.',
        ],

        'Profile' => [
            'EmailVerified' => 'This email is already verified and does not need to be verified again.',
        ],

        'Referral' => [
            'ReferralRewardPackageNotFound' => 'Reward package was not found.',

            'ReferralSessionDateConflict' => 'The specified time interval has a time overlap with the ":name" item.',
            'ReferralSessionStartDateConflict' => 'The specified start time interval has a time overlap with the ":name" item.',
            'ReferralSessionFinishDateConflict' => 'The specified finish time interval has a time overlap with the ":name" item.',

            'ReferralSessionUpdateOnlyUpcoming' => 'It is possible to edit calculation fields only for items with ":status" status.',
            'ReferralSessionDestroyError' => 'The ":name" case is being used by the system and it is not possible to remove it at this time.',

            'ReferralRewardPackageUpdateRule' => [
                'UsedInReferralSession' => 'The :packageName item is being used in the execution :sessionName session, and it is not possible to change the :attribute until the processing is complete.',
                'UsedInReferralCustomSetting' => 'The :packageName reward package has been used in client custom settings and it is not possible to change the :attribute, you must delete or change that item first.',
            ],

            'ReferralRewardPackageDeleteRule' => [
                'UsedInReferralSession' => 'The :packageName item is being used in the execution :sessionName session, and it cannot be deleted until the processing is complete.',
                'UsedInReferralCustomSetting' => 'The :packageName reward package has been used in client custom settings and it is not possible to delete it, you must delete or change that item first.',
            ],

            'ReferralRewardItemUpdateRule' => [
                'UsedInReferralSession' => 'The :itemName item is being used in the execution :sessionName session, and it is not possible to change the :attribute until the processing is complete.',
                'UsedInRewardPayment' => 'The :itemName item is in the payment queue for clients, and it is not possible to change the :attribute until the processing is complete.',
            ],

            'ReferralRewardItemDeleteRule' => [
                'UsedInReferralSession' => 'The :itemName item is being used in the execution :sessionName session, and it is not possible to delete it until the processing is complete.',
                'UsedInRewardPayment' => 'The :itemName item is in the payment queue for clients, and it is not possible to delete it until the processing is complete.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | This app  Enums
    |--------------------------------------------------------------------------
    */

    'Enum' => [

        'PermissionTypeEnum' => [

            'AdminPanel' => 'Admin Panel',
            'Site' => 'Site',
        ],

        'TemplatesEnum' => [
            'FAQ' => 'FAQ',
            'Article' => 'Article',
            'PhotoGallery' => 'Photo Gallery',
            'VideoGallery' => 'Video Gallery',
        ],

        'SystemReservedEnum' => [
            'SystemSettings' => 'System Settings',
        ],

        'ClientCategoryMapTypesEnum' => [

            'CustomCategory' => 'Custom Category',
            'LoyaltyLevel' => 'Loyalty Level',
        ],

        'DynamicData' => [

            'IpRestriction' => [
                'SectionTitle' => 'Ip Restriction',
                'Explanation' => 'Explanation Text',
                'SiteLink' => 'Site Link',
            ],
            'Comment' => [
                'SectionTitle' => 'Comment',
                'CommentRegistrationExplanation' => 'Explanation after comment registration',
            ],
            'Search' => [
                'SectionTitle' => 'Search',
                'SearchGuideText' => 'Search guide text',
            ],
        ],

        'ChatbotActions' => [
            'BotResponse' => 'Bot Response',
            'PickList' => 'Pick List',
        ],

        'ChatbotSteps' => [
            'UserInput' => 'User Input',
            'BotResponse' => 'Bot Response',
            'Filter' => 'Filter',
            'BotAction' => 'Action',
        ],

        'ChatbotResponseTypes' => [
            'Text' => 'Text',
            'RandomText' => 'Random Text',
            'Image' => 'Image',
            'Button' => 'Button',
        ],

        'ChatbotUserInputTypes' => [
            'Number' => 'Number',
            'OneLineText' => 'One line Text',
            'MultipleLineText' => 'Multiple line text',
            'Image' => 'Image',
        ],

        'ChatbotFilterTypes' => [
            'ClientCategory' => 'Client Category',
        ],

        'ChatbotActionTypes' => [
            'End' => 'End',
            'GoToStep' => 'Go to step',
            'StartTicket' => 'Start collecting request information',
            'MakeTicket' => 'Make request',
            'OpenLiveChat' => 'Open live chat',
        ],

        'DomainStatusEnum' => [
            'Unknown' => 'Unknown',
            'Preparing' => 'Preparing',
            'ReadyToUse' => 'Ready to use',
            'InUse' => 'In use',
            'Blocked' => 'Blocked',
            'Expired' => 'Expired',
        ],

        'ReferralRewardTypeEnum' => [
            'CashBack' => 'Cash Back',
            'Bonus' => 'Bonus',
        ],

        'ReferralRewardPaymentSatusEnum' => [
            'InProgress' => 'InProgress',
            'PaymentQueue' => 'Payment Queue',
            'Paid' => 'Paid',
        ],

        'PartnerEnum' => [
            'Betconstruct' => 'Betconstruct',
        ],

        'ReferralSessionStatusEnum' => [
            'PayingRewards' => 'Paying Rewards',
            'InProgress' => 'In Progress',
            'Upcoming' => 'Upcoming',
            'Finished' => 'Finished',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | This app  Admin Pages
    |--------------------------------------------------------------------------
    */

    'AdminPages' => [

        'Dashboard' => [
            'Domains' => [
                'AssignableDomainsStatistics' => 'Statistics of assignable domains',
                'Count' => 'Count',
            ],
        ],

        'AccessControl' => [

            'Permissions' => [
                'Route' => 'Route',
                'Ability' => 'Ability',
                'Type' => 'Permission Type',
            ],
        ],

        'ClientsManagement' => [
            'MapType' => 'Map Type',
            'Value' => 'Input Value',
            'TrustScore' => 'Trust Score',
            'DomainSuspicious' => 'Domain Suspicious Score',
            'IsEmailVerified' => 'Verified email',
            'IsProfileCompleted' => 'Completed profile',
            'IsProfileFurtherInfoCompleted' => 'Completed profile further information',
        ],

        'PostGrouping' => [
            'ParentId' => 'Parent Category',
            'Description' => 'Description',
            'Template' => 'Template',
            'Photo' => 'Photo',
            'IsPublicSpace' => 'Is Public?',
            'Position' => 'Position',

            'PostSpaceId' => 'Post Space',
            'ClientCategoryId' => 'Client Category',
            'PostAction' => 'Ability',
        ],

        'Posts' => [
            'MainPhoto' => 'Main Photo',
            'Author' => 'Author',
            'EditedBy' => 'Edited By',
            'PinNumber' => 'Pin Number',
        ],

        'Comments' => [
            'DisplayName' => 'Name',
            'Answer' => 'Answer',
        ],

        'Chatbot' => [
            'ChatbotName' => 'Chatbot Name',
            'EditChatbot' => 'Edit Chatbot',
            'StartPoint' => 'Start Point',
            'Step' => 'Step',
            'StepId' => 'Step ID',
            'MoveType' => 'Move Type',
            'MoveUnder' => 'Move to under ...',
            'MoveAfter' => 'Move to after ...',
            'MoveBefore' => 'Move to before ...',

            'Form' => [
                'StepTitle' => 'Step Title',
                'StepDelay' => 'Delay (Second)',
                'StepType' => 'Answer Type',

                'ResponseText' => 'Response Text',
                'ResponseImage' => 'Response Image',

                'CurrentImage' => 'Current Image: ',

                'ButtonTitle' => 'Button Title',
                'ButtonType' => 'Button Type',
                'ButtonTypesUrl' => 'URL',
                'ButtonTypes' => [
                    // Keys used in HTML form and PHP
                    'GoToStep' => 'Go to step',
                    'OpenUrl' => 'Open URL',
                ],

                'UserInput' => [
                    'StepType' => 'User input Type',
                    'Title' => 'Title',
                    'Description' => 'Description',
                    'Placeholder' => 'Placeholder',
                    'RequiredField' => 'Required field',
                    'Minimum' => 'Minimum',
                    'Maximum' => 'Maximum',
                    'MinLength' => 'Minimum number of input characters',
                    'MaxLength' => 'Maximum number of input characters',

                    'IgnoreValidation' => 'In the field of validation entries, if you do not need to check the item, leave that field blank.',
                ],

                'Filter' => [
                    'StepType' => 'Filter Type',
                    'AllowedClientCategories' => 'Allowed client categories',
                ],

                'BotAction' => [
                    'StepType' => 'Action Type',
                    'TicketSubject' => 'Request Subject',
                    'TicketPriority' => 'Request Priority',
                    'TicketSendingSchedule' => 'Request sending schedule',
                    'TicketSendingScheduleDescr' => 'This setting specifies the maximum number of times the user can send this request in each cycle of the specified time.\nNote: If the user has a similar request that has not been closed, they cannot create a new and duplicate request even if it is allowed within the time limit.',
                    'HourLimit' => 'Hour Limit',
                    'NumberLimit' => 'Number Limit',
                    'ScheduleFaildTargetStep' => 'If the user\'s access to create a request is denied, the user will be transferred to this step.',
                ],
            ],

            'ChatbotConfirm' => [
                'DeleteStep' => 'Deleting a step is irreversible, are you sure you want to delete the ChatbotDeleteConfirm_StepTitle step?',
            ],


        ],

        'Tickets' => [
            'TicketID' => 'Request ID',

            'TicketMessenger' => [
                'pageTitle' => 'Request answering',
                'ClientNotSentImage' => 'No image has been sent.',
                'TicketStatusChanged' => "Temporary message (this message is only visible to you)\nRequest status successfully changed to \":newStatus\" status.",
                'FormSaved' => "Temporary message (this message is visible only to you)\nInformation saved successfully.",
            ],
        ],

        'Referral' => [
            'BonusIdRequiredDescr' => 'If you choose a bonus as a reward',
            'ReferralCustomSettingDescr' => 'If you want to set the reward settings of the referral program for specific users differently from the general settings, use this section. Note that these settings will take precedence over the general settings of the site.',
            'RewardPackage' => 'Reward Package',
            'RewardType' => 'Reward Type',
            'BonusId' => 'Bonus ID',
            'RewardPercentage' => 'Reward Percentage',
            'ClaimCount' => 'Claim Count',
            'ReferredById' => 'Referrer ID',
            'Referrer' => 'Referrer',
            'RewardName' => 'Reward Name',
            'IsPaymentProcessDone' => 'Is payment process done?',
            'IsPaymentSuccessful' => 'Is payment successful?',

            // Referral Sessions
            'MinBetCount' => 'Min bet count',
            'MinBetOdds' => 'Min bet odds',
            'MinBetAmount' => 'Min bet amount',

            'subTitle' => [
                'Referrer' => '(Referrer)',
                'ReferrerUsd' => '(Referrer - USD)',
                'ReferrerIrr' => '(Referrer - IRR)',
                'Referred' => '(Referred)',
                'ReferredUsd' => '(Referred - USD)',
                'ReferredIrr' => '(Referred - IRR)',
            ],
            // Referral Sessions END
        ],

        'DomainExtension' => [
            'limitedOrder' => 'Limited Order',
        ],

        'DomainsHoldersAccounts' => [
            'domainHolder' => 'Domain Holder',
            'domainHolderAccount' => 'Domain Holder Account',
        ],

        'Domains' => [
            'domainCategory' => 'Domain Category',
            'autoRenew' => 'Auto Renew',
            'registeredAt' => 'Registered At',
            'expiresAt' => 'Expires At',
            'announcedAt' => 'Announced At',
            'blockedAt' => 'Blocked At',
            'assignedAt' => 'Assigned At',
            'reportedAt' => 'Reported At',
            'DomainAssignment' => 'Domain Assignment',
            'Reported' => 'Reported',
            'ReportsCount' => 'Reports Count',
            'DesktopVerion' => 'Desktop Verion',
            'MobileVerion' => 'Mobile Verion',
            'ClientsCount' => 'Clients Count',
            'Public' => 'Public',
            'FakeAssigned' => 'Fake Assigned',
            'SuspiciousClients' => 'Suspicious Clients',
            'ClientTrustScoreCurrent' => 'Trust Score (Current)',
            'ClientTrustScoreAssignment' => 'Trust Score (Assignment)',
            'DomainSuspiciousCurrent' => 'Domain Suspicious Score (Current)',
            'DomainSuspiciousAssignment' => 'Domain Suspicious Score (Assignment)',
        ],

        'CurrencyRates' => [
            'OneUsdRate' => 'One US dollar rate',
        ],


    ],

    /*
    |--------------------------------------------------------------------------
    | This app Site Pages
    |--------------------------------------------------------------------------
    */

    'Site' => [

        'Dashboard' => [
            'BcPermenantUrl' => 'Betcart Permenant URL',
            'BcUnblockedUrl' => 'Betcart Unblocked URL',
            'telegramBotJoinLink' => [
                'title' => 'Connect to Telegram',
                'descr' => 'Click the button below to connect to Telegram and receive notifications.',
                'joinBtn' => 'Connect to Telegram',
            ],

            'Referral' => [
                'title' => 'Referral',
                'descr' => 'Get rewards by referring Betcart to others.',
                'btn' => 'Referral Panel',
            ],

            'msg' => [
                'DomainCopied' => 'The domain was copied to clipboard.',
                'DomainReportConfirm' => "Note: To use this address, you must turn off your VPN.\n\nAre you sure that the site with this address will not be displayed correctly for you without VPN?",
                'DomainReported' => 'The domain problem was reported and will be investigated by the expert team.',
                'DomainReplaced' => 'A new URL has been assigned to you, please use this URL.',
            ],
        ],

        'ChatbotChatsStatus' => [
            'Active' => 'Active',
            'Closed' => 'Closed',
        ],

        'TicketsStatusEnum' => [
            'ClientReplied' => 'Client Replied',
            'New' => 'New',
            'InProgress' => 'In Progress',
            'WaitingForClient' => 'Waiting for client',
            'Closed' => 'Closed',
        ],

        'TicketPrioritiesEnum' => [
            'Critical' => 'Critical',
            'High' => 'High',
            'Normal' => 'Normal',
            'Low' => 'Low',
        ],

        'Notifications' => [
            'EmptyList' => 'There are no notifications for the show.',
            'Date' => 'Date',
            'Title' => 'Title',
            'Message' => 'Message',
        ],

        'Tickets' => [
            'NoTicket' => 'There are no requests for the show.',
            'Subject' => 'Subject',
            'UpdatedAt' => 'Updated At',
            'Status' => 'Status',
        ],

        'ReferralPanel' => [
            'ReferralLink' => 'Referral Link',
            'ReferralLinkNote' => 'To invite friends, you can give this link to your friends or post it on your website and social networks.',

            'msg' => [
                'ReferralLinkCopied' => 'The referral link has been copied to clipboard.',
            ],

            'InprogressSession' => [
                'CardTitle' => 'Inprogress Session',
                'CardSubtitle' => 'In this section, you can see information about the inprogress referral session.',

                'RewardAmountLable' => 'Reward amount in the last calculation of the system for the current session',

                'Timing' => 'Timing',
                'RewardCalculationStartTime' => 'Calculation start time: ',
                'RewardCalculationEndTime' => 'Calculation end time: ',

                'TermsAndConditionsReferrer' => 'Your terms and conditions',
                'TermsAndConditionsReferred' => 'Referral clients terms and conditions',
                'MinBetsCount' => 'Minimum number of bets: ',
                'MinBetOdds' => 'Minimum odds of each bet: ',
                'MinBetAmount' => 'Minimum amount of each bet: ',
                'BetResult' => 'Bet result: WON | LOSS',

                'BetsExceptionsTitle' => 'Bets that are not considered in the calculations',
                'BetsExceptionItems' => [
                    'NonSportsBets' => 'Non-sports bets (such as casino, slots, etc.)',
                    'SystemTypeBets' => 'Sports system bets type',
                    'CashedOutBets' => 'Bets that have been partially or completely sold',
                ],

                'InProgressReward' => [
                    'title' => 'InProgress Reward',
                    'claimableDescr' => 'You can choose the reward you want from the list below (maximum :claimableCount items).',
                ],
            ],

            'ReferredPerformanceChart' => [
                'CardTitle' => 'Comparison chart of the number of referred users',
                'CardSubtitle' => 'Weekly comparison chart of the number of users referred by you in the last 14 days.',
            ],

            'RewardPerformanceChart' => [
                'CardTitle' => 'Chart of the latest rewards received',
                'CardSubtitle' => 'You can see your latest rewards received in this chart.',
            ],

            'Statistics' => [
                'IncreasedBy' => 'Increased by :percentage %',
                'DecreasedBy' => 'Decreased by :percentage %',
                'AllReferralsCount' => 'Total Introduced Users',
                'ActiveReferralsCount' => 'Total Active Users',
                'TotalReward' => 'Total Reward',
            ]
        ],
    ],


];
