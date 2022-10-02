<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Comment;

/* @var $this yii\web\View */
/* @var $model common\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <div class="row">
    	<div class="col-md-6">
    		<label class="text-primary-1 control-label"><?= Yii::t('app', 'From')?></label>
    		<div><?= $model->from->name ?></div>
    	</div>
    	<div class="col-md-6">
    		<label class="text-primary-1 control-label"><?= Yii::t('app', 'To')?></label>
    		<div><?= $model->target->name ?></div>
    	</div>
    </div>
    <div class="row">
    	<div class="col-md-6">
    		<label class="text-primary-1 control-label"><?= Yii::t('app', 'Date and Time')?></label>
    		<div><?= date('M-d-Y', strtotime($model->datetime)).' '.Yii::t('app', 'at').' '.date('h:i:s A', strtotime($model->datetime))?></div>
    	</div>
    	<div class="col-md-6">
    		<label class="text-primary-1 control-label"><?= Yii::t('app', 'Go to Comment')?></label>
    		<div><a href="/doctors/profile?id=<?= $model->target->doctor->id ?>" target="_blank"><?= Yii::t('app', 'Doctor Profile') ?></a></div>
    	</div>
    </div>
	<div class="row">
		<div class="col-xs-12">
			<label class="text-primary-1 control-label"><?= Yii::t('app', 'Comment')?></label>
			<div><?= $model->text ?></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<?= $form->field($model, 'status')->inline(true)->radioList([Comment::STATUs_APPROVED => Yii::t('app', 'Approved'), Comment::STATUS_BANNED => Yii::t('app', 'Banned')])  ?>
		</div>
		<div class="col-md-6">
			<?= $form->field($model, 'ban_reason')->textInput(['maxlength' => true, 'id' => 'ban_reason']) ?>
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
$("input[type='radio'][name='Comment[status]']").on('change', function(e){	
	$('#ban_reason').attr('disabled', !($(this).val() == 'B'));
	
});
<?php
if ($model->status != Comment::STATUS_BANNED){
	echo "$('#ban_reason').attr('disabled', true);\n";
}
?>
$.material.init();
</script>
