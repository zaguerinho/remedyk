<?php

	use common\assets\GlisseAsset;
	use yii\web\View;
	use yii\bootstrap\ActiveForm;
	use yii\bootstrap\Html;
	use yii\bootstrap\Modal;
	use common\models\Appointment;
	use dosamigos\tinymce\TinyMce;
	use johnitvn\ajaxcrud\CrudAsset;
	use kartik\grid\GridView;


	/* @var $this yii\web\View */
	/* @var $model common\models\Patient */
	/* @var $doctor common\models\Doctor */
	/* @var $clinicalStory common\models\ClinicalStory */
	$patientDomain = Yii::$app->params['patientsDomain'];

	GlisseAsset::register($this);
	CrudAsset::register($this);
	$js = <<<JS
	function adjustPictureSize(){
		$('.pict-thumbnail').each(function(){
			var imgWidth = $(this).width();
			$(this).css({height: imgWidth});
		});
	}

	function initGlisse(){
		$('.glisse-pict').glisse({
				changeSpeed:550,
				speed:500,
				effect:'bounce',
				fullscreen:false
		});
		adjustPictureSize();
	}
JS;
	$this->registerJs($js, View::POS_END, 'glisse-functions');
	$js1 = <<<JS

	initGlisse();
	$(window).resize(function(){
    	adjustPictureSize();
	});
JS;
	$this->registerJs($js1, View::POS_READY, 'init-glisse-gallery');

	$js2 = <<<JS
function selectAttachment(){
	$('#attach_file').click();
}

function show_details(id){

	$('#record_preview').hide();
	$.get('ajax-show-clinic-story-details', {id: id}, function(data){
		$('#story_doctor_name').html(data.doctor_name);
		$('#story_doctor_phone').html(data.doctor_phone);
		$('#story_doctor_email').html(data.doctor_email);
		$('#story_specialty').html(data.specialty);
		$('#story_registered_on').html(data.registered_on);
		$('#story_notes').html(data.notes);

		let attachments = '';

		for (let i = 0; i < data.attachments.length; i++){
			const item = data.attachments[i];
			attachments += 	'<div class="col-md-4">'+
								'<a target="_blank" href="$patientDomain'+item.url+'" class="text-primary-2 text-bold"><i class="fa fa-file-text-o"></i> '+item.name+'</a>'+
							'</div>';

		}


		$('#story_attachments').html(attachments);
		$('#record_preview').show();
	}).fail(function(error){
		console.log(error);
	});
	return false;
}

JS;
	$this->registerJs($js2, View::POS_END, 'dynamic-functions');

	$this->title = Yii::t('app', 'My Profile ');

	$genders = ['M' => Yii::t('app', 'Male'), 'F' => Yii::t('app', 'Female')];
	$doctor  = Yii::$app->user->identity->doctor;

?>
<div class="patient-view">
    <div class="panel">
        <div class="panel panel-header">
            <div class="row">
                <div class="col-xs-3">
                    <button onclick="window.location.href =  '/site/index';" class="btn btn-action"><i
                                class="fa fa-arrow-left"></i> <?= Yii::t('app', 'Back') ?></button>
                </div>
                <div class="col-xs-9 text-right">
                    <a href="/profile/update" class="btn btn-xs btn-action"><span><i
                                    class="fa fa-edit"></i></span> <?= Yii::t('app', 'Update profile') ?></a>

                </div>
            </div>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-7 separator-md-right"><!-- Patient -->
                    <div class="row">
                        <div class="col-md-2 text-xs-center text-md-left">
                            <img id="profile_picture" alt="patient-picture"
                                 class="img img-circle img-medium glisse-pict" src="<?= $model->getPicture() ?>">
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-12 text-xs-center text-md-left">
                                    <h3><?= $model->user->name ?></h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-4 text-xs-center text-md-left">
                                    <i class="fa fa-map-marker text-primary-1"></i> <?= $model->address
										? $model->address->toString() : Yii::t('app', 'Not Available') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group separator"></div>
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="no-margin"><?= Yii::t('app', 'Patient Bio') ?></h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->attributeLabels()['gender'] ?></label>
                                <div><?= $genders[$model->gender] ?></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->attributeLabels()['height'] ?></label>
                                <div><?= $model->height ?></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->attributeLabels()['weight'] ?></label>
                                <div><?= $model->weight ?></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->attributeLabels()['age'] ?></label>
                                <div><?= $model->user->age ?></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->attributeLabels()['blood_type'] ?></label>
                                <div><?= $model->blood_type ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12"><!-- Records -->
                            <div id="record_list">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h4 class="" style="padding-left: 15px;"><?= Yii::t('app', 'Records'); ?></h4>
                                    </div>
                                </div>
                                <div class="separator"></div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div id="ajaxCrudDatatable">
											<?= GridView::widget([
												'id'           => 'crud-datatable',
												'dataProvider' => $dataProvider,
												'pjax'         => true,
												'columns'      => require(__DIR__ . '/_clinical_stories_columns.php'),
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
                            <div id="record_preview" class="bottom-align" style="display: none;">
                                <div class="row secondary">
                                    <div class="col-xs-12">
                                        <h4><?= Yii::t('app', 'Record Preview') ?></h4>
                                    </div>
                                </div>
                                <div class="row secondary">
                                    <div class="col-md-3">
                                        <div class="form-group no-margin">
                                            <label class="control-label">
												<?= Yii::t('app', 'Doctor'); ?>
                                            </label>
                                            <div id="story_doctor_name"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group no-margin">
                                            <label class="control-label">
												<?= Yii::t('app', 'Phone Number'); ?>
                                            </label>
                                            <div id="story_doctor_phone"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group no-margin">
                                            <label class="control-label">
												<?= Yii::t('app', 'Email'); ?>
                                            </label>
                                            <div id="story_doctor_email"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row secondary">
                                    <div class="col-md-3">
                                        <div class="form-group no-margin">
                                            <label class="control-label">
												<?= Yii::t('app', 'Specialty'); ?>
                                            </label>
                                            <div id="story_specialty"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group no-margin">
                                            <label class="control-label">
												<?= Yii::t('app', 'Record Date'); ?>
                                            </label>
                                            <div id="story_registered_on"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row secondary">
                                    <div class="col-xs-12">
                                        <div class="form-group no-margin">
                                            <label class="control-label">
												<?= Yii::t('app', 'Notes'); ?>
                                            </label>
                                            <div>
                                                <div class="form-control primary" id="story_notes"
                                                     style="background-color: #FFFFFF; height: 150px; overflow-y: scroll; overflow-x: wrap;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row secondary" id="story_attachments">

                                </div>
                                <div class="row secondary">
                                    <div class="separator form-group"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="separator form-group"></div>

                </div>
                <div class="col-md-5">
                    <div class="col-xs-12">
                        <h4 class="no-margin"><?= Yii::t('app', 'Contact Info') ?></h4>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->user->attributeLabels()['first_name'] ?></label>
                                <div><?= $model->user->first_name ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->user->attributeLabels()['last_name'] ?></label>
                                <div><?= $model->user->last_name ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->user->attributeLabels()['birth_date'] ?></label>
                                <div><?= $model->user->birth_date ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->user->attributeLabels()['phone'] ?></label>
                                <div><?= $model->user->phone ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= $model->user->attributeLabels()['email'] ?></label>
                                <div><?= $model->user->email ?></div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group no-margin">
                                <label class="control-label"><?= Yii::t('app', 'Address') ?></label>
                                <div><?= $model->address
										? $model->address->toString()
										: Yii::t('app',
											'Not set') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php Modal::begin([
	"id"     => "ajaxCrudModal",
	"footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
