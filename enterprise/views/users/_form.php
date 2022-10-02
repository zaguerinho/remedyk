<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
    	<div class="col-md-12">
    		<div class="form-group">
    			<label class="control-label"><?= Yii::t('app', 'Name') ?></label>
    			<div class="input-group">
    				<?= $model->name ?>
    			</div>
    		</div>
    		
    	</div>
    </div>
    <div class="row">
    	<div class="col-dmd-12">
    		<?= $form->field($model, 'status')->inline(true)->radioList([				User::STATUS_DELETED => Yii::t('app', 'Banned'), 
    																	User::STATUS_TO_CONFIRM => Yii::t('app', 'Activation Pending'), 
    																	User::STATUS_ACTIVE => Yii::t('app', 'Activated')]) ?>
    	</div>
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
</script>
