<?php
use common\models\Notification;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;


return [
	
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'text',
    		'format'    => 'raw',
    		'value'     => function($model, $key, $index, $widget){
    			$notification = Notification::findOne($model['id']);
    			return '<i class="'.$notification->fa_icon_class.'"></i> ' . $notification->localized_text;
    		}
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'datetime',
    	'label' => Yii::t('app', 'Received At'),
    	'format'    => ['date', 'php:M-d-Y'],
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'seen_at',
    	'format'    => ['date', 'php:M-d-Y'],
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'visited_at',
    	'format'    => ['date', 'php:M-d-Y'],
    ],
    
    
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
    	'template' => '{visit} {delete}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'    => [
        		
        		'view'             => function($url, $model, $key){
        		return Html::a('<span class="text-primary-1 fa fa-eye"></span>',
        				['notifications/view', 'id' => $model['id']],
        				[
        						'title'     => Yii::t('app', 'View Notification details'),
        						'role' => 'modal-remote',
        						'data-toggle' => 'tooltip'
        						
        				]);
        		},
        		'visit' => function($url, $model, $key){
        			return Html::a('<span class="text-primary-1 fa fa-link"></span>',
        				[$model['target_url']],
        				[
        						'title' => Yii::t('app', 'View'),
        						'data-pjax' => '0',
        						'onclick' => new JsExpression('visitLink('.$model['id'].'); return false;')
        				]);
        		}
        ],
        
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete', 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=> Yii::t('app', 'Are you sure?'),
                          'data-confirm-message'=>Yii::t('app', 'Are you sure want to delete this notification?')], 
    ],

];   