<?php
	
	/* @var $this yii\web\View */
	
	use yii\helpers\Html;
	use kartik\form\ActiveForm;
	
	/* @var $searchModel doctors\models\search\AppointmentSearch */
	
	$form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]);
	
	$value  = '';
	$classN = array_pop(explode('\\', $searchModel::className()));
	if(!empty($_REQUEST[$classN]) && !empty($_REQUEST[$classN]['r_filter']))
		$value = trim($_REQUEST[$classN]['r_filter']);
	
	
	$searchModel->r_filter = $value;
?>


    <div class="row">
        <div class="col-md-4">
            <div class="form-group no-margin">
                <div class="input-group">
					<?= $form->field($searchModel,
						'r_filter',
						[
							'addon' => [
								'append' => [
									'content'  => Html::submitButton('<i class="fa fa-search"></i>',
										['class' => 'btn btn-secondary']),
									'asButton' => true,
								],
							],
						])
						->label(false) ?>
                </div>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>