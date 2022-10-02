<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'r9jG8bGgs-h41rD4CgCC8CoSStqpUu_d',
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

return $config;
