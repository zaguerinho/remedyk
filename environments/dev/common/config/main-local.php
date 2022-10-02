<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=localhost;dbname=remedyk',
            'username' => 'postgres',
            'password' => 'Remedyk2017',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        	'transport' => [
        		'class' => 'Swift_SmtpTransport',
        		'host' => 'smtp.gmail.com',
        		'username' => 'remedyk.test@gmail.com',
        		'password' => 'jsyqyocgbaxmslqb',
        		'port'          => '587',
        		'encryption'    => 'tls',
        		'streamOptions'	=> [
        			'ssl' => [
        				'verify_peer' => false,
        				'verify_peer_name' => false,
        				'allow_self_signed' => true
        			],
        		],
        	],
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
        ],
    ],
	'modules' => [
		'gridview' => [ 'class' => '\kartik\grid\Module' ]
	],
];

