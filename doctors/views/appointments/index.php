<?php
	
	use yii\helpers\Url;
	use yii\helpers\Html;
	use yii\bootstrap\Modal;
	use kartik\grid\GridView;
	use johnitvn\ajaxcrud\CrudAsset;
use yii\web\JsExpression;
use yii\web\View;
	
	/* @var $this yii\web\View */
	/* @var $searchModel doctors\models\search\AppointmentSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	
	$this->title = Yii::t('app', 'Appointments');
	
	CrudAsset::register($this);

?>
<div class="appointment-index">
    <div class="panel">
        <div class="panel panel-head">
            <div class="row">
                <div class="col-xs-3 panel-header-text">
                    <span class="text-primary-2 text-bold"><?= Html::a(Yii::t('app', 'Start'),
							Url::home(),
							['class' => "text-primary-2 text-bold"]) ?></span><span
                            class="text-primary-1 text-bold"> / <?= $this->title ?></span>
                </div>
				<?php if(!Yii::$app->getUser()->identity->isPatient()){ ?>
                    <div class="col-xs-9 text-right">
                        <a href="/patients/send-appointment" role="modal-remote" class="btn btn-xs btn-action"><span><i
                                        class="fa fa-plus"></i></span> <?= Yii::t('app', 'New Appointment') ?></a>

                    </div>
				<?php } ?>
            </div>
        </div>
        <div class="panel-body">
			<?= $this->render('@common/partials/_grid_search', ['searchModel' => $searchModel]) ?>
            <br>
            <div class="row">
                <div class="col-xs-12">

                    <div id="ajaxCrudDatatable">
						<?= GridView::widget([
							'id'           => 'crud-datatable',
							'dataProvider' => $dataProvider,
							//'filterModel' => $searchModel,
							'pjax'         => true,
							'pjaxSettings' => [
								'options' => [
									'id' => 'pjax-appointments'
								]
							],
							'columns'      => require(__DIR__ . '/_columns.php'),
							'toolbar'      => [
								[
									'content' => false,
								],
							],
							'bordered'     => false,
							'striped'      => false,
							'hover'        => true,
							'condensed'    => false,
							'responsive'   => true,
							'panel'        => [
								'type'    => false,
								'heading' => false,
								'before'  => false,
								'after'   => false,
								'footer'  => false,
							],
						]) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
	<?php if ($appointment_id){
		$js = new JsExpression('$(\'a[href="/appointments/view?id='.$appointment_id.'"]\').trigger("click")');
		$this->registerJs($js, View::POS_READY);
	} ?>
</div>
<?php Modal::begin([
	"id"     => "ajaxCrudModal",
	"footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
