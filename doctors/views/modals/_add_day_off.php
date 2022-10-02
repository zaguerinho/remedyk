<?php

use yii\bootstrap\Modal;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use doctors\models\DayOff;
use kartik\widgets\TimePicker;
/* @var $this \yii\web\View */

$js = <<<JS

function addDayOff(date){
	$('#day_off_date').val(date.format('M/D/YYYY'));
	$('#day_off_from').val('08:00 AM');
	$('#day_off_to').val('06:00 PM');
	$('#add_day_off').modal('show');
}

function confirmDayOff(){
	var submitData = $('#form_add_day_off').serialize();
	console.log(submitData);
	$.post('/doctors/ajax-add-day-off', submitData, function(data){
		$('#doctor_calendar').fullCalendar('refetchEvents');
	}).fail(function(error){
		
	}).always(function(data){
		$('#add_day_off').modal('hide');
	});
}

JS;
$model = new DayOff();
$header = '<div class="modal-title">'.Yii::t('app', 'Add Day Off').'</div>';
$footer = '<div class="row">
	<div class="col-xs-12">
		<button class="btn btn-secondary" data-dismiss="modal">'.Yii::t('app', 'Cancel').'</button>
		<button class="btn btn-primary" onclick="confirmDayOff();">'.Yii::t('app', 'Add').'</button>
	</div>
</div>';

$this->registerJs($js, View::POS_END);

Modal::begin([
		'id' => 'add_day_off',
		'size' => Modal::SIZE_DEFAULT,
		'header' => $header,
		'footer' => $footer,
]);
?>

<?php $form = ActiveForm::begin([
	'id' => 'form_add_day_off'
]); ?>

<div class="row">
	<div class="col-md-6">
		<?= $form->field($model, 'date', ['options' => ['class' => 'form-group no-margin']])->widget(DatePicker::className(),[
				'options' => ['id' => 'day_off_date'],
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
				'removeButton' => false,
				'pluginOptions' => [
						'autoclose'=>true,
						'format' => 'mm/d/yyyy'
				]
				
		]); ?>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="form-group no-margin">
			<label for="day_off_from" class="control-label"><?= Yii::t('app', 'Hours') ?></label>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-3 col-xs-6">
		<?= $form->field($model, 'from', ['options' => ['class' => 'form-group no-margin']])->widget(TimePicker::className(), [
				'options' => [
						'placeholder' => $model->getAttributeLabel('from'),
						'id' => 'day_off_from',
				],
				'pluginOptions' => [
						'defaultTime' => false
				],
		])->label(false); ?>
	</div>
	<div class="col-md-3 col-xs-6">
		<?= $form->field($model, 'to', ['options' => ['class' => 'form-group no-margin']])->widget(TimePicker::className(), [
				'options' => [
						'placeholder' => $model->getAttributeLabel('to'),
						'id' => 'day_off_to',
					],
				'pluginOptions' => [
						'defaultTime' => false
				],
				
			])->label(false); ?>
	</div>
</div>

<?php ActiveForm::end(); ?>
<?php 
Modal::end();

