<?php
	
	use common\models\Appointment;
	use common\models\Patient;
use common\models\User;
	use yii\bootstrap\Html;
use yii\helpers\Url;
	
	$sendAppointment = \common\models\User::getUserIdentity()
		->isDoctor() ? '{send_appointment}   ' : '';
	$admin = '';
		if (User::getUserIdentity()->isStaff()){
			$admin = ' {access}';
		}
	$cols = [
		// [
		// 'class'=>'\kartik\grid\DataColumn',
		// 'attribute'=>'id',
		// ],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'user.username',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'user.first_name',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'user.last_name',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'user.email',
		],
	];
	if (User::getUserIdentity()->isDoctor()){
		$cols[] = [
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'status',
			'format'    => 'raw',
			'value'     => function($model, $key, $index, $widget){
				$patient = Patient::findOne($model['id']);
				$status  = $patient->getAppointmentStatus();
				switch($status){
					case Appointment::STATUS_REQUESTED:
						return '<span class="text-bold text-requested">' . Yii::t('app', 'Requested') . '</span>';
					case Appointment::STATUS_ACCEPTED:
						return '<span class="text-bold text-accepted">' . Yii::t('app', 'Accepted') . '</span>';
					case Appointment::STATUS_CONFIRMED_BY_DOCTOR:
						return '<span class="text-bold text-accepted">' . Yii::t('app', 'Confirmed by Doctor').'</span>';
					case Appointment::STATUS_CONFIRMED:
						return '<span class="text-bold text-confirmed">' . Yii::t('app', 'Confirmed') . '</span>';
					case Appointment::STATUS_REJECTED:
						return '<span class="text-bold text-rejected">' . Yii::t('app', 'Rejected') . '</span>';
					case Appointment::STATUS_CANCELLED:
						return '<span class="text-bold text-cancelled">' . Yii::t('app', 'Cancelled') . '</span>';
					case Appointment::STATUS_OPEN:
						return '<span class="text-bold text-open">' . Yii::t('app', 'Open') . '</span>';
					case Appointment::STATUS_CLOSED:
						return '<span class="text-bold text-closed">' . Yii::t('app', 'Done') . '</span>';
				}
				
				return '<span class="text-bold">' . $status . '</span>';
				
			},
		];
	}
		
	if (User::getUserIdentity()->isStaff()){
		$cols[] = [
				'class'     => '\kartik\grid\DataColumn',
				'attribute' => 'user.status',
				'format'    => 'raw',
				'value'     => function($model, $key, $index, $widget){
				$patient = Patient::findOne($model['id']);	
				$user = $patient->user;
				$status  = $user->status;
				switch($status){
					case User::STATUS_ACTIVE:
						return '<span class="text-bold text-confirmed">' . Yii::t('app', 'Active') . '</span>';
					case User::STATUS_TO_CONFIRM:
						return '<span class="text-bold text-cancelled">' . Yii::t('app', 'Activation Pending') . '</span>';
					case User::STATUS_DELETED:
						return '<span class="text-bold text-rejected">' . Yii::t('app', 'Banned/Inactive').'</span>';
				}
				
				return '<span class="text-bold">' . $status . '</span>';
				
				},
				];
	}
	
	
	$cols[] = [
			'class'      => 'kartik\grid\ActionColumn',
			'dropdown'   => false,
			'vAlign'     => 'middle',
			'template'   => $sendAppointment . '{view} '.$admin.' {message}',
			'urlCreator' => function($action, $model, $key, $index){
				return Url::to([$action, 'id' => $key]);
			},
			'buttons'    => [
				'send_appointment' => function($url, $model, $key){
					return Html::a('<span class="text-primary-1 fa fa-calendar"></span>',
						['patients/send-appointment', 'id' => $model['id']],
						[
							'role'        => 'modal-remote',
							'title'       => Yii::t('app', 'Send Appointment'),
							'data-toggle' => 'tooltip',
						]);
				},
				'access' => function($url, $model, $key) {
				$patient = Patient::findOne($model['id']);
				$user = $patient->user;
				return Html::a('<span class="text-primary-1 fa fa-key"></span>',
						['users/update', 'id' => $user->id],
						[
								'role' => 'modal-remote',
								'title'=>Yii::t('app', 'Enable/Disable'),
								'data-toggle'=>'tooltip',
						]);
				},
				'message'          => function($url, $model, $key){
					$patient = Patient::findOne($model['id']);
					$id = $patient->user->id;
					return Html::a('<span class="text-primary-1 fa fa-comment"></span>',
						['#'],
						[
							//'role'=>'modal-remote',
							'title'       => Yii::t('app', 'Message'),
							//'data-toggle' => 'control-sidebar',
							'onclick' => 'return gotoChat('.$id.');'
						]);
				},
				'view'             => function($url, $model, $key){
					return Html::a('<span class="text-primary-1 fa fa-eye"></span>',
						['patients/view', 'id' => $model['id']],
						[
							'title'     => Yii::t('app', 'View'),
							'data-pjax' => '0',
						
						]);
				},
			],
		];
	
	
	
	return $cols;