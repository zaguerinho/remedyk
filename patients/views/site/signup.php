<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \patients\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use patients\models\DoctorSignupForm;
use yii\widgets\MaskedInput;
use common\models\User;
use kartik\widgets\DatePicker;
if ($model->className() == DoctorSignupForm::className()){ 
	$this->title = Yii::t('app', 'Doctor Register');
}
else {
	$this->title = Yii::t('app', 'Patient Register');
}

?>
<div class="site-signup">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="panel">
				<div class="panel-head">
					<div class="row">
						<div class="col-xs-12">
							<h2 class="panel-header-text"><?= $this->title ?></h2>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
					<div class="row">
						<div class="col-xs-12 text-xs-center text-md-left">
							<?=$form->field($model, 'gender')->inline(true)->radioList(['F' => Yii::t('app', 'Female'), 'M' => Yii::t('app', 'Male')])->label(false) ?>
						</div>
						
					</div>
					<div class="row">
						<div class="col-md-6">
							<?= $form->field($model, 'first_name')->textInput(['autofocus' => true]) ?>
						</div>
						<div class="col-md-6">
							<?= $form->field($model, 'last_name') ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
						</div>
						<div class="col-md-6">
							<?= $form->field($model, 'email') ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
									'mask' => User::PHONE_FORMAT,
							]) ?>
						</div>
						<div class="col-md-6">
							<?= $form->field($model, 'birth_date')->widget(DatePicker::className(),[
									'type' => DatePicker::TYPE_COMPONENT_APPEND,
				    					'pluginOptions' => [
				    							'autoclose'=>true,
				    					]
									
							]) ?>
						</div>
					</div>
					
					<?php if ($model->className() == DoctorSignupForm::className()): ?>
						<div class="row">
							<div class="col-md-12">
								<?= $form->field($model, 'license_number'); ?>
							</div>
						</div>					
					<?php endif; ?>

					<div class="row">
						<div class="col-md-6">
							<?= $form->field($model, 'password')->passwordInput() ?>
						</div>
						<div class="col-md-6">
							<?= $form->field($model, 'repassword')->passwordInput() ?>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 text-md-right text-xs-center">
							<div class="form-group">
								<?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-secondary', 'name' => 'cancel-button', 'onclick' => 'location.href = "/site/login"']) ?>
			                    <?= Html::submitButton(Yii::t('app', 'Register'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
			                </div>
							
						</div>
					</div>
	                
					
					<?php ActiveForm::end(); ?>
				</div>
			</div>
		</div>
	</div>

</div>
