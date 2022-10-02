<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-enterprise',
	'language' => 'en',
	'defaultRoute' => 'moderations/index',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'enterprise\controllers',
    'bootstrap' => ['log'],
    //'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-enterprise',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-enterprise', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the enterprise
            'name' => 'advanced-enterprise',
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
