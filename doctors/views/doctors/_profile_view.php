<?php

use common\assets\GlisseAsset;
use yii\bootstrap\Html;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;
use common\widgets\AddressFiller;
use common\models\Address;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use common\models\Specialty;
use common\assets\VideoHelperAsset;
use common\models\Procedure2doctor;
use kartik\widgets\Typeahead;
use common\models\Certification;
use doctors\controllers\DoctorsController;
use common\models\Currency;

/* @var $user \common\models\User */
/* @var $doctor \common\models\Doctor */

$user = Yii::$app->user->identity;
$doctor = $user->doctor;
$address = $doctor->postalAddress ? $doctor->postalAddress : new Address();


$doctorsDomain = Yii::$app->params['doctorsDomain'];

if ($doctor->doctorVideos) {
	$youtubeId = ($doctor->doctorVideos[0]->url);
	$youtubeUrl = 'https://youtube.com/watch?v='.$youtubeId;
	$youtubeEmbedUrl = 'https://www.youtube.com/embed/'.$youtubeId;
}
else {
	$youtubeId = $youtubeUrl = $youtubeEmbedUrl = '';
}

$selectProcedureText = Yii::t('app', 'Select Procedure');
$selectedSpecialty = $doctor->specialties ? $doctor->specialties[0] : null;
if ($selectedSpecialty){
	$proceduresArray = ['' => $selectProcedureText] + ArrayHelper::map($selectedSpecialty->procedures, 'id', function($element){ return Json::decode($element->name, true)[Yii::$app->language];});
	$selectedSpecialtyId = $selectedSpecialty->id;
}
else {
	$proceduresArray = ['' => $selectProcedureText];
	$selectedSpecialtyId = null;
}

$currencyArray = ArrayHelper::map(Currency::find()->all(), 'id', function($element){return $element->code;});
$specialtiesArray = ['' => Yii::t('app', 'Select Specialty')] + ArrayHelper::map(Specialty::find()->all(), 'id', function($element){ return Json::decode($element->name, true)[Yii::$app->language];});
$certificationsArray = ArrayHelper::merge([''], array_values(ArrayHelper::map(Certification::find()->all(), 'id', 'name')));

$doctorPictures = $doctor->doctorPictures;
$doctorPicturesCount = $doctor->getMembership()->picture_count;
// Get the address of the main office

GlisseAsset::register($this);

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

VideoHelperAsset::register($this);

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

function update_video(text){
	const id = getVideoId(text);
	const url = getVideoUrl(text); 	
	const embedUrl = getVideoEmbedUrl(text);

	$('#promotional_video_preview').attr('src', embedUrl);
	$('#video_id').val(id);
	$('#video_url').val(url);
}

function changeProcedureSpecialty(){
	$.get('/doctors/ajax-get-specialty-procedures', {id: $('#procedure_specialties_list').val()}, function(data){
		let options = '<option value="">{$selectProcedureText}</option>'; 
		$.each(data.results, function(id, item){
			options += '<option value="'+id+'">'+item+'</option>';
		});
		$('#procedure_list').html(options);
	}).fail(function(error){
		console.log(error)
		$('#procedure_list').html('<option value="">{$selectProcedureText}</option>');
	});
 }

function add_specialty(id, name){
	let items = $('#specialty_items');
	let item ='<div class="row">'+
		'<div class="col-xs-12">'+
			'<div class="form-group no-margin">'+
				'<div class="input-group">'+
					'<input type="text" name="specialty['+id+']" id="specialty_'+id+'" class="form-control" data-specialty-id="'+id+'" value="'+name+'" readonly>'+
					'<span class="input-group-btn"><button type="button" class="btn btn-secondary" onclick="remove_specialty(this)"><i class="fa fa-close"></i></button></span>'+
				'</div>'+
			'</div>'+
		'</div>'+
	 '</div>';	
	
	if ($('#specialty_'+id).val() === undefined && id != ''){ //Add it
		items.append(item);
		$('#procedure_specialties_list').append('<option value="'+id+'">'+name+'</option>').trigger('change');
	}	
}
function remove_specialty(sender){
	const id = $(sender).parent().parent().find('input').attr('data-specialty-id');
	$(sender).parent().parent().parent().parent().parent().remove();

	//Remove th specialty frm the dropdown
	$('#procedure_specialties_list').find('option[value='+id+']').remove();

	//Refresh the list of procedures
	$('#procedure_specialties_list').trigger('change')

	//Remove the added procedures of that specialty
	$('#procedure_items').find('[data-specialty-id='+id+']').parent().parent().parent().parent().remove();
	
}

