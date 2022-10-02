<?php
	
	use yii\helpers\Url;
	
	return [
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'name',
			'value'     => function($model){
				/* @var $model \common\models\Specialty */
				return $model->localized_name;
			},
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'is_active',
			'value'     => function($model){
				/* @var $model \common\models\Specialty */
				return $model->is_active ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
			},
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'specialist_name',
			'value'     => function($model){
				/* @var $model \common\models\Specialty */
				return $model->localized_specialist_name;
			},
		],
		[
			'class'         => 'kartik\grid\ActionColumn',
			'dropdown'      => false,
			'vAlign'        => 'middle',
			'urlCreator'    => function($action, $model, $key, $index){
				return Url::to([$action, 'id' => $key]);
			},
			'viewOptions'   => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
			'updateOptions' => ['role' => 'modal-remote', 'title' => 'Update', 'data-toggle' => 'tooltip'],
			'template'      => ' {view} {update}',
			
			/*
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
			*/
		],
	
	];