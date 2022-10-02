<?php
	
	use common\assets\GlisseAsset;
	use yii\web\View;
	use yii\bootstrap\ActiveForm;
	use yii\bootstrap\Html;
	use yii\bootstrap\Modal;
	use common\models\Appointment;
	use common\models\Procedure2doctor;
	use common\models\Specialty;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Json;
	use dosamigos\tinymce\TinyMce;
	use johnitvn\ajaxcrud\CrudAsset;
	use kartik\grid\GridView;
use common\models\ClinicalStoryType;
	
	
	/* @var $this yii\web\View */
	/* @var $model common\models\Patient */
	/* @var $doctor common\models\Doctor */
	/* @var $clinicalStory common\models\ClinicalStory */
	$patientDomain = Yii::$app->params['patientsDomain'];
	$allClinicalStoriyTypes = ArrayHelper::map(ClinicalStoryType::find()->all(), 'id', 'localized_name');
	$allClinicalStoriyTypesJson = Json::encode($allClinicalStoriyTypes);
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

	$('.filter-checkbox').change(function(){
		refreshData();
	});
	
	$('#ajaxCrudModal').on('hide.bs.modal', function(event){
		refreshData();
	});
	
	function refreshData(){
		const allTypes = {$allClinicalStoriyTypesJson};
		let checks = {};
		for (var id in allTypes){
			checks['story_type_'+id] = $('#story_type_'+id).is(':checked');
		}
		checks['id'] = {$model->id};
		var pjaxUrl = location.href.split('?')[0];
		$.pjax.reload({container:'#pjax-clinical-story', url: pjaxUrl, push: false, data: checks});
	
	}
JS;
	$this->registerJs($js1, View::POS_READY, 'init-glisse-gallery');
	
	$js2 = <<<JS
function selectAttachment(){
	$('#attach_file').click();
}
function add_attachment(input){
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			
			// The file comes in e.target.reult base64encoded
			const fileContent = e.target.result;
			const item = '<div class="col-md-4">'+
							'<div class="form-group no-margin">'+
								'<div class="input-group">'+
									'<input type="hidden" name="attachment[]" value="'+fileContent+'">'+
									'<input type="text" name="attachment_name[]" class="form-control" value="'+e.target.fileName+'" readonly>'+
									'<span class="input-group-btn"><button type="button" class="btn btn-secondary" onclick="remove_attachment(this)"><i class="fa fa-close"></i></button></span>'+
								'</div>'+
							'</div>'+	
						 '</div>';

			
			$('#attachments_container').append(item);

        };
		reader.fileName = input.files[0].name;
        reader.readAsDataURL(input.files[0]);
	}
}
function remove_attachment(sender){
	$(sender).parent().parent().parent().parent().remove();
}

