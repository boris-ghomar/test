<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Confirm Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during confirm for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    //Delete.simple
	'Delete' => [
		'simple'		=>	'Do you really want to delete this item?',
		'simpleName'	=>	'Do you really want to delete the ":Name" ?',
		'jsGrid'	=> [
			'default'	=> 'function(item){return \'The "\' + item.:col_name + \'" will be removed. Are you sure?\';}',
		],

        'AllNotifications' => [
            'Header' => 'Confirm delete all notifications',
            'Body' => "This action cannot be reversed. Are you sure you want to delete all notifications?",
        ],
	],

];
