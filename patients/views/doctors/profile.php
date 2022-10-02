<?php
use kartik\rating\StarRating;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;
use common\assets\GlisseAsset;
use johnitvn\ajaxcrud\CrudAsset;
use common\models\User;

/* @var $this yii\web\View */
/* @var $doctor \common\models\Doctor */
/* @var $user \common\models\User */
/* @var $office_id int|null */

GlisseAsset::register($this);
$js = <<<JS
	function adjustPictureSize(){
		$('.pict-thumbnail').each(function(){
			var imgWidth = $(this).width();
			$(this).css({height: imgWidth});
		});
	}
	
	$('.glisse-pict').glisse({
				changeSpeed:550,
				speed:500,
				effect:'bounce',
				fullscreen:false
			});
	adjustPictureSize();
	$(window).resize(function(){
    	adjustPictureSize();
	});

	$('#ajaxCrudModal').on('hide.bs.modal', function(event){
		refreshData();
	});

	function refreshData(){
		$('#doctor_calendar').fullCalendar('refetchEvents');
		$('#send_appointment').attr('href', '/patients/send-appointment?doctor_id={$doctor->id}&office_id={$office_id}');
	}

JS;
CrudAsset::register($this);

$this->registerJs($js, View::POS_READY, 'init-glisse-gallery');

$js2 = <<<JS
	function clickDateTime(date, jsEvent, view){
		var calendar = $('#doctor_calendar');
		switch (view.name){
			case 'agendaDay':
			case 'agendaWeek':						
				$('#send_appointment').attr('href', '/patients/send-appointment?doctor_id={$doctor->id}&office_id={$office_id}&dateTime='+date.format('YYYY-MM-DD[T]HH:mm:ss')).click();
				break;
			case 'month':
				calendar.fullCalendar('changeView', 'agendaDay', date.format('YYYY-MM-DD'));				
				break;
		}
	}

JS;

$this->registerJs($js2, View::POS_END, 'calendar-functions');

$patient_id = null;
if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isPatient()){
	$patient_id = Yii::$app->user->identity->patient->id;
}

$user = $doctor->user;

$patient_user_id = Yii::$app->user->isGuest ? null : User::getUserIdentity()->id;
$js3 = <<<JS
	function rate(value){
		$.post('/doctors/ajax-rate', {value: value, doctor_id:{$doctor->id}}, function(data){
			$('#rating_{$doctor->id}').rating('update', data.avgRate);
		}).fail(function(error){
			console.log(error);
		});
	}
	
	function checkSending(sender){
		if ($(sender).val() == '') {
			$(sender).parent().parent().find('button').attr('disabled', true);
		}
		else {
			$(sender).parent().parent().find('button').removeAttr('disabled');
		}
	}

	function sendComment(parent_id){
		parent_id = parent_id || null;
		let text = "";
		if (parent_id != null){
			text = $('#reply_'+parent_id).val();
			$('#reply_'+parent_id).val('').trigger('keyup');
			
		}
		else {
			text = $('#comment').val();
			$('#comment').val('').trigger('keyup');
		}
		
		$.post('/doctors/ajax-send-comment', {target_id:{$user->id}, from_id:{$patient_user_id}, parent_comment_id: parent_id, text: text}, function(data){
			
			let cssClass = data.parent_id == "" ? '12' : '11 col-xs-offset-1';
			let textClass = data.status == 200 ? 'text-secondary-1' : 'text-danger';
			let html = '<div class="row">'+
							'<div class="col-xs-'+cssClass+'">'+
								'<label class="text-primary-1 control-label">'+data.name+'</label>'+
								'<p class="'+textClass+'">'+data.text+'</p>'+
							'</div>'+
						'</div>'+
						'<div class="separator" style="margin-bottom: 0;"></div>';
			if (data.parent_id != ""){
				$('#new_replies_'+data.parent_id).append(html);
			}
			else {
				$('#new_comments').append(html);
			}
			$('#no_comments').hide();
		}).fail(function(error){
			console.log(error);
		});
		
	}
JS;
if ($patient_user_id){
	$this->registerJs($js3, View::POS_END, 'rating-functions');
}

