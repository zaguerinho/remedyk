<?php
use yii\bootstrap\Html;
use yii\helpers\Url;

return [
    
    [
    	
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
    	'label' => Yii::t('app', 'Address'),
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'address.text',
    ],
    [
    		'class' => 'kartik\grid\ActionColumn',
    		'dropdown' => false,
    		'vAlign'=>'middle',
    		'template' => '{view}   {update}   {delete}',
    		'urlCreator' => function($action, $model, $key, $index) {
    		return Url::to([$action,'id'=>$key]);
    		},
    		'buttons' =>
    		[
    				'view' => function($url, $model, $key) {
    				return Html::a('<span class="text-primary-1 fa fa-eye"></span>',
    						['/operating-rooms/view', 'id' => $model['id']],
    						[
    								'role'=>'modal-remote',
    								'title'=>Yii::t('app', 'View'),
    								'data-toggle'=>'tooltip',
    						]);
    				},
    				'update' => function($url, $model, $key) {
    				return Html::a('<span class="text-primary-1 fa fa-pencil"></span>',
    						['/operating-rooms/update', 'id' => $model['id']],
    						[
    								'role'=>'modal-remote',
    								'title'=>Yii::t('app', 'Update'),
    								'data-toggle'=>'tooltip',
    						]);
    				},
    				'delete' => function($url, $model, $key) {
    				return Html::a('<span class="text-primary-1 fa fa-trash"></span>',
    						['/operating-rooms/delete', 'id' => $model['id']],
    						[
    								'title'=>Yii::t('app', 'Delete'),
    								'role' => 'modal-remote',
    								'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
    								'data-request-method'=>'post',
    								'data-toggle'=>'tooltip',
    								'data-confirm-title' => Yii::t('app', 'Are you sure?'),
    								'data-confirm-message' => Yii::t('app', 'Are you sure want to delete this item')
    								 
    						]);
    				},
    		],
    ],

];   