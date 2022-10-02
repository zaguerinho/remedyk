<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-doctors',
	'language' => 'en',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'doctors\controllers',
    'bootstrap' => ['log', 'checkActivation'],
    //'modules' => [],
    'components' => [
    	'checkActivation' => [
    		'class'=> 'doctors\components\CheckActivation',
    	],
        'request' => [
            'csrfParam' => '_csrf-doctors',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-doctors', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the doctors
            'name' => 'advanced-doctors',
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
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