$this->title = $user->first_name.' '.$user->last_name;
$specialties = Yii::t('app', '(None)');;
if ($specialtyArray = $doctor->specialties){
	$specialtyArray = ArrayHelper::map($specialtyArray, 'id', function($element){return Json::decode($element["name"])[Yii::$app->language];});
	
	$specialties = implode(', ', $specialtyArray);
}

$procedures = Yii::t('app', '(None)');
if ($procedureArray = $doctor->procedures){
	$procedureArray = ArrayHelper::map($procedureArray, 'id', function($element){return Json::decode($element["name"])[Yii::$app->language];});
	
	$procedures = implode(', ', $procedureArray);
}

$certifications = Yii::t('app', '(None)');
if ($certificationsArray = $doctor->certifications ){
	$certificationsArray = ArrayHelper::map($certificationsArray, 'id', 'name');
	$certifications = implode(', ', $certificationsArray);
}

$doctorPictures = $doctor->doctorPictures;
$maxPictures = min($doctor->getMembership()->picture_count, count($doctorPictures));

if ($doctor->doctorVideos) {
	$youtubeId = ($doctor->doctorVideos[0]->url);
	$youtubeEmbedUrl = 'https://www.youtube.com/embed/'.$youtubeId;
}
else {
	$youtubeId = $youtubeUrl = $youtubeEmbedUrl = '';
}

?>


