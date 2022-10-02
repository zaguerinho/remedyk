<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'A70jJS-f_cKw7FK03NVONJIZgI8frHAc',
        ],
		'assetManager' => [
			'bundles' => [
				'common\assets\MaterialAdminLteAsset' => [
					'skin' => 'skin-remedyk',
					/*
					"skin-blue",
					"skin-black",
					"skin-red",
					"skin-yellow",
					"skin-purple",
					"skin-green",
					"skin-blue-light",
					"skin-black-light",
					"skin-red-light",
					"skin-yellow-light",
					"skin-purple-light",
					"skin-green-light"
					 */
				],
			],
    	],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['components']['assetManager']['linkAssets'] = true;
    //$config['components']['assetManager']['forceCopy'] = true;

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
    		'class' => 'yii\gii\Module',
    		'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],
    		'generators' => [
    				'crud' => [
    						'class' => 'yii\gii\generators\crud\Generator',
    						'templates' => [ // setting templates
    								'material-desing' => '@vendor/exocet/yii2-bootstrap-material-design/generators/material-design/crud',
    								'material-desing-h' => '@vendor/exocet/yii2-bootstrap-material-design/generators/material-design-h/crud',
    								'material-design-with-icons' => '@vendor/exocet/yii2-bootstrap-material-design/generators/material-design-with-icons/crud',
    								'material-design-h-with-icons' => '@vendor/exocet/yii2-bootstrap-material-design/generators/material-design-h-with-icons/crud'
    						]
    				]
    		],
    ];
}

return $config;
