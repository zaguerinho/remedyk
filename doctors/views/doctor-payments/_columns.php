<?php
use yii\bootstrap\Html;
use yii\web\View;
use common\models\DoctorPayment;

$js = <<<JS
	function selectFile(id){
		$('#invoice_'+id).click();
	}
	function uploadFile(input){
		var id = $(input).attr('data-id');
		if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			// The file comes in e.target.reult base64encoded
			// The name comes in e.target.fileName as string
			const fileContent = e.target.result;
			const fileName = e.target.fileName;
			$.post('/doctor-payments/ajax-upload-invoice', {fileName: fileName, fileContent: fileContent, doctorPaymentId: id}, function(data){
				$.pjax.reload({container:'#crud-datatable-pjax', url: location.href, push: false})
			}).fail(function(error){
			
			});
			
        };
		reader.fileName = input.files[0].name;
        reader.readAsDataURL(input.files[0]);
	}
	}
JS;

$this->registerJs($js, View::POS_END);

return [
		[
				'class'=>'\kartik\grid\DataColumn',
				'attribute'=>'id',
		],
		[
				'class'=>'\kartik\grid\DataColumn',
				'attribute'=>'user.name',
				'label' => Yii::t('app', 'Paid by'),
		],
		[
				'class'=>'\kartik\grid\DataColumn',
				'attribute'=>'paid_on',
				'label' => Yii::t('app', 'Paid On'),
				'format' =>['date', 'php:M-d-Y']
		],
		[
				'class'=>'\kartik\grid\DataColumn',
				'attribute'=>'amount',
		],
		[
				'class'=>'\kartik\grid\DataColumn',
				'attribute'=>'notes',
		],
		[
				'class'=>'\kartik\grid\DataColumn',
				'attribute'=>'invoice_name',
				'format' => 'raw',
				'label' => Yii::t('app', 'Invoice XML'),
				'value' => function($model, $key, $index, $widget){
					$status = $model['status'];
					
					switch ($status){
						case DoctorPayment::STATUS_INVOICE_REQUEST:
							$result = '	<input class="form-control" type="file" id="invoice_'.$model['id'].'" data-id="'.$model['id'].'" onchange="uploadFile(this)" accept=".xml" style="display:none;">
								<button type="button" onclick="selectFile('.$model['id'].')" class="btn btn-tertiary btn-xs" style="margin: -5px">'.Yii::t('app', 'Attach File').'</button>';
							break;
						case DoctorPayment::STATUS_PENDING_PAYMENT:
							$result = '	<input class="form-control" type="file" id="invoice_'.$model['id'].'" data-id="'.$model['id'].'" onchange="uploadFile(this)" accept=".xml" style="display:none;">
								<button type="button" onclick="selectFile('.$model['id'].')" class="btn btn-tertiary btn-xs" style="margin: -5px">'.Yii::t('app', 'Change File').'</button>';
							break;
						case DoctorPayment::STATUS_PAID:
							$result = ' <a href="/doctor-payments/view-invoice?id='.$model['id'].'" data-pjax="0" target="_blank" class="text-primary-2 text-bold"><i class="fa fa-xml"></i> '.$model['invoice_name'].'</a>';
							break;
						case DoctorPayment::STATUS_PENDING_APPOINTMENT:
							$result = ' <span class="text-bold text-requested">'.Yii::t('app', 'Appointment Pending').'</span>';
							break;
						case DoctorPayment::STATUS_CANCELLED:
							$result = ' <span class="text-bold text-cancelled">'.Yii::t('app', 'Payment Cancelled').'</span>';
							break;
					}
					
					
					return $result;
				}
		],
		[
				'class'=>'\kartik\grid\DataColumn',
				'attribute'=>'receipt',
				'format' => 'raw',
				'label' => '',
				'value' => function($model, $key, $index, $widget){
					if ($model['status'] == DoctorPayment::STATUS_PAID){
						return Html::a('<span class="text-primary-1 fa fa-file-text-o"></span>',
								['doctor-payments/view-receipt', 'id' => $model['id']],
								[
										'data-pjax'   => '0',
										'target'	  => '_blank',
										'title'       => Yii::t('app', 'View Receipt'),
										'data-toggle' => 'tooltip',
										
								]);
					}
				}
		],

];   