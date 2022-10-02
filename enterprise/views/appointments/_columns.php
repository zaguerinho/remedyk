<?php
use common\models\Appointment;
use yii\helpers\Url;

return [
		[
				'class'     => '\kartik\grid\DataColumn',
				'label' 	=> Yii::t('app', 'Appointment Number'),
				'attribute' => 'id',
		],
		[
				'class'     => '\kartik\grid\DataColumn',
				'label' 	=> Yii::t('app', 'Patient First Name'),
				'attribute' => 'patient.user.first_name',
		],
		[
				'class'     => '\kartik\grid\DataColumn',
				'label' 	=> Yii::t('app', 'Patient Last Name'),
				'attribute' => 'patient.user.last_name',
		],
		[
				'class'     => '\kartik\grid\DataColumn',
				'label' 	=> Yii::t('app', 'Doctor First Name'),
				'attribute' => 'doctor.user.first_name',
		],
		[
				'class'     => '\kartik\grid\DataColumn',
				'label' 	=> Yii::t('app', 'Doctor Last Name'),
				'attribute' => 'doctor.user.last_name',
		],
		[
				'class'     => '\kartik\grid\DataColumn',
				'attribute' => 'date',
				'format'    => ['date', 'php:M-d-Y'],
		],
		[
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
						return '<span class="text-bold text-cancelled">' . Yii::t('app', 'Cancelled by ') . $canceller . ' (' . date('M-d-Y', strtotime($appointment->cancel_datetime)) . ')</span>';
					case Appointment::STATUS_OPEN:
						return '<span class="text-bold text-open">' . Yii::t('app', 'Open') . '</span>';
					case Appointment::STATUS_CLOSED:
						return '<span class="text-bold text-closed">' . Yii::t('app', 'Done') . '</span>';
				}
				
				return '<span class="text-bold">' . $status . '</span>';
				
				},
		]
		

];   