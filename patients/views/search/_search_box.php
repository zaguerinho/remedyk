<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

?>


<fieldset style="margin: 0 20px;">
<?php 
	$searchButton = Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-block']);
	$form = ActiveForm::begin([
        'id' => 'search-form',
        'layout' => 'horizontal',
		'action' => Url::to(['/search/index']),
		'method' => 'GET',
		'fieldConfig' => [
				'template' => "{label}\n<div class=\"input-group\">\n{input}\n<span class=\"input-group-btn\">\n</span></div>\n{error}",
				'labelOptions' => ['class' => 'control-label'],
				'options' => ['class' => 'form-group'],
		],
    ]); ?>
    <div class="row">
		<div class="col-md-6">
			<?= $form->field($search, 'searchString')->textInput(['autofocus' => true, 'class' => 'form-control']) ?>
		</div>
		
		<div class="col-md-4">
			<?= $form->field($search, 'searchAddress')->textInput(['class' => 'form-control']) ?>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label class="control-label"></label>
				
					<?= $searchButton; ?> 
				
			</div>
		</div>
    </div>
<?php ActiveForm::end(); ?>
</fieldset>
