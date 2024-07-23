<?php

return [

    /*
    |--------------------------------------------------------------------------
    | "Post Edit" Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in "Post Edit" page
    | for various messages that we need to display to the user. You are free
    | to modify these language lines according to your application's requirements.
    |
    */

    'cardTitleCreate' => 'Create post',
    'cardDescriptionCreate' => "In this section you can create new post.",

    'cardTitleEdit' => 'Edit post',
    'cardDescriptionEdit' => "In this section you can edit post.",

    'tab' => [

        'Content' => [
            'title' => 'Content',
            'descriptionTitle' => 'What does the "Content" section do?',
            'descriptionText' => 'In this section you can edit the content of the post.',
        ],

        'MainPhoto' => [
            'title' => 'Main Photo',
            'descriptionTitle' => 'What does the "Main Photo" section do?',
            'descriptionText' => 'In this section you can edit the main photo of the post.',
        ],

        'SEO' => [
            'title' => 'SEO',
            'descriptionTitle' => 'What does the "SEO" section do?',
            'descriptionText' => 'In this section, you can edit the items related to SEO of the post.',
        ],


    ],

    'messages' => [
        'SavedSuccessfully' => 'Post successfully saved.',
        'UpdatesSuccessfully' => 'Post successfully updated.',
        'SavedFailed' => 'Failed to save post.',
    ],

    'form' => [

        /* Content-tab */
        'post_space_id' => [
            'name' => 'Post Space',
            'placeholder' => '',
            'notice' => "",
        ],
        'is_published' => [
            'name' => 'Publish the post',
            'placeholder' => '',
            'notice' => "If this option is enabled, this post will be displayed to users.<br/>Also, if the post is not yet ready to be published and you are just saving step by step, leave this option off so that the final check of the post publication is not done.",
        ],
        'title' => [
            'name' => 'Title',
            'placeholder' => 'Title',
            'notice' => 'Try to keep the title of the post between 40 and 60 characters.',
        ],
        'content' => [
            'name' => 'Content',
            'placeholder' => 'Start your awesome content ...',
            'notice' => '',
        ],

        /* MainPhoto-tab */
        'main_photo' => [
            'name' => 'Main image of the post',
            'placeholder' => 'Select the image',
            'notice' => 'This image is the index of the post, which is displayed in the list of posts, the beginning of the post and any part that is needed.',
        ],

        /* SEO-tab */
        // placed in general.SeoMetaTags
    ],
];
