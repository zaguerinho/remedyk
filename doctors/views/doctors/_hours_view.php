<?php



use common\models\Currency;
use doctors\controllers\DoctorsController;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use kartik\time\TimePicker;
use doctors\models\DoctorHoursForm;


$user = Yii::$app->user->identity;
$doctor = $user->doctor;
/* @var \common\models\Doctor $doctor */
$currencies = ArrayHelper::map(Currency::find()->all(), 'id', 'code');
?>

<?php $form = ActiveForm::begin() ?>
<div class="separator"></div>
<?= Html::hiddenInput('activePage', DoctorsController::HOURS) ?>
<?php foreach ($doctor->offices as $office): ?>
	<div class="row">
		<div class="col-xs-12">
			<h3><?= $office->title ?></h3>
		</div>
	</div>
	<?php foreach (DoctorHoursForm::WEEK_DAYS as $index => $label): ?>
	<div class="row secondary">
		<div class="col-md-1">
			<div class="form-group no-margin">
				<label class="control-label">&nbsp;</label>
				<div class="text-primary-1 text-bold"><?= Yii::t('app', $label) ?></div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="row">
				<div class="col-xs-5"><?= $form->field($model, "first_from[{$office->id}][{$index}]", ['options' => ['class' => 'form-group no-margin']])->widget(TimePicker::className(), [
						'options' => [						
							'id' => 'first_from_'.$office->id.'_'.$index,
						],
						'pluginOptions' => [
							'defaultTime' => false
						],
						])?></div>
				<div class="col-xs-2 text-center">
					<div class="form-group no-margin">
						<label class="control-label">&nbsp;</label>
						<div class="input-group"><label class="text-primary-1 control-label"><?= Yii::t('app', 'To') ?></label></div>
					</div>
				</div>
				<div class="col-xs-5"><?= $form->field($model, "first_to[{$office->id}][{$index}]", ['options' => ['class' => 'form-group no-margin']])->widget(TimePicker::className(), [
						'options' => [						
							'id' => 'first_to_'.$office->id.'_'.$index,
							'style' => 'margin-top: 12px;'
						],
						'pluginOptions' => [
							'defaultTime' => false
						],
						])?></div>
			</div>
			
		</div>
		<div class="col-md-3">
			<div class="row">
				<div class="col-xs-5"><?= $form->field($model, "second_from[{$office->id}][{$index}]", ['options' => ['class' => 'form-group no-margin']])->widget(TimePicker::className(), [
						'options' => [						
							'id' => 'second_from_'.$office->id.'_'.$index,
						],
						'pluginOptions' => [
							'defaultTime' => false
						],
						])?></div>
				<div class="col-xs-2 text-center">
					<div class="form-group no-margin">
						<label class="control-label">&nbsp;</label>
						<div class="input-group"><label class="text-primary-1 control-label"><?= Yii::t('app', 'To') ?></label></div>
					</div>
				</div>
				<div class="col-xs-5"><?= $form->field($model, "second_to[{$office->id}][{$index}]", ['options' => ['class' => 'form-group no-margin']])->widget(TimePicker::className(), [
						'options' => [						
							'id' => 'second_to_'.$office->id.'_'.$index,
							'style' => 'margin-top: 12px;'
						],
						'pluginOptions' => [
							'defaultTime' => false
						],
						]) ?>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="row">
				<div class="col-xs-5"><?= $form->field($model, "third_from[{$office->id}][{$index}]", ['options' => ['class' => 'form-group no-margin']])->widget(TimePicker::className(), [
						'options' => [						
							'id' => 'third_from_'.$office->id.'_'.$index,
						],
						'pluginOptions' => [
							'defaultTime' => false
						],
						])?></div>
				<div class="col-xs-2 text-center">
					<div class="form-group no-margin">
						<label class="control-label">&nbsp;</label>
						<div class="input-group"><label class="text-primary-1 control-label"><?= Yii::t('app', 'To') ?></label></div>
					</div>
				</div>
				<div class="col-xs-5"><?= $form->field($model, "third_to[{$office->id}][{$index}]", ['options' => ['class' => 'form-group no-margin']])->widget(TimePicker::className(), [
						'options' => [						
							'id' => 'third_to_'.$office->id.'_'.$index,
							'style' => 'margin-top: 12px;'
						],
						'pluginOptions' => [
							'defaultTime' => false
						],
						])?></div>
			</div>
		
		</div>
		<div class="col-md-2">
			<?= $form->field($model, "closed[{$office->id}][{$index}]", ['options' => ['style' => 'margin-top: 42px;']])->checkbox(['id' => 'closed_'.$office->id.'_'.$index]) ?>
		</div>
	</div>
	<!--  -->
	<?php endforeach; ?>
	<div class="separator"></div>
<?php endforeach; ?>

<div class="row">
	<div class="col-xs-12">
		<div class="text-primary-1 text-bold"><?= Yii::t('app', 'Agenda\'s Configurations') ?></div>
	</div>
</div>
<div class="row">
	<div class="col-md-4 col-xs-12">
		<?= $form->field($doctor, 'appointment_duration')->widget(TimePicker::className(), [
					'options' => [
							'placeholder' => Yii::t('app', 'Start Time'),
							'id' => 'appointment_custom_start_time',
					],
					'pluginOptions' => [
							'defaultTime' => false,
							'showSeconds' => true,
							'showMeridian' => false,
							'minuteStep' => 1,
							'secondStep' => 5,
					],
			])?>
	</div>
	<div class="col-md-4 col-xs-12">
		<?= $form->field($doctor, 'appointment_anticipation')->widget(MaskedInput::className(), [
				'clientOptions' => [
						'alias' => 'integer',
						//'digits' => 0,
						'digitsOptional' => false,
						//'radixPoint' => '.',
						'groupSeparator' => ',',
						'autoGroup' => true,
						'removeMaskOnSubmit' => true
				]
			
		])?>
	</div>
	<div class="col-md-2 col-xs-8">
		<?= $form->field($doctor, 'appointment_price')->widget(MaskedInput::className(), [
				'clientOptions' => [
						'alias' => 'decimal',
						'digits' => 2,
						'digitsOptional' => false,
						'radixPoint' => '.',
						'groupSeparator' => ',',
						'autoGroup' => true,
						'removeMaskOnSubmit' => true
				]
			
		]) ?>
	</div>
	<div class="col-md-2 col-xs-4">
		<?= $form->field($doctor, 'currency_id')->dropDownList($currencies) ?>
	</div>
</div>


<div class="row">
	<div class="col-sm-12 text-right">
		<a href="" class="btn btn-secondary"><?= Yii::t('app', 'Cancel') ?></a>
		<button type="submit" class="btn btn-primary"><?= Yii::t('app', 'Save Changes') ?></button>
	</div>
</div>
<?php ActiveForm::end()?>