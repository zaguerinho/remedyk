<?php
	
	use yii\web\View;
	use common\models\Commission;
	
	$js   = <<<JS
		function selectFile(id){
			$('#invoice_'+id).click();
		}
		function uploadFile(input){
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					
					// The file comes in e.target.reult base64encoded
					// The name comes in e.target.fileName as string
					const fileContent = e.target.result;
					const fileName = e.target.fileName;
					$.post('/commissions/ajax-upload-invoice', {fileName: fileName, fileContent: fileContent}, function(data){
					
					}).fail(function(error){
					
					});
	
			};
				reader.fileName = input.files[0].name;
				reader.readAsDataURL(input.files[0]);
			}
		}
JS;
	$user = \common\models\User::getUserIdentity();
	
	if($user->isDoctor())
		$this->registerJs($js, View::POS_END);
	
	$cols   = [];
	$cols[] = [
		'class'     => '\kartik\grid\DataColumn',
		'attribute' => 'id',
	];
	
	if($user->isDoctor())
		$cols[] = [
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'appointment.patient.user.name',
			'label'     => Yii::t('app', 'Patient'),
		];
	elseif($user->isPatient())
		$cols[] = [
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'appointment.doctor.user.name',
			'label'     => Yii::t('app', 'Doctor'),
		];
	
	$cols[] = [
		'class'     => '\kartik\grid\DataColumn',
		'attribute' => 'paid_on',
		'label'     => Yii::t('app', 'Payment Date'),
		'format'    => ['date', 'php:M-d-Y'],
	];
	$cols[] = [
		'class'     => '\kartik\grid\DataColumn',
		'attribute' => 'appointment.date',
		'label'     => Yii::t('app', 'Appointment Date'),
		'format'    => ['date', 'php:M-d-Y'],
	];
	$cols[] = [
		'class'     => '\kartik\grid\DataColumn',
		'attribute' => 'percent',
	];
	$cols[] = [
		'class'     => '\kartik\grid\DataColumn',
		'attribute' => 'amount',
	];
	$cols[] = [
		'class'     => '\kartik\grid\DataColumn',
		'attribute' => 'status',
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
	];
	$cols[] = [
		'class'     => '\kartik\grid\DataColumn',
		'attribute' => 'doctor_payment_id',
		'label'     => Yii::t('app', 'Payment Folio'),
	
	];
	
	return $cols;