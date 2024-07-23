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
        'subject' => 'خطای زمان اجرا!',
        'message' => "در زمان اجرای برنامه خطایی رخ داده است. \n :data",
    ],

    'NewPersonnelAddedToRole' => [
        'subject' => 'پرسنل جدید به گروه کاری اضافه شد!',
        'message' => "یک پرسنل جدید با نام کاربری :username توسط :operator به نقش کاری :role اضافه شده است.",
    ],

	'PersonnelRoleChanged' => [
        'subject' => 'نقش کاری پرسنل تغییر کرد!',
        'message' => "نقش کاری کاربر با نام کاربری :username توسط :operator از :oldRole به :newRole تغییر کرده است.",
    ],

	'YourRoleChanged' => [
        'subject' => 'نقش کاری شما تغییر کرد!',
        'message' => "نقش کاری شما توسط :operator از :oldRole به :newRole تغییر کرده است.",
    ],

    'PersonnelDeleted' => [
        'subject' => 'پرسنل حذف شد!',
        'message' => "پرسنلی با نام کاربری :username در نقش :role توسط :operator حذف شده است.",
    ],

    'YourCommentReplied' => [
        'subject' => 'نظر جدید برای کامنت شما',
        'message' => ":replyOwnerDisplayName یک نظر در پاسخ به نظر شما ارسال کرده است.",
    ],

    'YourCommentPublished' => [
        'subject' => 'نظر شما در سایت منتشر شد',
        'message' => "نظر شما در پست :postTitle منتشر شد.",
    ],

    'YourWaitingTicketExpired' => [
        'subject' => 'درخواست شما منقضی شد',
        'message' => "به دلیل عدم پاسخ شما در :maxWaitingHours ساعت گذشته، درخواست شما (شناسه: :ticketId) منقضی و توسط سیستم بسته شد.",
    ],

    'YourTicketWaitingYourResponse' => [
        'subject' => 'درخواست در انتظار پاسخ',
        'message' => "پشتیبان در درخواست (شناسه :ticketId) منتظر پاسخ شما است، لطفا سریع تر اقدام کنید.",
    ],

    'YourTicketClosed' => [
        'subject' => 'درخواست بسته شد',
        'message' => "درخواست شما (شناسه: :ticketId) توسط پشتیبان بسته شد.",
    ],

    'ReferralRewardPaymentDone' => [
        'subject' => 'پاداش شما پرداخت شد',
        'message' => "تبریک!! مبلغ :amount :rewardType \":rewardName\" بابت پاداش توسط برنامه معرفی به دوستان به شما پرداخت شد.",
    ],
];