<div class="panel">
	<div class="panel panel-head">
		<button class="btn btn-secondary" onclick="history.back()"><i class="fa fa-arrow-left"></i> <?= Yii::t('app', 'Back') ?></button>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-2 text-md-center"><!-- Picture -->
				<img alt="doctor-picture" class="img img-circle img-medium glisse-pict" src="<?= $user->getProfilePicture() ?>">
			</div>
			
			<div class="col-md-7 separator-md-right"><!-- General Data -->
				<div class="row">
					<div class="col-xs-12">
						<h3><?= $user->first_name . ' ' . $user->last_name ?></h3>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div style="float: left; margin: 10px 0; padding: 10px 0;">
							<?= StarRating::widget([
							'name' => 'rating_'.$doctor->id,
							'id' => 'rating_'.$doctor->id,
							'value' => $doctor->rating,
							'pluginOptions' => [
									'readonly'=>true,//(Yii::$app->user->isGuest || (User::getUserIdentity()->isPatient() &&!User::getUserIdentity()->patient->isPatientOf($user->id))), 
									'showClear'=>false,
									//'theme' => 'krajee-svg',
									'step' => 0.5,
									'filledStar' => '<i class="remedyk-star"></i>',
									'emptyStar' => '<i class="remedyk-star-o"></i>',
									
									'rtl' => true,
									'size' => '',
									'clearCaption' => '0.0',
									'defaultCaption' => '{rating}',
									'starCaptions' => new JsExpression("function(val){return val ? val.toFixed(1) : val;}"),
									'starCaptionClasses' => new JsExpression("function(val){return 'star-rating';}"),
							
							],
							'options' => ['style' => "font-family: 'AmplesoftMedium';"]
							]) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<h4><?= Yii::t('app', 'Specialties') ?></h4>
						<p class="text-justify"><?= $specialties ?></p>
					</div>
					<div class="col-md-6">
						<h4><?= Yii::t('app', 'Procedures') ?></h4>
						<p class="text-justify"><?= $procedures ?></p>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<h4><?= Yii::t('app', 'About the Doctor') ?></h4>
						<p class="text-justify"><?= $doctor->resume ?></p>
					</div>
					<div class="col-md-6">
						<h4><?= Yii::t('app', 'Certifications') ?></h4>
						<p class="text-justify"><?= $certifications ?></p>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12">
						<div class="separator"></div>
					</div>
				</div>
				
				<div class="row"> <!-- Media -->
					<div class="col-md-5">
						<h4><?= Yii::t('app', 'Promotional Video') ?></h4>
						<div class="row">
							<div class="col-xs-12 text-center">
								<iframe allowFullScreen="allowFullScreen" id="promotional_video_preview" class="embeded-video" src="<?= $youtubeEmbedUrl ?>"></iframe>
							</div>
						</div>
					</div>
					<div class="col-md-7" >
						<h4><?= Yii::t('app', 'Doctor Gallery') ?></h4>
											
						<div class="row">
							<div class="col-xs-12">
							<?php for($i = 0; $i < $maxPictures; $i++):?>
							
								<img class="pict-thumbnail glisse-pict" src="<?= $doctorPictures[$i]->getPicture() ?>" data-glisse-big="<?= $doctorPictures[$i]->getPicture() ?>" rel="group1">
							
							<?php endfor; ?>
							</div>
						</div>
												
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-xs-12">
						<div class="separator"></div>
					</div>
				</div>
				<?php if ($patient_id): ?>
				<?= $this->render('@common/partials/_doctor_calendar', ['id' => 'doctor_calendar', 'doctor_id' => $doctor->id, 'patient_id' => $patient_id, 'office_id' => $office_id,
							'customOptions' => [
									'dayClick' => new JsExpression('function(date, jsEvent, view){ if ($.inArray("secondary", jsEvent.target.classList) == -1 ) {clickDateTime(date, jsEvent, view);} }'),
							]
					]) ?>
				<?php endif; ?>
				
				<div class="row"> <!-- Comments -->
					<div class="col-xs-12">
						<h4><?= Yii::t('app', 'Reviews') ?></h4>
					</div>
				</div>
				<?php if (!Yii::$app->user->isGuest && (User::getUserIdentity()->isPatient() && User::getUserIdentity()->patient->isPatientOf($user->id))): ?>
				<?php $rating = User::getUserIdentity()->patient->getRating($doctor->id); ?>
				<div class="row">
					<div class="col-md-12">
						<h5><?= Yii::t('app', 'Your rate for this Doctor: ') ?></h5>
					</div>
				</div>
				<div class="row">					
					<div class="col-xs-12">
						<div style="float: left; margin: 10px 0; padding: 10px 0;">
							<?= StarRating::widget([
							'name' => 'your_rating_'.$doctor->id,
							'id' => 'your_rating_'.$doctor->id,
							'value' => $rating,
							'pluginOptions' => [
									'readonly'=>false,  
									'showClear'=>false,
									//'theme' => 'krajee-svg',
									'step' => 1,
									'filledStar' => '<i class="remedyk-star"></i>',
									'emptyStar' => '<i class="remedyk-star-o"></i>',
									
									'rtl' => true,
									'size' => '',
									'clearCaption' => '0.0',
									'defaultCaption' => '{rating}',
									'starCaptions' => new JsExpression("function(val){return val ? val.toFixed(1) : val;}"),
									'starCaptionClasses' => new JsExpression("function(val){return 'star-rating';}"),
							
							],
							'pluginEvents' => [
								'rating:change' => new JsExpression("function(event, value, caption){rate(value);}"),
							],
							'options' => ['style' => "font-family: 'AmplesoftMedium';", 'onclick' => 'rate(this)']
							]) ?>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<div class="separator" style="margin-bottom: 0;"></div>
				<?php if ($doctor->getSortedComments()): ?>
					<?php foreach ($doctor->getSortedComments() as $comment): ?>
						
						<?php if (($comment->approved_by && !$comment->banned_by) || (!Yii::$app->user->isGuest && $comment->from_id == User::getUserIdentity()->id) || (!Yii::$app->user->isGuest && User::getUserIdentity()->isStaff())): ?>
							<div class="row">
								<div class="col-xs-12">
									<label class="text-primary-1 control-label"><?= $comment->from->name ?></label>
									<?php if (($comment->approved_by && !$comment->banned_by) || (!Yii::$app->user->isGuest && User::getUserIdentity()->isStaff())): ?>
										<p><?= $comment->text ?> <?php if ($comment->banned_by){ ?>- <span class="text-rejected">(<?= Yii::t('app', 'Banned by ').$comment->bannedBy->name ?>)</span><?php } ?></p>
									<?php elseif(!$comment->banned_by): ?>
										<p class="text-secondary-1"><?= Yii::t('app', 'Your comment is being moderated before it can be shown.') ?></p>
									<?php else: ?>
										<p class="text-danger"><?= Yii::t('app', 'Your comment has being banned.').$comment->ban_reason ?></p>
									<?php endif; ?>
								</div>
							</div>
							<div class="separator" style="margin-bottom: 0;"></div>
							<?php if ($doctor->getSortedComments($comment->id)): ?>
								<?php foreach ($doctor->getSortedComments($comment->id) as $response): ?>
									<?php if (($response->approved_by && !$response->banned_by) || (!Yii::$app->user->isGuest && $response->from_id == User::getUserIdentity()->id) || (!Yii::$app->user->isGuest && User::getUserIdentity()->isStaff())): ?>
										<div class="row">
											<div class="col-xs-11 col-xs-offset-1">
												<label class="text-primary-1 control-label"><?= $response->from->name ?></label>
												<?php if (($response->approved_by && !$response->banned_by) || (!Yii::$app->user->isGuest && User::getUserIdentity()->isStaff())): ?>
													<p><?= $response->text ?> <?php if ($response->banned_by){ ?>- <span class="text-rejected">(<?= Yii::t('app', 'Banned by ').$response->bannedBy->name ?>)</span><?php } ?></p>
													
												<?php elseif(!$response->banned_by): ?>
													<p class="text-secondary-1"><?= Yii::t('app', 'Your comment is being moderated before it can be shown.') ?></p>
												<?php else: ?>
													<p class="text-danger"><?= Yii::t('app', 'Your comment has being banned.').$response->ban_reason ?></p>
												<?php endif; ?>
											</div>
										</div>
										<div class="separator" style="margin-bottom: 0;"></div>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
							<?php if (!Yii::$app->user->isGuest && (User::getUserIdentity()->isPatient() && User::getUserIdentity()->patient->isPatientOf($user->id))): ?>
								<span id="new_replies_<?= $comment->id ?>"></span>
								<div class="row">
									<div class="col-xs-11 col-xs-offset-1">
										<div class="input-group" style="background-color: #EEEEEE;">
											<div class="form-group" style="padding-left: 10px;">
												<input type="text" id="reply_<?= $comment->id ?>" class="form-control"  placeholder="<?= Yii::t('app', 'Leave a Reply...') ?>" onkeyup="checkSending(this);">
											</div>
										    <span class="input-group-btn">
										        <button class="btn btn-primary" type="button" onclick="sendComment(<?= $comment->id ?>)" disabled><?= Yii::t('app', 'Send') ?></button>
										    </span>
										</div>
									</div>
								</div>
							<?php endif; ?>
							<div class="separator" style="margin-bottom: 0;"></div>
						<?php endif; ?>
					<?php endforeach;?>
				<?php else: ?>
					<div class="row" id="no_comments"> <!-- Comments -->
						<div class="col-xs-12">
							<p><?= Yii::t('app', 'No reviews yet.') ?></p>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if (!Yii::$app->user->isGuest && (User::getUserIdentity()->isPatient() && User::getUserIdentity()->patient->isPatientOf($user->id))): ?>
					<span id="new_comments"></span>
					<div class="row">
						<div class="col-xs-12">
							<div class="input-group" style="background-color: #EEEEEE;">
								<div class="form-group" style="padding-left: 10px;">
									<input type="text" id="comment" class="form-control" placeholder="<?= Yii::t('app', 'Leave a Review...') ?>" onkeyup="checkSending(this);">
								</div>
							    <span class="input-group-btn">
							        <button class="btn btn-primary" type="button" onclick="sendComment()" disabled><?= Yii::t('app', 'Send') ?></button>
							    </span>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<div class="row">
					<div class="col-xs-12">
						<div class="separator"></div>
					</div>
				</div>
			</div>
			
			<div class="col-md-3"><!-- Actions -->
				<!-- <h4><?= Yii::t('app', 'Business Hours') ?></h4> -->
				<div class="row">
					<div class="col-xs-6">
					</div>
					<div class="col-xs-6">
					</div>
				</div>
				<?php if (Yii::$app->user->isGuest || $patient_id): ?>
				<div class="form-group">
					<a id="send_appointment" <?php if ($patient_id) {?>href="/patients/send-appointment?doctor_id=<?= $doctor->id ?>&office_id=<?= $office_id ?>" role="modal-remote" <?php } else { ?> href="/site/login?doctor_id=<?= $doctor->id ?>&office_id=<?= $office_id ?>"<?php } ?>class="btn btn-tertiary"><?= Yii::t('app', 'Set Appointment') ?></a>
				
				</div>
				<?php endif; ?>
				
			</div>
		</div>
	</div>
</div>

<?php Modal::begin([
    "id"=>"ajaxCrudModal",
	//'size' => 'large',
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>