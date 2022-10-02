<?php

use common\models\Appointment;
use yii\bootstrap\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Appointment */

switch ($model->status){
	case Appointment::STATUS_REQUESTED:
		$statusText = '<span class="text-bold text-requested">'.Yii::t('app', 'Requested').'</span>';
		break;
	case Appointment::STATUS_ACCEPTED:
		$statusText = '<span class="text-bold text-accepted">'.Yii::t('app', 'Accepted').'</span>';
		break;
	case Appointment::STATUS_CONFIRMED:
		$statusText = '<span class="text-bold text-confirmed">'.Yii::t('app', 'Confirmed').'</span>';
		break;
	case Appointment::STATUS_CONFIRMED_BY_DOCTOR:
		$statusText = '<span class="text-bold text-accepted">'.Yii::t('app', 'Confirmed by Doctor').'</span>';
		break;
	case Appointment::STATUS_REJECTED:
		$statusText = '<span class="text-bold text-rejected">'.Yii::t('app', 'Rejected').'</span>';
		break;
	case Appointment::STATUS_CANCELLED:
		$statusText = '<span class="text-bold text-cancelled">'.Yii::t('app', 'Cancelled').'</span>';
		break;
	case Appointment::STATUS_OPEN:
		$statusText = '<span class="text-bold text-open">'.Yii::t('app', 'Open').'</span>';
		break;
	case Appointment::STATUS_CLOSED:
		$statusText = '<span class="text-bold text-closed">'.Yii::t('app', 'Done').'</span>';
		break;
	default:
		$statusText = '<span class="text-bold">'.$model->status.'</span>';
		break;
}


?>
<div class="appointment-view">
 	<div class="row">
 		<div class="col-md-6">
 			<label class="control-label text-primary-1 text-bold"><?= Yii::t('app', 'Patient') ?></label>
 			<div><?= $model->patient->user->name; ?></div>
 		</div>
 		<div class="col-md-6">
 			<label class="control-label text-primary-1 text-bold"><?= Yii::t('app', 'Price') ?></label>
 			<div><?= number_format($model->price, 2) . ' '.  $model->currency->code; ?></div>
 		</div>
 	</div>
    <div class="row">
    	<div class="col-md-6">
    		<label class="control-label text-primary-1 text-bold"><?= Yii::t('app', 'Date') ?></label>
 			<div><?= date('m/d/Y', strtotime($model->date)); ?></div>
    	</div>
    	<div class="col-md-6">
    		<label class="control-label text-primary-1 text-bold"><?= Yii::t('app', 'Time') ?></label>
 			<div><?= date('h:i A', strtotime($model->start_time)) . ' - ' . date('h:i A', strtotime($model->end_time)); ?></div>
    	</div>
    </div>
    
    <?php if ($model->office) { $location_text = $model->office->title; $location_url = $model->office->address->url_gmaps; }?> 
    <?php if ($model->operatingRoom) { $location_text = $model->operatingRoom->name; $location_url = $model->operatingRoom->address->url_gmaps; }?>
    <?php if (isset($location_text) && isset($location_url)): ?>
    <div class="row">
    	<div class="col-xs-12">
    		<label class="control-label text-primary-1 text-bold"><?= Yii::t('app', 'Location') ?></label>
    		<div><?= Html::a($location_text, $location_url, ['class' => 'remedyk-link', 'target' => '_blank']) ?></div> 
    	</div>
    </div>
    <?php endif; ?>
    
    <?php if ($model->procedure2doctor): ?>
	<div class="row">
    	<div class="col-xs-12">
    		<label class="control-label text-primary-1 text-bold"><?= Yii::t('app', 'Procedure') ?></label>
    		<div><?= $model->procedure2doctor->procedure->localized_name ?></div> 
    	</div>
    </div>
    <?php endif; ?>
    
    <div class="row">
    	<div class="col-xs-12">
    		<label class="control-label text-primary-1 text-bold"><?= Yii::t('app', 'Appointment Status') ?></label>
    		<div><?= $statusText ?></div>
    	</div>
    </div>
</div>
