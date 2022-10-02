<?php

/* @var $this yii\web\View */

use johnitvn\ajaxcrud\CrudAsset;
use kartik\grid\GridView;
use yii\widgets\Breadcrumbs;
use yii2fullcalendar\yii2fullcalendar;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use common\models\Appointment;
use yii\web\View;
use yii\web\JsExpression;
CrudAsset::register($this);
$this->title = Yii::t('app', 'Agenda');

$js = <<<JS
$('.filter-checkbox').change(function(){
	refreshData();
});

$('#ajaxCrudModal').on('hide.bs.modal', function(event){
	refreshData();
});

function refreshData(){
	var confirmed = $('#confirmed').is(':checked');
	var requested = $('#requested').is(':checked');
	var accepted = $('#accepted').is(':checked');
	var rejected = $('#rejected').is(':checked');
	var cancelled = $('#cancelled').is(':checked');	
	if (!confirmed && !requested && !accepted && !rejected && !cancelled){
		$('#confirmed').prop('checked', true);
		confirmed = true;
	}
	var pjaxUrl = location.href.split('?')[0];
	$.pjax.reload({container:'#pjax-appointments', url: pjaxUrl, push: false, data: {confirmed, requested, accepted, rejected, cancelled}});
	$('#doctor_calendar').fullCalendar('refetchEvents');
}
JS;
$this->registerJs($js, View::POS_READY);

$check_confirmed = Yii::$app->request->get('confirmed', "true") == 'true' ? ' checked="checked"' : '';
$check_requested = Yii::$app->request->get('requested', "false") == 'true' ? ' checked="checked"' : '';
$check_accepted = Yii::$app->request->get('accepted', "false") == 'true' ? ' checked="checked"' : '';
$check_rejected = Yii::$app->request->get('rejected', "false") == 'true' ? ' checked="checked"' : '';
$check_cancelled = Yii::$app->request->get('cancelled', "false") == 'true' ? ' checked="checked"' : '';

$js2 = <<<JS
function editEvent(eventId){
	
}
JS;
$this->registerJs($js2, View::POS_END);

