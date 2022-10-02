<?php
	
	use common\models\User;
use yii\helpers\Url;
	
	return [
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'username',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'first_name',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'last_name',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'email',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'phone',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'birth_date',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'profileType',
				'format'    => 'raw',
			'value'     => function($model, $key, $index, $widget){
				$user = User::findOne($model['id']);
				switch($user->profileType()){
					case User::DOCTOR:
						return '<span class="text-bold text-requested">' . Yii::t('app', 'Doctor') . '</span>';
					case User::PATIENT:
						return '<span class="text-bold text-confirmed">' . Yii::t('app', 'Patient') . '</span>';
					case User::STAFF:
						return '<span class="text-bold text-cancelled">' . Yii::t('app', 'Staff') . '</span>';
						
				}
			}
		]
		/*
		[
			'class'         => 'kartik\grid\ActionColumn',
			'dropdown'      => false,
			'vAlign'        => 'middle',
			'urlCreator'    => function($action, $model, $key, $index){
				return Url::to([$action, 'id' => $key]);
			},
			'viewOptions'   => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
			'updateOptions' => ['role' => 'modal-remote', 'title' => 'Update', 'data-toggle' => 'tooltip'],
			'deleteOptions' => [
				'role'                 => 'modal-remote',
				'title'                => 'Delete',
				'data-confirm'         => false,
				'data-method'          => false,// for overide yii data api
				'data-request-method'  => 'post',
				'data-toggle'          => 'tooltip',
				'data-confirm-title'   => 'Are you sure?',
				'data-confirm-message' => 'Are you sure want to delete this item',
			],
		],
		*/
	
	];