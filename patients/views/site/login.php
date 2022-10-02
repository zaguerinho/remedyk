<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;
use yii\base\Widget;

$this->title = Yii::t('app','Log In');

?>
<div class="site-login">
	<div class="row">
		<div class="col-md-offset-4 col-md-4">
			<div class="panel text-center">
				<div class="panel-head">
					<div class="row">
						<div class="col-xs-12">
							<h2><?= $this->title ?></h2>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12" style="margin-left: -40px;"><?= AuthChoice::widget(['baseAuthUrl' => ['site/auth']]) ?></div>
					</div>
					<div class="row">
				        <div class="col-xs-12">
				            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
								
				                <?= $form->field($model, 'username', ['options' => ['class' => 'form-group text-left']])->textInput(['autofocus' => true]) ?>
				
				                <?= $form->field($model, 'password', ['options' => ['class' => 'form-group text-left']])->passwordInput() ?>
				
				                <!--<?//= $form->field($model, 'rememberMe')->checkbox() ?>-->
								<div class="form-group">
				                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
				                </div>
				                
				                <div style="margin:1em 0">
				                    <?= Html::a(Yii::t('app','Forgot password?'), ['site/request-password-reset'], ['class' => 'remedyk-link']) ?>
				                </div>
				
				                
				
				            <?php ActiveForm::end(); ?>
				        </div>
				    </div>
				    <div class="separator no-padding"></div>
				   	<div class="row">
				   		<div class="col-xs-12">
				   			<h3><?= Yii::t('app', 'Don\'t have an account?') ?></h3>
				   		</div>
				   	</div>
				   	<div class="row">
				   		<?php if ($doctor_id && $office_id): ?>
				   			<?= Html::button(Yii::t('app', 'Register as patient'), ['class' => 'btn btn-tertiary', 'onclick' => 'location.href = "/site/signup/patient/'.$doctor_id.'/'.$office_id.'"']) ?>
				   		<?php else: ?>
				   			<?= Html::button(Yii::t('app', 'Register as patient'), ['class' => 'btn btn-tertiary', 'onclick' => 'location.href = "/site/signup"']) ?>
				   		<?php endif; ?>
				   		<?= Html::button(Yii::t('app', 'Register as doctor'), ['class' => 'btn btn-tertiary', 'onclick' => 'location.href = "/site/signup/doctor"']) ?>
				   	</div> 
				</div>
			    
		    </div>	
		</div>
	</div>
	
</div>
