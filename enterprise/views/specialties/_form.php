<?php
	
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	
	/* @var $this yii\web\View */
	/* @var $model common\models\Specialty */
	/* @var $form yii\widgets\ActiveForm */
	
	$form = ActiveForm::begin();
	
	$model->spanishName           = $model->getSpanishName();
	$model->englishName           = $model->getEnglishName();
	$model->spanishSpecialistName = $model->getSpanishSpecialistName();
	$model->englishSpecialistName = $model->getEnglishSpecialistName();

?>
<div class="specialty-form">
    <div class="row">
        <div class="col-xs-12">
            <h4 class="no-margin"><?= Yii::t('app', 'Update Record') ?></h4>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group no-margin">
                    <label class="control-label"><?= $model->attributeLabels()['spanishName'] ?></label>
                    <div><?= $form->field($model, 'spanishName')
							->textInput()
							->label(false) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group no-margin">
                    <label class="control-label"><?= $model->attributeLabels()['englishName'] ?></label>
                    <div><?= $form->field($model, 'englishName')
							->textInput()
							->label(false) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group no-margin">
                    <label class="control-label"><?= $model->attributeLabels()['spanishSpecialistName'] ?></label>
                    <div><?= $form->field($model, 'spanishSpecialistName')
							->textInput()
							->label(false) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group no-margin">
                    <label class="control-label"><?= $model->attributeLabels()['englishSpecialistName'] ?></label>
                    <div><?= $form->field($model, 'englishSpecialistName')
							->textInput()
							->label(false) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group no-margin">
                <div><?= $form->field($model, 'is_active')
						->checkbox()
						->label(false) ?></div>
            </div>
        </div>
    </div>
</div>

<?php if(!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
			['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php } ?>

<?php ActiveForm::end(); ?>
