<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel doctors\models\search\OfficeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Operating Rooms');

?>
<div class="office-index">
    <div class="panel">
		<div class="panel panel-head">
			<div class="row">
				<div class="col-xs-3 panel-header-text">
					<span class="text-primary-2 text-bold"><?= Html::a( Yii::t('app', 'Start'), Url::home(), ['class'=> "text-primary-2 text-bold"] ) ?></span><span class="text-primary-1 text-bold"> / <?= $this->title ?></span>
				</div>
				<div class="col-xs-9 text-right">
					<a href="/operating-rooms/create" role="modal-remote" class="btn btn-xs btn-action"><span><i class="fa fa-plus"></i></span> <?= Yii::t('app', 'Add Operating Room') ?></a>
					
				</div>
			</div>	
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group no-margin">
						<div class="input-group">
							<input type="text" class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-secondary"><i class="fa fa-search"></i></button>
							</span>
						</div>
						
					</div>
				</div>
			</div>
			<br>
			
			<?= $this->render('_list', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]) ?>
		</div>
	</div>
</div>

<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
