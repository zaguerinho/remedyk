<?php
use \yii\bootstrap\ActiveForm;
use doctors\controllers\DoctorsController;
use yii\bootstrap\Html;

?>

<?php $form = ActiveForm::begin() ?>

<?= Html::hiddenInput('activePage', DoctorsController::OFFICES) ?>

<div class="row">	
	<div class="col-xs-12 text-right">
		<a href="/offices/create" role="modal-remote" class="btn btn-xs btn-action"><span><i class="fa fa-plus"></i></span> <?= Yii::t('app', 'Add Office') ?></a>					
	</div>
</div>	

<?= $this->render('/offices/_list', [ 'searchModel' => $officesSearchModel, 'dataProvider' => $officesDataProvider]) ?>

	
<?php ActiveForm::end()?>