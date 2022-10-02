<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\AddressFiller;

/* @var $this yii\web\View */
/* @var $model common\models\Office */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="office-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	
	<?= AddressFiller::widget(['options' => ['form' => $form, 'address' => $address, 'id' => 'operating_room']]) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
<script>
	if (typeof(google) != 'undefined') 
		initializeElement('operating_room');
</script>