?>
<div class="site-index">
	<div class="panel">
		<div class="panel panel-header">
			<div class="row">
				<div class="col-xs-3 panel-header-text">
					<span class="text-primary-2 text-bold"><?= Yii::t('app', 'Start') ?></span><span class="text-primary-1 text-bold"> / <?= Yii::t('app', 'Agenda') ?></span>
				</div>
				<div class="col-xs-9 text-right">
					<a href="/patients/send-appointment" role="modal-remote" class="btn btn-xs btn-action"><span><i class="fa fa-plus"></i></span> <?= Yii::t('app', 'New Appointment') ?></a>
				</div>
			</div>		
		</div>
		<div class="panel-body">
			<div class="row"><!-- Filters -->
				<div class="col-md-1">
					<h3 style="margin-top: 9px;"><?= Yii::t('app', 'Filter') ?></h3>
				</div> 
				<div class="col-md-11">
					<div class="checkbox text-justify">
			          	<label class="confirmed">
			            	<input type="checkbox" id="confirmed" class="filter-checkbox"<?= $check_confirmed ?>> <?= Yii::t('app', 'Confirmed') ?>
			          	</label>
			          	<label class="requested">
				         	<input type="checkbox" id="requested" class="filter-checkbox"<?= $check_requested ?>> <?= Yii::t('app', 'Requested') ?>
				      	</label> 
				      	<label class="accepted">
			        		<input type="checkbox" id="accepted" class="filter-checkbox"<?= $check_accepted ?>> <?= Yii::t('app', 'Accepted') ?>
			        	</label>
			        	<label class="rejected">
			        		<input type="checkbox" id="rejected" class="filter-checkbox"<?= $check_rejected ?>> <?= Yii::t('app', 'Rejected') ?>
			       		</label>
			        	<label class="cancelled">
			        		<input type="checkbox" id="cancelled" class="filter-checkbox"<?= $check_cancelled ?>> <?= Yii::t('app', 'Cancelled') ?>
			    		</label>
        			</div>
        			
				</div>
				
			</div>
			<div class="separator no-padding"></div>
			<div class="row">
				<div class="col-md-5"><!-- Appointments list -->
					<?= GridView::widget([
						
						'dataProvider' => $dataProvider,
						//'filterModel' => $searchModel,
						'hover'=>true,
						'striped' => false,
						'pjax' => true,		
						'pjaxSettings' => [
							'options' => [
									'id' => 'pjax-appointments'
							]	
						],
						'bootstrap' => true,
						'bordered' => false,	
						'panel' => [
								'heading'=>false,
								'type'=> false,
								'before'=>false,
								'after'=>false,
								'footer'=>false
						],
						'responsive' => true,
						'responsiveWrap' => true,
						'columns'=>[
								[
									'attribute'=>'patient.user.name',
								],
								[
										'attribute'=>'date',
										'filter' => false,
										'format' =>['date', 'php:M-d-Y']
								],
								[
										'attribute'=>'status',
										'filter' => false,
										'format' => 'raw',
										'value' => function($model, $key, $index, $widget){
											switch ($model['status']){
												case Appointment::STATUS_REQUESTED: 
													return '<span class="text-bold text-requested">'.Yii::t('app', 'Requested').'</span>';
												case Appointment::STATUS_ACCEPTED: 
													return '<span class="text-bold text-accepted">'.Yii::t('app', 'Accepted').'</span>';
												case Appointment::STATUS_CONFIRMED_BY_DOCTOR:
													return '<span class="text-bold text-accepted">'.Yii::t('app', 'Confirmed by Doctor').'</span>';
												case Appointment::STATUS_CONFIRMED: 
													return '<span class="text-bold text-confirmed">'.Yii::t('app', 'Confirmed').'</span>';
												case Appointment::STATUS_REJECTED: 
													return '<span class="text-bold text-rejected">'.Yii::t('app', 'Rejected').'</span>';
												case Appointment::STATUS_CANCELLED: 
													return '<span class="text-bold text-cancelled">'.Yii::t('app', 'Cancelled').'</span>';
												case Appointment::STATUS_OPEN:
													return '<span class="text-bold text-open">'.Yii::t('app', 'Open').'</span>';
											}
											return '<span class="text-bold">'.$model['status'].'</span>';
											
										}
								],
								[
										'class' => 'kartik\grid\ActionColumn',
						
										'dropdown' => false,
										'vAlign'=>'middle',
										'template' => '{view} {message}',
										'urlCreator' => function($action, $model, $key, $index) {
										return Url::to([$action,'id'=>$key]);
										},
										'buttons' =>
										[
												
												'message' => function($url, $model, $key) {
													$appointment = Appointment::findOne($model['id']);
													$id = $appointment->patient->user->id;
													return Html::a('<span class="text-primary-1 fa fa-comment"></span>',
															['#'],
															[
																	//'role'=>'modal-remote',
																	'title'=>Yii::t('app', 'Message'),
																	//'data-toggle'=>'control-sidebar',
																	'onclick' => 'return gotoChat('.$id.');'
															]);
												},
												'view' => function($url, $model, $key) {
													return Html::a('<span class="text-primary-1 fa fa-eye"></span>',
															['appointments/view', 'id' => $model['id']],
															[
																	'role'=>'modal-remote',
																	'title'=>Yii::t('app', 'View'),
																	'data-toggle'=>'tooltip',
															]);
												},
										],
										
								],
						]
					]); 
					
					?>
				</div>
				<?php if ($appointment_id){
					$js = new JsExpression('$(\'a[href="/appointments/view?id='.$appointment_id.'"]\').trigger("click")');
					$this->registerJs($js, View::POS_READY);
				} ?>
				
				<div class="col-md-7 separator-md-left"><!-- Calendar -->
					<?= $this->render('@common/partials/_doctor_calendar', ['id' => 'doctor_calendar',
							'ajaxParams' => ['confirmed' => "$('#confirmed').is(':checked')", 
									'requested' => "$('#requested').is(':checked')", 
									'accepted' => "$('#accepted').is(':checked')", 
									'rejected' => "$('#rejected').is(':checked')", 
									'cancelled' => "$('#cancelled').is(':checked')",
							],
							'customOptions' => [
									'dayClick' => new JsExpression('function(date, jsEvent, view){ if ($.inArray("secondary", jsEvent.target.classList) == -1 ) {addDayOff(date);} }'),
									'eventClick' => new JsExpression("function(calEvent, jsEvent, view){
																			if ($.inArray('secondary', calEvent.className) == -1){
																				editEvent(calEvent.id);
																			}
																		}"),
								]
					]) ?>
					
				</div>
			</div>
			
		</div>
		 
	</div>
</div>


<?= $this->render('/modals/_add_day_off'); ?>



<?php Modal::begin([
    "id"=>"ajaxCrudModal",
	//'size' => 'large',
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
