<?php
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\helpers\ArrayHelper;
use common\models\Doctor;
use common\models\Currency;

/* @var $this yii\web\View */
/* @var $model common\models\DoctorPayment */
/* @var $form yii\widgets\ActiveForm */

$doctorsList = ['' => Yii::t('app', '- Select Doctor -')]+ArrayHelper::map(Doctor::find()->all(), 'id', function($element){ return $element->user->name; });
$currenciesList = ArrayHelper::map(Currency::find()->all(), 'id', 'code');

?>

<div class="doctor-payment-form">

    <?php $form = ActiveForm::begin(['id' => 'doctor-payment-form']); ?>
    
    <div class="row">
    	
    	<div class="col-md-6 col-xs-12">
    		<?= $form->field($model, 'doctor_id')->dropDownList($doctorsList, ['onchange' => 'refreshCommissions()', 'id' => 'doctor_id']); ?>
    	</div>
    	<div class="col-md-4 col-xs-8">
    		<?= $form->field($model, 'amount')->widget(MaskedInput::className(), [
    			'options' => ['id' => 'amount', 'class' => 'form-control'],
				'clientOptions' => [
						'alias' => 'decimal',
						'digits' => 2,
						'digitsOptional' => false,
						'radixPoint' => '.',
						'groupSeparator' => ',',
						'autoGroup' => true,
						'removeMaskOnSubmit' => true
				]
			
		]);?>
    	</div>
    	<div class="col-md-2 col-xs-4">
    		<?= $form->field($model, 'currency_id')->dropDownList($currenciesList, ['onchange' => 'refreshCommissions()', 'id' => 'currency_id']); ?>
    	</div>
    </div>
    <div class="row">
    	<div class="col-xs-12">
    		<?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>
    	</div>
    </div>
    
    <div class="row">
    	<div class="col-xs-6"><h4><?= Yii::t('app', 'Collections (Patient Payments)') ?></h4></div>
    	</div>
	<div class="separator"></div>
    
    <div id="commissions_container" class="container-items row">
    	
    </div>
  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

<script>
	$.material.init();
	refreshCommissions();
	function refreshCommissions(){
		var doctor_id = $('#doctor_id').val();
		var currency_id = $('#currency_id').val();
		$('#commissions_container').html('');
		$.get('/doctor-payments/ajax-load-commissions?doctor_id='+doctor_id+'&currency_id='+currency_id<?php if (!$model->isNewRecord){ ?> + '&doctor_payment_id=<?= $model->id ?>'<?php } ?>, {}, function(data){
			var html = '';
			$.each(data, function(index, value){
				var item = 	'<div class="well col-md-6">'+
					    		'<div class="row">'+
					    			'<div class="col-xs-8">'+
					    				'<h4><?= Yii::t('app', 'Collection') ?> #'+value.id+'</h4>'+
					    			'</div>'+
					    			'<div class="col-xs-4 text-right">'+
					    				'<div class="checkbox">'+
								          	'<label>'+
								            	'<input type="checkbox" class="update-total-amount" data-id="'+value.id+'" onchange="updateTotalAmount(this)" id="commission-'+value.id+'" name="commission['+value.id+']"';
							            		if (value.selected){
								            		item += ' checked="checked"';
							            		}
								          		item += '>'+
								          	'</label>'+
								         '</div>'+
					    			'</div>'+
					    			'<input type="hidden" id="commission-pay-to-doctor-'+value.id+'" name="commission-pay-to-doctor['+value.id+']" value="'+value.pay_to_doctor+'" />'+
					    		'</div>'+
					    		'<div class="separator" style="margin-bottom: 0;"></div>'+
					    		'<div class="row">'+
					    			'<div class="col-xs-6">'+
					    				'<label class="text-primary-1 control-label"><?= Yii::t('app', 'Patient') ?></label>'+
					    				'<div>'+value.patient+'</div>'+
					    			'</div>'+	
					    			'<div class="col-xs-6">'+
				    				'<label class="text-primary-1 control-label"><?= Yii::t('app', 'Appointment') ?></label>'+
				    				'<div><?= Yii::t('app', 'Appointment') ?> #'+value.appointment_id+'</div>'+
				    			'</div>'+		
					    		'</div>'+
					    		'<div class="row">'+
					    			'<div class="col-xs-6">'+
					    				'<label class="text-primary-1 control-label"><?= Yii::t('app', 'Appointment Date') ?></label>'+
					    				'<div>'+value.appointment_date+'</div>'+
					    			'</div>'+
					    			'<div class="col-xs-6">'+
					    				'<label class="text-primary-1 control-label"><?= Yii::t('app', 'Payment Date') ?></label>'+
					    				'<div>'+value.payment_date+'</div>'+
					    			'</div>'+
					    		'</div>'+
					    		'<div class="row">'+
					    			'<div class="col-xs-6">'+
					    				'<label class="text-primary-1 control-label"><?= Yii::t('app', 'Appointment Price') ?></label>'+
					    				'<div>'+value.appointment_price+'</div>'+
					    			'</div>'+
					    		'</div>'+
					    		'<div class="row">'+
					    			'<div class="col-xs-12">'+
					    				'<label class="text-primary-1 control-label"><?= Yii::t('app', 'Paid') ?></label>'+
					    				'<div>'+value.paid_amount+' ('+value.paid_percent+')</div>'+
					    			'</div>'+
					    		'</div>'+	
					    		'<div class="row">'+
					    			'<div class="col-xs-12">'+
					    				'<label class="text-primary-1 control-label"><?= Yii::t('app', 'Remedyk Commission') ?></label>'+
					    				'<div>'+value.commission_amount+' ('+value.commission_percent+')</div>'+
					    			'</div>'+
					    		'</div>'+
					    		'<div class="separator" style="margin-top: 10px;"></div>'+
					    		'<div class="row">'+
					    			'<div class="col-xs-12">'+
					    				'<h4><?= Yii::t('app', 'Amount to Doctor: ') ?>'+value.pay_to_doctor_text+'</h4>'+
					    			'</div>'+
					    		'</div>'+    		
					    	'</div>';
		    	html += item;
			}); 
			$('#commissions_container').html(html);			
			$.material.init();
			resetTotalAmount();
		}).fail(function(error){
			$('#commissions_container').html('<p class="text-danger"><?= Yii::t('app', 'There was an error loading the pending payments, please try again.') ?></p>');
		});
	}

	function resetTotalAmount(){
		$('#amount').val('0.00');
		$('.update-total-amount').each(function(index){
			if ($(this).is(':checked')){
				updateTotalAmount(this);
			}
		});
	}
	
	function updateTotalAmount(sender){
		var id = $(sender).attr('data-id');
		var amount_to_pay = parseFloat($('#commission-pay-to-doctor-'+id).val());
		var current_total = parseFloat($('#amount').val().replace(/,/g,''));
		var new_total = $(sender).is(':checked')? current_total + amount_to_pay : current_total - amount_to_pay;
		$('#amount').val(new_total.toFixed(2));
	}
</script>
