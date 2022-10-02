<?php
	
	use kartik\widgets\StarRating;
	use yii\base\Widget;
	use yii\helpers\Json;
	use yii\web\JsExpression;
	/* @var $office common\models\Office */
	/* @var $doctor common\models\Doctor */
	$doctor = $office->doctor;
	$picture = $doctor->getPicture();
	$user    = $doctor->user;
	
	$specialties = Yii::t('app', '(None)');;
	if($specialtyArray = $doctor->specialties){
		$specialtyArray = \yii\helpers\ArrayHelper::map($specialtyArray,
			'id',
			function($element){
				return Json::decode($element["name"])[Yii::$app->language];
			});
		
		$specialties = implode(', ', $specialtyArray);
	}
	
	$procedures =  Yii::t('app', '(None)');
	if($procedureArray = $doctor->procedures){
		$procedureArray = \yii\helpers\ArrayHelper::map($procedureArray,
				'id',
				function($element){
					return Json::decode($element["name"])[Yii::$app->language];
				});
		
		$procedures = implode(', ', $procedureArray);
	}
	
?>

<div class="row">
    <div class="col-md-2">
        <div class="row visible-xs-block visible-sm-block"><!-- Title (Doctor) -->
            <div class="col-xs-12 text-bold text-primary-1" style="margin-bottom: 10px;">
				<?= Yii::t('app', 'Doctor') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"><!-- Picture -->
                <img src="<?= $picture; ?>" class="img img-circle img-small" style="margin: 10px 0;"/>
            </div>
            <div class="col-md-10"><!-- Name -->
                <a data-pjax="false" href="/doctors/profile?id=<?= $doctor->id ?>&office_id=<?= $office->id ?>"><h4 class="text-bold"
                                                                                       style="padding: 10px 0; "><?= $user->first_name
																													 . ' '
																													 . $user->last_name ?></h4>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-2"><!-- Specialties -->
        <div class="row visible-xs-block visible-sm-block"><!-- Title (Specialties) -->
            <div class="col-xs-12 text-bold text-primary-1" style="margin-bottom: 10px;">
				<?= Yii::t('app', 'Specialties') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
				<?= $specialties ?>
            </div>
        </div>
    </div>
    <div class="col-md-2"><!-- Procedures -->
        <div class="row visible-xs-block visible-sm-block"><!-- Title (Procedures) -->
            <div class="col-xs-12 text-bold text-primary-1" style="margin-bottom: 10px;">
				<?= Yii::t('app', 'Procedures') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
            	<?= $procedures ?>
            </div>
        </div>
    </div>
    <div class="col-md-2"><!-- Rating -->
        <div class="row visible-xs-block visible-sm-block"><!-- Title (Rating) -->
            <div class="col-xs-12 text-bold text-primary-1" style="margin-bottom: 10px;">
				<?= Yii::t('app', 'Rating') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div style="float: left; margin: 10px 0; padding: 10px 0;">
					<?= StarRating::widget([
						'name'          => 'rating_' . $doctor->id.'_'.$office->id,
						'id'            => 'rating_' . $doctor->id.'_'.$office->id,
						'value'         => $doctor->rating,
						'pluginOptions' => [
							'readonly'           => true,
							'showClear'          => false,
							//'theme' => 'krajee-svg',
							'step'               => 0.5,
							'filledStar'         => '<i class="remedyk-star"></i>',
							'emptyStar'          => '<i class="remedyk-star-o"></i>',
							
							//'rtl' => true,
							'size'               => '',
							'clearCaption'       => '0.0',
							'defaultCaption'     => '{rating}',
							'starCaptions'       => new JsExpression("function(val){return val ? val.toFixed(1) : val;}"),
							'starCaptionClasses' => new JsExpression("function(val){return 'star-rating';}"),
						
						],
						'options'       => ['style' => "font-family: 'AmplesoftMedium';"],
					]) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"><!-- View Profile -->
        <a data-pjax="false" href="/doctors/profile?id=<?= $doctor->id ?>&office_id=<?= $office->id ?>"
           class="btn btn-tertiary btn-block"><?= Yii::t('app', 'View Profile') ?></a>
    </div>
    <div class="col-md-2"><!-- Set Appointment -->
        <a <?php if (!Yii::$app->user->isGuest){ ?>href="/patients/send-appointment?doctor_id=<?= $doctor->id ?>&office_id=<?= $office->id ?>" role="modal-remote"<?php } else { ?>href="/site/login?doctor_id=<?= $doctor->id ?>&office_id=<?= $office->id ?>"<?php } ?> class="btn btn-tertiary btn-block"><?= Yii::t('app', 'Set Appointment') ?></a>
    </div>

</div>
<div class="separator no-padding"></div>