function add_procedure(id, name, price, specialty_id, currency_id){
	let item ='<div class="row">'+
				'<div class="col-xs-12">'+
					'<div class="form-group no-margin">'+
						'<div class="input-group">'+
							'<input type="hidden" name="procedure_specialty_id['+id+']" id="procedure_specialty_id_'+id+'" value="'+specialty_id+'">'+
							'<input type="hidden" name="procedure_currency_id['+id+']" id="procedure_currency_id_'+id+'" value="'+currency_id+'">'+
							'<input type="hidden" name="procedure_price['+id+']" id="procedure_price_'+id+'" value="'+price+'">'+
							'<input type="text" name="procedure['+id+']" id="procedure_'+id+'" class="form-control" data-specialty-id="'+specialty_id+'" value="'+name+'  ('+price+' '+$('#procedure_currency_list option:selected').html()+')" readonly>'+
							'<span class="input-group-btn"><button type="button" class="btn btn-secondary" onclick="remove_procedure(this)"><i class="fa fa-close"></i></button></span>'+
						'</div>'+
					'</div>'+
				'</div>'+
			'</div>';
	if ($('#procedure_'+id).val() === undefined && price != '' && id != '')
		$('#procedure_items').append(item);
}

function remove_procedure(sender){
	$(sender).parent().parent().parent().parent().parent().remove();
}

function add_certification(name){
	let item ='<div class="row">'+
				'<div class="col-xs-12">'+
					'<div class="form-group no-margin">'+
						'<div class="input-group">'+
							'<input type="text" name="certification[]" class="form-control" value="'+name+'" readonly>'+
							'<span class="input-group-btn"><button type="button" class="btn btn-secondary" onclick="remove_certification(this)"><i class="fa fa-close"></i></button></span>'+
						'</div>'+
					'</div>'+
				'</div>'+
			'</div>';
	found = false;
		$('#certification_items').find('input.form-control').each(function(index, elem){
			if ($(elem).val() == name){
				found = true;
			}
		});

	if (!found && name != '')
		$('#certification_items').append(item);
}

function remove_certification(sender){
	$(sender).parent().parent().parent().parent().parent().remove();
}

function selectGalleryPicture(){
	$('#picture_list_file').trigger('click');
}

function add_picture(input){
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			// Put images in their places
			// The image comes in e.target.reult
			const image = e.target.result;
			const item = '<span class="pict-thumbnail-wrapper">'+
							'<input type="hidden" name="picture[]" value="'+image+'">'+
							' <img  class="pict-thumbnail glisse-pict" src="'+image+'" data-glisse-big="'+image+'" rel="group1" style="width: 180px; margin: 1px 1px 1px 1px;">'+
							' <span class="close-icon" onclick="remove_picture(this);"><i class="fa fa-times"></i></span>'+
						' </span>';
			if ($('#gallery_items').find('.pict-thumbnail-wrapper .close-icon').length < {$doctorPicturesCount}){
				$('#gallery_items .pict-thumbnail-wrapper:last-child').remove();	
				let last_item = $('#gallery_items .pict-thumbnail-wrapper .close-icon').last().parent();
				if (last_item.length > 0)
					last_item.after(item);
				else
					$('#gallery_items').prepend(item)
				initGlisse();
				last_item = $('#gallery_items .pict-thumbnail-wrapper .close-icon').last().parent();
				$(last_item).find('img').css({'width': ''});
				
			}
        };

        reader.readAsDataURL(input.files[0]);
	}
}
function remove_picture(sender){
	const parent = $(sender).parent().parent();
	$(sender).parent().remove();
	parent.append('<span class="pict-thumbnail-wrapper">'+
						'<img class="pict-thumbnail" src="{$doctorsDomain}/images/blank.png" style="margin: 1px 2px 1px 1px; ">'+
					'</span>');
}

