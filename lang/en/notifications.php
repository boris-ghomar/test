<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notifications Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during notifications for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */


	/*
    |--------------------------------------------------------------------------
    | Notifications Builders Lines
    |--------------------------------------------------------------------------
    |
    | Each notification needs a name and message, etc to be translated
	| in this section. And they is arranged by its builder name.
    |
    |
    */

    'RuntimeError' => [
        'subject' => 'Runtime Error!',
        'message' => "An error occurred while running the app.\n :data",
    ],

    'NewPersonnelAddedToRole' => [
        'subject' => 'New personnel added to role!',
        'message' => "A new personnel with the username :username has been added to the :role role by :operator .",
    ],

	'PersonnelRoleChanged' => [
        'subject' => 'Personnel role changed!',
        'message' => "Personnel role with username :username has been changed by :operator from :oldRole to :newRole.",
    ],

	'YourRoleChanged' => [
        'subject' => 'Your role Changed!',
        'message' => "Your role has been changed by :operator from :oldRole to :newRole.",
    ],

    'PersonnelDeleted' => [
        'subject' => 'Personnel deleted!',
        'message' => "Personnel with username :username in role :role has been deleted by :operator.",
    ],

    'YourCommentReplied' => [
        'subject' => 'New reply for your comment',
        'message' => "The :replyOwnerDisplayName posted a comment in response to your comment.",
    ],

    'YourCommentPublished' => [
        'subject' => 'Your comment was published on the site',
        'message' => "Your comment was published in the post :postTitle.",
    ],

    'YourWaitingTicketExpired' => [
        'subject' => 'Your request has expired',
        'message' => "Due to your lack of response in the last :maxWaitingHours hours, your request(ID: :ticketId) has expired and was closed by the system.",
    ],

    'YourTicketWaitingYourResponse' => [
        'subject' => 'Request waiting for response',
        'message' => "Support in request (ID :ticketId) is waiting for your response, please act as soon as possible.",
    ],

    'YourTicketClosed' => [
        'subject' => 'The request was closed',
        'message' => "Your request (ID: :ticketId) was closed by support.",
    ],

    'ReferralRewardPaymentDone' => [
        'subject' => 'Your reward has been paid',
        'message' => "Congratulations!! A :amount \":rewardName\" :rewardType reward was paid to you by the referral program.",
    ],
];
