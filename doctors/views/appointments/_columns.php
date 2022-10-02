<?php
	
	use common\models\Appointment;
	use yii\bootstrap\Html;
	use yii\helpers\Url;
use common\models\User;
	
	$isPatient = Yii::$app->getUser()->identity->isPatient();
	$isDoctor  = Yii::$app->getUser()->identity->isDoctor();
	$cols      = [];
	
	if($isDoctor)
		$cols[] = [
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'patient.user.first_name',
		];
	
	if($isDoctor)
		$cols[] = [
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'patient.user.last_name',
		];
	
	if($isDoctor)
		$cols[] = [
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'patient.user.email',
		];
	
	
	if($isPatient)
		$cols[] = [
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'doctor.user.first_name',
		];
	
	if($isPatient)
		$cols[] = [
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'doctor.user.last_name',
		];
	
	if($isPatient)
		$cols[] = [
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'doctor.user.email',
		];
	
	
	$cols[] = [
		'class'     => '\kartik\grid\DataColumn',
		'attribute' => 'date',
		'format'    => ['date', 'php:M-d-Y'],
	];
	$cols[] = [
		'class'     => '\kartik\grid\DataColumn',
		'attribute' => 'status',
		'format'    => 'raw',
		'value'     => function($model, $key, $index, $widget){
			$appointment = Appointment::findOne($model['id']);
			$status      = $appointment->status;
			switch($status){
				case Appointment::STATUS_REQUESTED:
					return '<span class="text-bold text-requested">' . Yii::t('app', 'Requested') . '</span>';
				case Appointment::STATUS_ACCEPTED:
					return '<span class="text-bold text-accepted">' . Yii::t('app', 'Accepted') . '</span>';
				case Appointment::STATUS_CONFIRMED_BY_DOCTOR:
					return '<span class="text-bold text-accepted">' . Yii::t('app', 'Confirmed by Doctor') . '</span>';
				case Appointment::STATUS_CONFIRMED:
					return '<span class="text-bold text-confirmed">' . Yii::t('app', 'Confirmed') . '</span>';
				case Appointment::STATUS_REJECTED:
					return '<span class="text-bold text-rejected">' . Yii::t('app', 'Rejected') . '</span>';
				case Appointment::STATUS_CANCELLED:
					$canceller = $appointment->changedBy->isDoctor() ? Yii::t('app', 'Doctor') : Yii::t('app', 'Patient');
					return '<span class="text-bold text-cancelled">' . Yii::t('app', 'Cancelled by ') . $canceller . '</span>';
				case Appointment::STATUS_OPEN:
					return '<span class="text-bold text-open">' . Yii::t('app', 'Open') . '</span>';
				case Appointment::STATUS_CLOSED:
					return '<span class="text-bold text-closed">' . Yii::t('app', 'Done') . '</span>';
			}
			
			return '<span class="text-bold">' . $status . '</span>';
			
		},
	];
	$cols[] = [
		'class'      => 'kartik\grid\ActionColumn',
		'dropdown'   => false,
		'vAlign'     => 'middle',
		'template'   => '{view}   {message}',
		'urlCreator' => function($action, $model, $key, $index){
			return Url::to([$action, 'id' => $key]);
		},
		'buttons'    => [
			
			'message' => function($url, $model, $key){
				$appointment = Appointment::findOne($model['id']);
				$id = $appointment->doctor->user->id;			
				if (User::getUserIdentity()->isDoctor()){
					$id = $appointment->patient->user->id;
				}
				return Html::a('<span class="text-primary-1 fa fa-comment"></span>',
					['#'],
					[
						//'role'=>'modal-remote',
						'title'       => Yii::t('app', 'Message'),
						//'data-toggle' => 'control-sidebar',
						'onclick' => 'return gotoChat('.$id.');'
					]);
			},
			'view'    => function($url, $model, $key){
				return Html::a('<span class="text-primary-1 fa fa-eye"></span>',
					['appointments/view', 'id' => $model['id']],
					[
						'role'        => 'modal-remote',
						'title'       => Yii::t('app', 'View'),
						'data-toggle' => 'tooltip',
					
					]);
			},
		],
	];
	
	
	return $cols;