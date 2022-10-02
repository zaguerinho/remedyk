<?php
require_once(__DIR__.'/functions.php');

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
		'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

            ],
        ],
    ],
	'modules' => [
		'gridview' =>  [
			'class' => '\kartik\grid\Module',
		]
	]
];
