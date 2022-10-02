<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use common\models\DoctorPayment;

return [

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
    	'label' => Yii::t('app', 'Doctor'),
        'attribute'=>'doctor.user.name',
    ],
	[
			'class'=>'\kartik\grid\DataColumn',
			'label' => Yii::t('app', 'Date'),
			'attribute'=>'paid_on',
			'format' =>  ['date', 'php:M-d-Y'],
	],
    [
        'class'=>'\kartik\grid\DataColumn',
    	'label' => Yii::t('app', 'Commission(%)'),
        'attribute'=>'doctor.membership.commission_percent',
    	'value' => function($model, $key, $index, $widget){
    		$payment = DoctorPayment::findOne($model['id']);
    		$doctor = $payment->doctor;
    		$percent = ($doctor->getMembership()->commission_percent*100).'%';
    		return $percent;
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
	    		case DoctorPayment::STATUS_PENDING_APPOINTMENT:
	    			return '<span class="text-bold text-requested">' . Yii::t('app', 'Appointment Pending') . '</span>';
	    		case DoctorPayment::STATUS_INVOICE_REQUEST:
	    			return '<span class="text-bold text-accepted">' . Yii::t('app', 'Invoice Pending') . '</span>';
	    		case DoctorPayment::STATUS_PENDING_PAYMENT:
	    			//return '<span class="text-bold text-confirmed">' . Yii::t('app', 'Pending Payment') . '</span>';
	    			return '<a href="/doctor-payments/process-pending?id='.$model['id'].'" class="btn btn-tertiary btn-grid" role="modal-remote">'.Yii::t('app', 'Pending').'</a>';
	    		case DoctorPayment::STATUS_PAID:
	    			return '<a href="/doctor-payments/view-receipt?id='.$model['id'].'" class="btn btn-tertiary btn-grid" data-pjax="0" target="_blank"><span class="text-bold text-rejected">' . Yii::t('app', 'Paid') . '</span></a>';
	    		case DoctorPayment::STATUS_CANCELLED:
	    			return '<span class="text-bold text-cancelled">' . Yii::t('app', 'Cancelled') . '</span>';
	    	}

    	},
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'amount',
    	'format' => ['decimal', 2]
    ],
    [
    		'class'=>'\kartik\grid\DataColumn',
    		'label' => Yii::t('app', 'Currency'),
    		'attribute'=>'currency.code',
    ],
    [
    		'class' => 'kartik\grid\ActionColumn',
    		'dropdown' => false,
    		'vAlign'=>'middle',
    		'template'   => '{edit} {view} {cancel}',
    		'urlCreator' => function($action, $model, $key, $index) {
    		return Url::to([$action,'id'=>$key]);
    		},
    		'buttons'    => [
    				'edit'    => function($url, $model, $key){
    				if ($model['status'] == DoctorPayment::STATUS_INVOICE_REQUEST || $model['status'] == DoctorPayment::STATUS_PENDING_PAYMENT){

    					return Html::a('<span class="text-primary-1 fa fa-pencil"></span>',
    							['doctor-payments/update', 'id' => $model['id']],
    							[
    									'role'        => 'modal-remote',
    									'title'       => Yii::t('app', 'Edit'),
    									'data-toggle' => 'tooltip',

    							]);
    				}
    				return '';
    				},
    				'view'    => function($url, $model, $key){
    				if ($model['status'] == DoctorPayment::STATUS_PENDING_PAYMENT || $model['status'] == DoctorPayment::STATUS_PAID){

    					return Html::a('<span class="text-primary-1 fa fa-file-text-o"></span>',
    							['doctor-payments/view-invoice', 'id' => $model['id']],
    							[
    									'data-pjax'   => '0',
    									'target'	  => '_blank',
    									'title'       => Yii::t('app', 'View Invoice'),
    									'data-toggle' => 'tooltip',

    							]);
    				}
    				return '';
    				},
						// Disabled for now, because there should be no need to cancel a wire transaction
						// and also it is made outside of this application, so we only store the receipt
    				// 'cancel'    => function($url, $model, $key){

	    			// 	return Html::a('<span class="text-primary-1 fa fa-ban"></span>',
	    			// 			['doctor-payments/cancel', 'id' => $model['id']],
	    			// 			[
	    			// 					'role'        => 'modal-remote',
	    			// 					'title'       => Yii::t('app', 'Cancel Payment'),
	    			// 					'data-toggle' => 'tooltip',

	    			// 			]);
    				// },
    		],
    ],

];