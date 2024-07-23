<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Logs Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during system logs for various
    | messages that we need to display to the user or personnel. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'domainExtension' => [

        'store'     => 'New domain extension :name added by :authUser.',
        'update'    => 'Domain extension :name updated by :authUser.',
        'destroy'   => "Domain extension :name deleted by :authUser.\n\nDetails:\n:details",
    ],

    'domainHolder' => [

        'store'     => 'New domain holder :domainHolderName added by :authUser.',
        'update'    => 'Domain holder :domainHolderName updated by :authUser.',
        'destroy'   => "Domain holder account :domainHolderName deleted by :authUser.\n\nDetails:\n:details",
    ],

    'domainHolderAccount' => [

        'store'     => 'New domain holder account :domainHolderName: :domainHolderUsername added by :authUser.',
        'update'    => 'Domain holder account :domainHolderName: :domainHolderUsername updated by :authUser.',
        'destroy'   => "Domain holder account :domainHolderName: :domainHolderUsername deleted by :authUser.\n\nDetails:\n:details",
    ],

    'domainCategory' => [

        'store'     => 'New domain category :domainCategory added by :authUser.',
        'update'    => 'Domain category :domainCategory updated by :authUser.',
        'destroy'   => "Domain category :domainCategory deleted by :authUser.\n\nDetails:\n:details",
    ],

    'domains' => [

        'store'     => 'New domain :name added by :authUser.',
        'update'    => 'Domain :name updated by :authUser.',
        'destroy'   => "Domain :name deleted by :authUser.\n\nDetails:\n:details",
    ],

    'cdnProvider' => [

        'store'     => 'New CDN provider :name added by :authUser.',
        'update'    => 'CDN provider :name updated by :authUser.',
        'destroy'   => "CDN provider :name deleted by :authUser.\n\nDetails:\n:details",
    ],

    'cdnAccount' => [

        'store'     => 'New CDN account :name added by :authUser.',
        'update'    => 'CDN account :name updated by :authUser.',
        'destroy'   => "CDN account :name deleted by :authUser.\n\nDetails:\n:details",
    ],

    'sharedCredential' => [

        'store'     => 'The :shareableName account was shared with :userUsername by :authUser.',
        'update'    => 'The shared :shareableName account was updated by :authUser.',
        'destroy'   => 'Sharing :shareableName account credentials with :userUsername was deleted by :authUser.',
    ],


];
