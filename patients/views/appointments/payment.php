<?php

	
?>

<p class="text-primary-3">
	<?= Yii::t('app', 'The appointment price is a total of ').number_format($appointment->price, 2).' '.$appointment->currency->code.'. ' ?>
	<?= Yii::t('app', 'You need to pay ').$partial_price.' '.$appointment->currency->code.' '.Yii::t('app', 'which represents the ').(Yii::$app->params['initialPercent']*100).Yii::t('app', '% of the total amount in order to ensure the appointment. ') ?>
	<?= Yii::t('app', 'The remaining money will be paid to the doctor the day of the appointment.') ?>
</p>

	
<?= $this->render('@common/partials/_conekta', ['submitText' => Yii::t('app', 'Pay ').$partial_price.' '.$appointment->currency->code]) ?>
