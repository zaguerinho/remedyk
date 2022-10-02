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

function changePicture(input){
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			// Put images in their places
			$('img.fast-change-picture').attr('src', e.target.result);
			$('img.fast-change-picture.glisse-pict').attr('data-glisse-big', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
	}
}

function selectPicture(){
	$('#change_picture').trigger('click');
}


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
	$user = $model->user;
	
	$form    = ActiveForm::begin([]);
	$address = $model->address ?: new \common\models\Address();

?>
    <div class="patient-view">
        <div class="panel">
            <div class="panel panel-header">
                <div class="row">
                    <div class="col-xs-3">
                        <button onclick="window.location.href =  '/profile/index';" class="btn btn-action"><i
                                    class="fa fa-arrow-left"></i> <?= Yii::t('app', 'Back') ?></button>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-7 separator-md-right"><!-- Patient -->
                        <div class="row">
                            <div class="col-md-2 text-xs-center text-md-left">
                                <img id="profile_picture" alt="patient-picture"
                                     class="img img-circle img-medium glisse-pict fast-change-picture" src="<?= $model->getPicture() ?>">
                            </div>
                            <div class="col-lg-4 text-xs-center text-lg-left" style="margin-top: 30px;">
								<?= Html::button(Yii::t('app', 'Upload Photo'), ['class' => 'btn btn-tertiary', 'onclick' => 'selectPicture()']); ?>
								<?= $form->field($user, 'picture_file')->fileInput(['id' => 'change_picture', 'onchange' => 'changePicture(this)', 'style' => 'display: none;', 'accept' => 'image/*'])->label(false) ?>
							</div>
                            <div class="col-md-6">
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
								<?= $form->field($model,
									'height')
									->textInput() ?>
                            </div>
                            <div class="col-md-2">
								<?= $form->field($model,
									'weight')
									->textInput() ?>
                            </div>
                            <div class="col-md-2">
								<?= $form->field($model,
									'blood_type')
									->textInput() ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save Changes') ?></button>
                            </div>
                        </div>
                        <div class="separator form-group"></div>
                    </div>
                    <div class="col-md-5">
                        <div class="col-xs-12">
                            <h4 class="no-margin"><?= Yii::t('app', 'Contact Info') ?></h4>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
								<?= $form->field($model->user,
									'first_name')
									->textInput() ?>
                            </div>
                            <div class="col-md-4">
								<?= $form->field($model->user,
									'last_name')
									->textInput() ?>
                            </div>
                            <div class="col-md-4">
								<?= $form->field($model->user,
									'birth_date')
									->textInput() ?>
                            </div>
                            <div class="col-md-4">
								<?= $form->field($model->user,
									'phone')
									->textInput() ?>
                            </div>
                            <div class="col-md-4">
								<?= $form->field($model->user,
									'email')
									->textInput() ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group no-margin">
                                    <label class="control-label"><?= Yii::t('app', 'Address') ?></label>
                                    <div>
										<?= \common\widgets\AddressFiller::widget([
											'options' => [
												'form'    => $form,
												'address' => $address,
												'id'      => 'profile',
											],
										]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

<?php
	ActiveForm::end();