function show_details(id){

	$('#record_preview').hide();
	$.get('ajax-show-clinic-story-details', {id: id}, function(data){
		$('#story_doctor_name').html(data.doctor_name);
		$('#story_doctor_phone').html(data.doctor_phone);
		$('#story_doctor_email').html(data.doctor_email);
		$('#story_type').html(data.clinical_story_type);
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
	
	$this->title = Yii::t('app', 'Patient ') . $model->user->name;
	
	$prevModule = Yii::t('app', 'Patients');
	$prevUrl    = '/patients/index';
	
	
	$genders            = ['M' => Yii::t('app', 'Male'), 'F' => Yii::t('app', 'Female')];
	$doctor             = Yii::$app->user->identity->doctor;
	$actionButton       = '<a href="/patients/send-appointment?id='
						  . $model->id
						  . '" role="modal-remote" class="btn btn-xs btn-action"><span><i class="fa fa-plus"></i></span> '
						  . Yii::t('app', 'New Appointment')
						  . '</a>';
	$appointment_status = $model->getAppointmentStatus();
	switch($appointment_status){
		case Appointment::STATUS_OPEN:
			$appointment      = $model->getOpenAppointment();
			$procedure2doctor = $appointment->procedure2doctor ? $appointment->procedure2doctor
				: new Procedure2doctor();
			$specialtyArray   = ['' => Yii::t('app', 'None Selected')] + ArrayHelper::map($doctor->specialties,
					'id',
					function($element){
						return Json::decode($element->name)[Yii::$app->language];
					});
			$procedureArray   = ['' => Yii::t('app', 'None Selected')] + ArrayHelper::map($doctor->procedures,
					'id',
					function($element){
						return Json::decode($element->name)[Yii::$app->language];
					});
			$actionButton     = '<a href="/patients/view?id='
								. $model->id
								. '&action='
								. Appointment::STATUS_CLOSED
								. '"  class="btn btn-xs btn-action"><span><i class="fa fa-plus"></i></span> '
								. Yii::t('app', 'Close Appointment')
								. '</a>';
			break;
		case Appointment::STATUS_CONFIRMED:
			$appointment  = $model->getNextAppointment();
			$actionButton = '<a href="/patients/view?id='
							. $model->id
							. '&action='
							. Appointment::STATUS_OPEN
							. '" class="btn btn-xs btn-action"><span><i class="fa fa-plus"></i></span> '
							. Yii::t('app', 'Open Appointment')
							. '</a>';
			break;
	}

?>
<div class="patient-view">
    <div class="panel">
        <div class="panel panel-header">
            <div class="row">
                <div class="col-xs-3">
                    <button onclick="window.location.href = '<?= $prevUrl ?>';" class="btn btn-action"><i
                                class="fa fa-arrow-left"></i> <?= Yii::t('app', 'Back to ') . $prevModule ?></button>
                </div>
				
				<?php if(\common\models\User::getUserIdentity()
					->isDoctor()){ ?>
                    <div class="col-xs-9 text-right">
						<?= $actionButton ?>
                    </div>
				<?php } ?>
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
                                    <i class="fa fa-envelope text-primary-1"></i> <?= $model->user->email ?>
                                </div>
                                <div class="col-xs-12 col-md-4 text-xs-center text-md-left">
                                    <i class="fa fa-map-marker text-primary-1"></i> <?= $model->address
										? $model->address->toString() : Yii::t('app', 'Not Available') ?>
                                </div>
								<?php if(isset($appointment)): ?>
                                    <div class="col-xs-12 col-lg-4 text-xs-center text-lg-left">
                                        <a href="/prescriptions/create?appointment_id=<?= $appointment->id ?>"
                                           role="modal-remote" class="btn btn-tertiary btn-xs"
                                           style="margin: -4px;"><?= Yii::t('app', 'Add Prescription') ?></a>
                                    </div>
								<?php endif; ?>
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
                    <div class="separator form-group"></div>
					
					<?php if($appointment_status == Appointment::STATUS_OPEN): ?>
                        <div class="row"><!-- Add Record -->
                            <div class="col-xs-12">
                                <h4 class="no-margin"><?= Yii::t('app', 'Record') ?></h4>
                            </div>
                        </div>
						<?php $form = ActiveForm::begin(); ?>
                        <div class="row">
                            <div class="col-md-4">
								<?= $form->field($procedure2doctor,
									'specialty_id',
									['options' => ['class' => 'form-group no-margin']])
									->dropDownList($specialtyArray)
								; ?>
                            </div>
                            <div class="col-md-4">
								<?= $form->field($procedure2doctor,
									'procedure_id',
									['options' => ['class' => 'form-group no-margin']])
									->dropDownList($procedureArray)
								; ?>
                            </div>
                            <div class="col-md-4">
								<?= $form->field($clinicalStory,
									'clinical_story_type_id',
									['options' => ['class' => 'form-group no-margin']])
									->dropDownList($allClinicalStoriyTypes)
								; ?>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xs-12">
								<?= $form->field($clinicalStory,
									'summary',
									['options' => ['class' => 'form-group no-margin']])
									->widget(TinyMce::className(),
										[
											'options'       => ['rows' => 6],
											'language'      => Yii::$app->language,
											'clientOptions' => [
												/*'plugins' => [
														"advlist autolink lists link charmap print preview anchor",
														"searchreplace visualblocks code fullscreen",
														"insertdatetime media table contextmenu paste"
												],*/
												
												'menubar' => false,
												'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ",
											],
										]) ?>
                            </div>
                        </div>
                        <div class="row" id="attachments_container">

                        </div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group no-margin">
                                    <label class="control-label" for="attach_file"><?= Yii::t('app',
											'Browse File') ?></label>
                                    <div class="row">
                                        <div class="col-xs-6">
											<?= Html::fileInput('attach_file',
												null,
												[
													'id'       => 'attach_file',
													'accept'   => 'application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/*',
													'onchange' => 'add_attachment(this)',
												]) ?>

                                            <input type="text" readonly="" class="form-control"
                                                   placeholder="<?= Yii::t('app', 'Browse Files...') ?>"
                                                   id="picture_list">
                                        </div>
                                        <div class="col-xs-6 text-right">
                                            <div class="input-group">
                                                <button type="button" id="attach_file_button"
                                                        class="btn btn-tertiary btn-xs"
                                                        onclick="selectAttachment()"><?= Yii::t('app',
														'Attach File') ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="separator form-group"></div>

                        <div class="row">
                            <div class="col-xs-12 text-xs-center text-md-right">
                                <button type="submit" class="btn btn-sm btn-primary"><?= Yii::t('app',
										'Add Record') ?></button>
                            </div>
                        </div>
						<?php ActiveForm::end(); ?>
					<?php endif; ?>
                </div>
				<?php if(\common\models\User::getUserIdentity()
						->isDoctor()){ ?>
                <div class="col-md-5"><!-- Records -->
                    <div id="record_list">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 class="" style="padding-left: 15px;"><?= Yii::t('app', 'Records'); ?></h4>
                            </div>
                        </div>
                        <div class="row">
	                        <div class="col-md-11">
								<div class="checkbox text-justify">
								<?php foreach ($allClinicalStoriyTypes as $storyId => $storyType): ?>
								<?php $checked_story = Yii::$app->request->get('story_type_'.$storyId, "true") == 'true' ? ' checked="checked"' : ''; ?>
						          	<label class="confirmed">
						            	<input type="checkbox" id="story_type_<?= $storyId ?>" class="filter-checkbox"<?= $checked_story ?>> <?= $storyType ?>
						          	</label>
						        <?php endforeach; ?>
			        			</div>
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
										'pjaxSettings' => [
												'options' => [
														'id' => 'pjax-clinical-story'
												]
										],
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
                                <h4 id="story_type"><?= Yii::t('app', 'Record Preview') ?></h4>
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
                <?php } ?>
            </div>

        </div>
    </div>
</div>

<?php Modal::begin([
	"id"     => "ajaxCrudModal",
	"footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
