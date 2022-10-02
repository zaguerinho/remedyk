<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-patients',
	'language' => 'en',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'checkActivation'],
    'controllerNamespace' => 'patients\controllers',
    'components' => [
    	'checkActivation' => [
    		'class'=> 'patients\components\CheckActivation',
    	],
    	'authClientCollection' => [
    		'class' => 'yii\authclient\Collection',
    		'clients' => [
    			'google' => [
    				'class' => 'yii\authclient\clients\Google',
    				'clientId' => 'API Key',
    				'clientSecret' => 'AIzaSyDtVX3NVEmL89r9phYzpslXeY2bOczD9BE'
    			],
    			'facebook' => [
    				'class' => 'yii\authclient\clients\Facebook',
    				//'authUrl' => 'https://www.facebook.com/dialog/oauth?display=popup',
    				'clientId' => '198093197452443',
    				'clientSecret' => '45a267a220f95ca68dfb8d2f0981a753',
    			],
    		],
    	],
        'request' => [
            'csrfParam' => '_csrf-patients',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-patients', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the patients
            'name' => 'advanced-patients',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        'urlManager' => [
            //'enablePrettyUrl' => true,
            //'showScriptName' => false,
            'rules' => [
            	'site/signup/<profileType>' => 'site/signup',
            	'site/signup/<profileType>/<doctor_id>/<office_id>' => 'site/signup',
            ],
        ],
        
    ],
    'params' => $params,
];
