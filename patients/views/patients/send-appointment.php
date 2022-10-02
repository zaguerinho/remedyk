<?php
use kartik\widgets\DatePicker;
use kartik\widgets\TimePicker;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use common\models\User;

/* @var $this yii\web\View */
/* @var  $appointment common\models\Appointment */
/* @var  $doctor common\models\Doctor */
$patient = User::getUserIdentity()->patient;


$availableTimes = $doctor->getFirstAvailable($appointment->office_id);
$appointmentTimes = [];
$duration = $doctor->appointment_duration;
foreach ($availableTimes as $time){
	$date = date('m/d/Y', strtotime($time));
	$startTime = date('h:i A', strtotime($time));
	$hours = date('H', strtotime($duration));
	$minutes = date('i', strtotime($duration));
	$endTime = date('h:i A', strtotime($startTime." +{$hours} hours +{$minutes} minutes"));
	$appointmentTimes[$time] = $date.' ('.$startTime.' - '.$endTime.')';
}
$appointment->date_time_start = (count($availableTimes)>0 && !$appointment->start_time) ? $availableTimes[0] : "";
if ($appointment->start_time){
	$appointment->start_time = date('h:i A', strtotime($appointment->start_time));
}
?>


<?php $form = ActiveForm::begin(); ?>
<?php 
	if (!$appointment->isNewRecord) 
		echo $form->field($appointment, 'id')->hiddenInput()->label(false);
?>

<div class="row">
	<div class="col-md-12">
		<?= $form->field($appointment, 'is_in_wait_list', ['options' => ['class' => 'form-group no-margin'], 'labelOptions' => ['style' => 'width: 100%']])->checkbox(); ?>
	</div>
</div>

<?php if ($appointment->office) { $location_text = $appointment->office->title; $location_url = $appointment->office->address->url_gmaps;}?> 
<?php if ($appointment->operatingRoom) { $location_text = $appointment->operatingRoom->name; $location_url = $appointment->operatingRoom->address->url_gmaps; }?>
<?php if (isset($location_text) && isset($location_url)): ?>
<div class="row">
	<div class="col-xs-12">
    	<label class="control-label text-primary-1 text-bold"><?= Yii::t('app', 'Location') ?></label>
    	<div><?= Html::a($location_text, $location_url, ['class' => 'remedyk-link', 'target' => '_blank']) ?></div> 
   	</div>
</div>
<?php endif; ?>

<div class="row">
	<div class="col-md-6 col-xs-12">
		<div class="form-group no-margin">
			<label class="control-label text-primary-1"><?= Yii::t('app', 'Doctor') ?></label>
			<div><?= $doctor->user->name; ?></div>
		</div>
		
	</div>
	
	<div class="col-md-6 col-xs-12">
		<div class="form-group no-margin">
			<label class="control-label text-primary-1"><?= Yii::t('app', 'Regular Price') ?></label>
			<div><?= $appointment->price.' '.$appointment->currency->code; ?></div>
			<p class="help-block" style="display: block;">* <?= Yii::t('app', 'Doctor can update the price before accept your request.') ?></p>
		</div>	
	</div>	
</div>

<div class="row">
	<div class="col-md-8">
		<?php if (count($appointmentTimes) == 0): ?>
			<div class="form-group text-danger"><?= Yii::t('app', 'This doctor has the agenda full. You cannot set an appointment.') ?></div>
		<?php endif; ?>
		<?= $form->field($appointment, 'date_time_start', ['options' => ['class' => 'form-group no-margin']])->radioList($appointmentTimes+['' => Yii::t('app', 'Custom')])->label(Yii::t('app', 'Hours')) ?>
		
		<div class="col-xs-6">
		<?= $form->field($appointment, 'date', ['options' => ['class' => 'form-group no-margin']])->widget(DatePicker::className(),[
				'options' => ['id' => 'appointment_date'],
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
				'removeButton' => false,
				'pluginOptions' => [
						'autoclose'=>true,
						'format' => 'mm/d/yyyy'
				]
				
		]); ?>
		</div>		
		<div class="col-xs-6">
			<?= $form->field($appointment, 'start_time', ['options' => ['class' => 'form-group no-margin']])->widget(TimePicker::className(), [
					'options' => [
							'placeholder' => Yii::t('app', 'Start Time'),
							'id' => 'appointment_custom_start_time',
					],
					'pluginOptions' => [
							'defaultTime' => false
					],
			]); ?>
		</div>		
	</div>
	
</div>


<script>
	$("input[type='radio'][name='Appointment[date_time_start]']").on('change', function(e){	
		$('#appointment_custom_start_time, #appointment_date').attr('disabled', !($(this).val() == ''));
		
	});
	
	$('#ajaxCrudModal').on('show.bs.modal', function(event){
		$("input[type='radio'][name='Appointment[date_time_start]']").trigger('change');
	});
	
	$.material.init();
	 
	<?php if (count($appointmentTimes) == 0): ?>
	$('#pay_submit').prop('disabled', true);
	<?php endif; ?>
</script>

<?php ActiveForm::end(); ?>