<?php
/* @var $this yii\web\View */


use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Subscription to membership ').Json::decode($membership->name, true)[Yii::$app->language];
?>
<div class="panel">
	<div class="panel panel-header">
		<div class="row">
			<div class="col-xs-12 panel-header-text">
				<span class="text-primary-2 text-bold"><?= Html::a( Yii::t('app', 'Start'), Url::home(), ['class'=> "text-primary-2 text-bold"] ) ?></span>
				<span class="text-primary-1 text-bold"> /</span> <span class="text-primary-2 text-bold"><?= Html::a( Yii::t('app', 'Change your Membership'), Url::to(['/memberships/index']), ['class'=> "text-primary-2 text-bold"] ) ?></span>
				<span class="text-primary-1 text-bold"> / <?= $this->title ?></span>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-4">
				<?= $this->render('@common/partials/_conekta', ['submitText' => Yii::t('app', 'Subscribe to ') . Json::decode($membership->name, true)[Yii::$app->language]]); ?>
			</div>
		</div>
	</div>
	<div class="panel-footer"><?= date('Y-m-d H:i:s') . date_default_timezone_get() ?></div>
</div>