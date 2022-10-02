<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model common\models\OperatingRoom */
?>
<div class="operating-room-view">
 	<div class="row">
 		<div class="col-xs-12">
 			<label class="control-label text-primary-1"><?= $model->attributeLabels()['name'] ?></label>
 			<div class="control-group"><?= $model->name ?></div>
 		</div>
 	</div>
    
	<div class="row">
 		<div class="col-xs-12">
 			<label class="control-label text-primary-1"><?= $model->attributeLabels()['address_id'] ?></label>
 			<div class="control-group"><?= Html::a($address->toString(),  $address->url_gmaps, ['class' => 'remedyk-link', 'target' => '_blank']) ?></div>
 		</div>
 	</div>

</div>