JS;

$this->registerJs($js2, View::POS_END, 'dynamic-functions');

?>
<?php $form = ActiveForm::begin(); ?>
<?= Html::hiddenInput('activePage', DoctorsController::PROFILE) ?>
<div class="row">
	<div class="col-md-4 separator-md-right">
		<div class="row form-group">
			<div class="col-lg-5 text-xs-center text-lg-left">
				<img id="profile_picture" alt="doctor-picture" class="img img-circle img-large glisse-pict fast-change-picture" src="<?= $doctor->getPicture() ?>">
			</div>
			<div class="col-lg-7 text-xs-center text-lg-left" style="margin-top: 30px;">
				<?= Html::button(Yii::t('app', 'Upload Photo'), ['class' => 'btn btn-tertiary', 'onclick' => 'selectPicture()']); ?>
				<?= $form->field($user, 'picture_file')->fileInput(['id' => 'change_picture', 'onchange' => 'changePicture(this)', 'style' => 'display: none;', 'accept' => 'image/*'])->label(false) ?>
			</div>
		</div>
		<div class="row" style="margin-top: 20px;">
			<div class="col-xs-12">
				<h3><?= Yii::t('app', 'Contact Info'); ?></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= $form->field($user, 'first_name', ['options' => ['class' => 'form-group no-margin']]); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= $form->field($user, 'last_name', ['options' => ['class' => 'form-group no-margin']]); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= $form->field($user, 'username', ['options' => ['class' => 'form-group no-margin']])->textInput(['disabled' => true]); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= $form->field($user, 'email', ['options' => ['class' => 'form-group no-margin']]); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= $form->field($user, 'phone', ['options' => ['class' => 'form-group no-margin']])->widget(MaskedInput::className(), [
						'mask' => '(999)-999-9999',
						'clientOptions' => [
								'removeMaskOnSubmit' => true
						],
				]); ?>
			</div>
		</div>
		
		<div class="row" style="margin-top: 20px;">
			<div class="col-xs-12">
				<div class="col-xs-12">
					<h3><?= Yii::t('app', 'Address'); ?></h3>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= AddressFiller::widget(['options' => ['form' => $form, 'address' => $address, 'id' => 'profile']]); ?>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="row">
			<div class="col-xs-12">
				<h3><?= Yii::t('app', 'Specialties') ?></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-8">
				<div class="form-group no-margin">
					<?= Html::dropDownList('specialty_list', null, $specialtiesArray, ['class' => 'form-control', 'id' => 'specialty_list']); ?>
				</div>
			</div>
			
			<div class="col-xs-4 text-right">
				<div class="form-group no-margin">	
					<div class="input-group">
						<button type="button" id="add_specialty_button" class="btn btn-tertiary btn-xs" onclick="add_specialty($('#specialty_list').val(), $('#specialty_list option:selected').html())"><?= Yii::t('app', 'Add') ?></button>
					</div>
				</div>
			</div>
		</div>	
		
		<div id="specialty_items">
			<?php foreach (ArrayHelper::map($doctor->specialties, 'id', 'name') as $id => $name): ?>
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group no-margin">
						<div class="input-group">
							<?= Html::textInput('specialty['.$id.']', Json::decode($name, true)[Yii::$app->language], ['readonly' => true, 'class' => 'form-control', 'id' => 'specialty_'.$id, 'data-specialty-id' => $id]); ?>
							<span class="input-group-btn"><button type="button" class="btn btn-secondary" onclick="remove_specialty(this)"><i class="fa fa-close"></i></button></span>
						</div>
					</div>
				</div>
			</div>	
			<?php endforeach; ?>
		</div>		
		
		<div class="row">
			<div class="col-xs-12">
				<h3><?= Yii::t('app', 'About the Doctor') ?></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= $form->field($doctor, 'resume', ['options' => ['class' => 'form-group no-margin']])->textarea()->label(false); ?>
			</div>
		</div>
		
		<div class="row">
			<div class="col-xs-12">
				<h3><?= Yii::t('app', 'Promotional Video') ?></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-7">
				<div class="form-group no-margin">
					<?= Html::textInput('video_url', $youtubeUrl, ['class' => 'form-control', 'id' => 'video_url', 'placeholder' => Yii::t('app', 'Paste your YouTube video url')]); ?>
					<?= Html::hiddenInput('video_id', $youtubeId, ['id' => 'video_id']) ?>
				</div>
			</div>
			<div class="col-xs-5 text-right">
				<div class="form-group no-margin">	
					<div class="input-group">
						<button type="button" id="update_video_button" class="btn btn-tertiary btn-xs" onclick="update_video($('#video_url').val())"><?= Yii::t('app', 'Preview') ?></button>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 text-center">
				<iframe allowFullScreen="allowFullScreen" id="promotional_video_preview" class="embeded-video" src="<?= $youtubeEmbedUrl ?>"></iframe>
			</div>
		</div>
		
	</div>
	
	<div class="col-md-4">
		<div class="row">
			<div class="col-xs-12">
				<h3><?= Yii::t('app', 'Procedures') ?></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group no-margin">
					<?= Html::dropDownList('procedure_specialties_list', 
							$selectedSpecialtyId, 
							ArrayHelper::map($doctor->specialties, 'id', function($element){ return Json::decode($element->name, true)[Yii::$app->language];}), 
							[
									'class' => 'form-control', 
									'id' => 'procedure_specialties_list',
									'onchange' => 'changeProcedureSpecialty()'
							]); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-4">
				<div class="form-group no-margin">
					<?= Html::dropDownList('procedure_list', null, $proceduresArray, ['class' => 'form-control', 'id' => 'procedure_list']); ?>
				</div>
			</div>
			<div class="col-xs-2">
				<div class="form-group no-margin">
					<?= MaskedInput::widget([
						'name' => 'set_procedure_price',
						'id' => 'set_procedure_price',
						'clientOptions' => [
								'alias' => 'decimal',
								'digits' => 2,
								'digitsOptional' => false,
								'radixPoint' => '.',
								'groupSeparator' => ',',
								'autoGroup' => true,
								'removeMaskOnSubmit' => true
						],
						'options' => [
								'placeholder' => Yii::t('app', 'Price'),
								'class' => 'form-control'
						]
					]); ?>
				</div>
			</div>
			<div class="col-xs-2">
			
				<div class="form-group no-margin">
					<?= Html::dropDownList('procedure_currency_list', null, $currencyArray, ['class' => 'form-control', 'id' => 'procedure_currency_list' ]) ?>
				</div>
			</div>
			
			<div class="col-xs-4 text-right">
				<div class="form-group no-margin">	
					<div class="input-group">
						<button type="button" id="add_procedure_button" class="btn btn-tertiary btn-xs" 
							onclick="add_procedure($('#procedure_list').val(), $('#procedure_list option:selected').html(), $('#set_procedure_price').val(), $('#procedure_specialties_list').val(), $('#procedure_currency_list').val())"><?= Yii::t('app', 'Add') ?></button>
					</div>
				</div>
			</div>
		</div>	
		
		<div id="procedure_items">
			<?php foreach ($doctor->procedures as $procedure): ?>
			<?php $doctorProcedure = Procedure2doctor::find()->where(['procedure_id' => $procedure->id, 'doctor_id' => $doctor->id ])->one(); ?>
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group no-margin">
						<div class="input-group">
							<?= Html::hiddenInput('procedure_specialty_id['.$procedure->id.']', $doctorProcedure->specialty_id, ['id' => 'procedure_specialty_id_'.$procedure->id]) ?>
							<?= Html::hiddenInput('procedure_currency_id['.$procedure->id.']', $doctorProcedure->currency_id, ['id' => 'procedure_currency_id_'.$procedure->id]) ?>
							<?= Html::hiddenInput('procedure_price['.$procedure->id.']', $doctorProcedure->price, ['id' => 'procedure_price_'.$procedure->id, 'data-specialty-id' => $doctorProcedure->specialty_id]) ?>
							<?= Html::textInput('procedure['.$procedure->id.']', 
									Json::decode($procedure->name, true)[Yii::$app->language]. 
									'  ('.$doctorProcedure->priceText.')', 
									['readonly' => true, 'class' => 'form-control', 'id' => 'procedure_'.$procedure->id]); ?>
							<span class="input-group-btn"><button type="button" class="btn btn-secondary" onclick="remove_procedure(this)"><i class="fa fa-close"></i></button></span>
						</div>
					</div>
				</div>
			</div>	
			<?php endforeach; ?>
		</div>
		
		
		<div class="row">
			<div class="col-xs-12">
				<h3><?= Yii::t('app', 'Certifications') ?></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-8">
				<div class="form-group no-margin">
					
					<?= Typeahead::widget([
						'name' => 'certification_list',
						'pluginOptions' => ['highlight' => true],
						'options' => [
							'placeholder' => Yii::t('app', 'Add your certification ...'),
							'id' => 'certification_list',
						],
						'dataset' => [
							[
								'local' => $certificationsArray,
								'limit' => 10,
							]
						],
						
					]) ?>
				</div>
			</div>		
			<div class="col-xs-4 text-right">
				<div class="form-group no-margin">	
					<div class="input-group">
						<button type="button" id="add_certification_button" class="btn btn-tertiary btn-xs" onclick="add_certification($('#certification_list').val())"><?= Yii::t('app', 'Add') ?></button>
					</div>
				</div>
			</div>
		</div>
		
		<div id="certification_items">
			<?php foreach ($doctor->certifications as $certification): ?>
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group no-margin">
							<div class="input-group">
								<?= Html::textInput('certification[]', $certification->name, [ 
										'readonly' => true, 
										'class' => 'form-control', 		
								]); ?>
								<span class="input-group-btn"><button type="button" class="btn btn-secondary" onclick="remove_certification(this)"><i class="fa fa-close"></i></button></span>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		
		<div class="row">
			<div class="col-xs-12">
				<h3><?= Yii::t('app', 'Gallery') ?></h3>	
			</div>
		</div>
		
		<div class="row">
			<div class="col-xs-6">
				<div class="form-group no-margin">
					<?= Html::fileInput('picture_list', null, [
							'id' => 'picture_list_file',
							'accept' => 'image/*',
							'onchange' => 'add_picture(this)'
					]) ?>
					
				    <input type="text" readonly="" class="form-control" placeholder="<?= Yii::t('app', 'Browse Files...') ?>" id="picture_list">
					
				</div>
				
			</div>
			<div class="col-xs-6 text-right">
				<div class="form-group no-margin">	
					<div class="input-group">
						<button type="button" id="add_picture_button" class="btn btn-tertiary btn-xs" onclick="selectGalleryPicture()"><?= Yii::t('app', 'Attach File') ?></button>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-xs-12">
				<div id="gallery_items">
				<?php for($i = 0; $i < $doctorPicturesCount; $i++):?>
					<span class="pict-thumbnail-wrapper">
					<?php if (count($doctorPictures) > $i): ?>
							<input type="hidden" name="picture[]" value="<?= $doctorPictures[$i]->id ?>">
							<img  class="pict-thumbnail glisse-pict" src="<?= $doctorPictures[$i]->getPicture() ?>" data-glisse-big="<?= $doctorPictures[$i]->getPicture() ?>" rel="group1">
							<span class="close-icon" onclick="remove_picture(this);"><i class="fa fa-times"></i></span>
					<?php else: ?>
						<img class="pict-thumbnail" src="<?= $doctorsDomain ?>/images/blank.png">
					<?php endif; ?>
					</span>
				<?php endfor; ?>
				</div>
			</div>
		</div>
		
	</div>
</div>
<div class="row">
	<div class="col-sm-12 text-right">
		<a href="" class="btn btn-secondary"><?= Yii::t('app', 'Cancel') ?></a>
		<button type="submit" class="btn btn-primary"><?= Yii::t('app', 'Save Changes') ?></button>
	</div>
</div>
<?php ActiveForm::end(); ?>