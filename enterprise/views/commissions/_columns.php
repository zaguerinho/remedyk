<?php
use common\models\Commission;
use yii\bootstrap\Html;
use yii\helpers\Url;

return [
    [
        'class'=>'\kartik\grid\DataColumn',
    	'label' => Yii::t('app', 'Patient'),
        'attribute'=>'appointment.patient.user.name',
    ],
	[
			'class'=>'\kartik\grid\DataColumn',
			'label' => Yii::t('app', 'Doctor'),
			'attribute'=>'appointment.doctor.user.name',
	],
	[
			'class'=>'\kartik\grid\DataColumn',
			'label' => Yii::t('app', 'Date'),
			'attribute'=>'paid_on',
			'format' =>  ['date', 'php:M-d-Y'],
	],
	[
			'class'=>'\kartik\grid\DataColumn',
			'label' => Yii::t('app', 'Procedure'),
			'attribute'=>'appointment.procedure2doctor.procedure.localized_name',
	],
	[
			'class'=>'\kartik\grid\DataColumn',
			'label' => Yii::t('app', 'Payment Method'),
			'attribute'=>'paymentMethod.localized_name',
	],
	[
			'label' => Yii::t('app', 'Total Price'),
			'class'=>'\kartik\grid\DataColumn',
			'attribute'=>'appointment.price',
			'format' => ['decimal', 2]
	],
    [
    	'label' => Yii::t('app', 'Paid Amount'),
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'amount',
    	'format' => ['decimal', 2]
    ],
	[
			'label' => Yii::t('app', 'Currency'),
			'class'=>'\kartik\grid\DataColumn',
			'attribute'=>'appointment.currency.code',
	],
    [
    	'label' => Yii::t('app', 'Paid(%)'),
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'percent',
    	'value' => function($model, $key, $index, $widget){
    		return ($model['percent']*100).'%';
    	}
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
    	'format'    => 'raw',
    	'label'     => Yii::t('app', 'Status'),
    	'value'     => function($model, $key, $index, $widget){
    		$status = $model['status'];
    		switch($status){
    			case Commission::STATUS_PENDING_APPOINTMENT:
    				return '<span class="text-bold text-requested">' . Yii::t('app', 'Appointment Pending') . '</span>';
    			case Commission::STATUS_INVOICE_REQUEST:
    				return '<span class="text-bold text-accepted">' . Yii::t('app', 'Invoice Pending') . '</span>';
    			case Commission::STATUS_PENDING_PAYMENT:
    				return '<span class="text-bold text-confirmed">' . Yii::t('app', 'Pending Payment') . '</span>';
    			case Commission::STATUS_PAID:
    				return '<span class="text-bold text-rejected">' . Yii::t('app', 'Paid') . '</span>';
    			case Commission::STATUS_CANCELLED:
    				return '<span class="text-bold text-cancelled">' . Yii::t('app', 'Cancelled') . '</span>';
    		}
    		
    	},
    ],
    
    /*[
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
    	'template'   => '{view}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'    => [
        		'view'    => function($url, $model, $key){
        		return Html::a('<span class="text-primary-1 fa fa-file-text-o"></span>',
        				['commissions/view-invoice', 'id' => $model['id']],
        				[
        						'role'        => 'modal-remote',
        						'title'       => Yii::t('app', 'View CFDI'),
        						'data-toggle' => 'tooltip',
        						
        				]);
        		},
        		],
    ],*/

];   