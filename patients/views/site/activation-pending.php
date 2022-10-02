<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = Yii::t('app', 'Activate your account');
$user = Yii::$app->user->identity;
?>

<div class="panel">
	<div class="panel-head">
		<div class="row">
			<div class="col-xs-12">
				<h2 class="panel-header-text"><?= Yii::t('app', 'Welcome to Remedyk!') ?></h2>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-12">
				<p><?= Yii::t('app', 'You are almost there, ') . $user->first_name ?> </p>
				<p><?= Yii::t('app', 'We have sent you a confirmation link to your registered email. Just click it and start searching the best doctors!')  ?></p>	
			</div>
		</div>
		<?php $form = ActiveForm::begin(); ?>
		<div class="row">
			<div class="col-xs-12 text-xs-center text-md-right">
				<div class="form-group">
					<?= Html::submitButton(Yii::t('app', 'Resend confirmation email'), ['class' => 'btn btn-tertiary']) ?>
				</div>
			</div>
		</div>			
		<?php ActiveForm::end(); ?>
	</div>
</div>