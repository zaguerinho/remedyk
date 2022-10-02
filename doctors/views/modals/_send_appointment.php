<?php
use kartik\widgets\DatePicker;
use kartik\widgets\TimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use common\models\Appointment;
use common\models\Currency;
use common\assets\GlisseAsset;

/* @var $this yii\web\View */
/* @var  $appointment common\models\Appointment */
/* @var  $doctor common\models\Doctor */

GlisseAsset::register($this);

$doctor = Yii::$app->user->identity->doctor;
$myPatients = ArrayHelper::map($doctor->patients, 'id', function($elem){ return $elem->user->first_name . ' ' . $elem->user->last_name; });
$readOnly = $appointment->patient ? true : false;

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

$offices = ArrayHelper::map($doctor->offices, function($element){ return Appointment::LOCATION_TYPE_OFFICE.'-'.$element->id;}, 'title');
$operating_rooms = ArrayHelper::map($doctor->operatingRooms, function($element){ return Appointment::LOCATION_TYPE_OPERATING_ROOM.'-'.$element->id;}, 'name');;

$locationsList = ['' => Yii::t('app', '- Not defined -'), Yii::t('app', 'Offices') => $offices, Yii::t('app', 'Operating Rooms') => $operating_rooms];

$procedure2doctors = $doctor->procedure2doctors;

$doctorProcedures = ['' => Yii::t('app', '- None Selected -')] + ArrayHelper::map($procedure2doctors, 'id', function($element){ return $element->procedure->localized_name; });
$doctorProceduresOptions = ['' => ['data-price' => $doctor->appointment_price, 'data-currency-id' => $doctor->currency_id]] + ArrayHelper::map($procedure2doctors, 'id', function($element){ return ['data-price' => $element->price, 'data-currency-id' => $element->currency_id]; });

$currencies = ArrayHelper::map(Currency::find()->all(), 'id', 'code');

?>

<?php $form = ActiveForm::begin(); ?>
<?php 
	if (!$appointment->isNewRecord) 
		echo $form->field($appointment, 'id')->hiddenInput()->label(false);
?>
<div class="row">
	<div class="col-md-6 col-xs-12">
		<?= $form->field($appointment, 'patient_id', ['options' => ['class' => 'form-group no-margin']])->dropDownList($myPatients, ['disabled' => $readOnly]) ?>
	</div>
	
	<div class="col-md-4 col-xs-8">
		<?= $form->field($appointment, 'price', ['options' => ['class' => 'form-group no-margin']])->widget(MaskedInput::className(), [
				'clientOptions' => [
						'alias' => 'decimal',
						'digits' => 2,
						'digitsOptional' => false,
						'radixPoint' => '.',
						'groupSeparator' => ',',
						'autoGroup' => true,
						'removeMaskOnSubmit' => true
				]
			
		]); ?>	
	</div>
	<div class="col-md-2 col-xs-4">
		<?= $form->field($appointment, 'currency_id', ['options' => ['class' => 'form-group no-margin']])->dropDownList($currencies) ?>
	</div>
</div>

<div class="row">
	<div class="col-md-8" id="appointment_times">
		<?php if (count($appointmentTimes) == 0): ?>
			<div class="form-group text-danger"><?= Yii::t('app', 'You have agenda full. You cannot set an appointment here.') ?></div>
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
	
	<div class="col-md-4">
		<?= $form->field($appointment, 'procedure2doctor_id')->dropDownList($doctorProcedures, ['options' => $doctorProceduresOptions, 'onchange' => 'changePrice(this)']) ?>
		<?= $form->field($appointment, 'location_id')->dropDownList($locationsList, ['onchange' => 'changeLocation(this)']) ?>
	</div>
	
</div>


<script>
	$("input[type='radio'][name='Appointment[date_time_start]']").on('change', function(e){	
		$('#appointment_custom_start_time, #appointment_date').attr('disabled', !($(this).val() == ''));
		
	});
	
	$('#ajaxCrudModal').on('show.bs.modal', function(event){
		$("input[type='radio'][name='Appointment[date_time_start]']").trigger('change');
	});

	function changePrice(sender){
		$('[name="Appointment[price]"]').val($(sender).find('option:selected').attr('data-price'));
		$('[name="Appointment[currency_id]"]').val($(sender).find('option:selected').attr('data-currency-id'));
	}
	function changeLocation(sender){
		let location_id = $(sender).find('option:selected').val();
		$('#appointment-date_time_start').html('<div style="width: 100% text-align: center;"><div class="lds-eclipse"><div></div></div></div>');
		$.get('/patients/ajax-recalculate-times', {location_id: location_id}, function(data){
			
			let content = '';
			$.each(data, function(value, text){
				content += '<div class="radio radio-primary">'
		          +'<label>'
		            +'<input name="Appointment[date_time_start]" value="'+value+'" type="radio">'
		            +text
		          +'</label>'
		        +'</div>';
			});
			if (content == ''){
				content += '<div class="form-group text-danger"><?= Yii::t('app', 'You have agenda full. You cannot set an appointment here.') ?></div>';
			}
			content += '<div class="radio radio-primary">'
		          +'<label>'
		            +'<input name="Appointment[date_time_start]" value="" type="radio">'
		            +'<?= Yii::t('app', 'Custom') ?>'
		          +'</label>'
		        +'</div>';
			$('#appointment-date_time_start').html(content);
			$.material.init();
		});
	}
	
	$.material.init();
	
</script>

<?php ActiveForm::end(); ?>