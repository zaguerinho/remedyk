<?php
use \yii\bootstrap\ActiveForm;
use doctors\controllers\DoctorsController;
use yii\bootstrap\Html;

?>

<?php $form = ActiveForm::begin() ?>

<?= Html::hiddenInput('activePage', DoctorsController::OPERATING_ROOMS) ?>

<div class="row">	
	<div class="col-xs-12 text-right">
		<a href="/operating-rooms/create" role="modal-remote" class="btn btn-xs btn-action"><span><i class="fa fa-plus"></i></span> <?= Yii::t('app', 'Add Operating Room') ?></a>					
	</div>
</div>	

<?= $this->render('/operating-rooms/_list', [ 'searchModel' => $operatingRoomsSearchModel, 'dataProvider' => $operatingRoomsDataProvider]) ?>

	
<?php ActiveForm::end()?>