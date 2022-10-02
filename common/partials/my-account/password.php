<?php
	/**
	 * SYCET by TJ ALTA TECNOLOGIA Y APLICACIONES, S. DE R.L. DE C.V. is licensed under a
	 * Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.
	 * Based on a work at http://sycet.net and its subdomains.
	 *
	 * @link      http://gotribit.com/
	 * @copyright Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License
	 *
	 */
	
	
	/* @var $this yii\web\View */
	/* @var $model \common\models\PasswordForgot */
	/* @var $user \common\models\User */
	
	/* @var $form yii\bootstrap\ActiveForm */
	
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	
	$this->title = Yii::t('app', 'Update password');
	
	$form = ActiveForm::begin(['id' => 'change-password-form'])

?>

    <div class="panel panel-default">
        <div class="panel-heading"><h4><?= Yii::t('app', 'Set new password') ?></h4></div>
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
					<?= $form->field($model, 'newPass')
						->widget(\kartik\password\PasswordInput::classname(),
							[
								'pluginOptions' => [
									'showMeter'  => true,
									'toggleMask' => false,
								],
							])
					; ?>
					
					
					<?= Html::submitButton(Yii::t('app', Yii::t('app', 'Continue')),
						[
							'class' => 'btn btn-primary btn-block btn-flat',
							'name'  => 'login-button',
						]) ?>
                </div>
                <div class="col-sm-4"></div>

            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>