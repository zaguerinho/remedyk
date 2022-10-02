<?php
/* @var $this yii\web\View */
use yii\helpers\Json;
$doctor = Yii::$app->user->identity->doctor;

$this->title = Yii::t('app', 'Change your Membership')
?>
<div class="row">
	<?php foreach ($memberships as $membership): ?>
	<div class="col-md-4">
		<div class="panel">
			<div class="panel-header">
				<div class="row">
					<div class="col-xs-12">
						<h2 class="panel-header-text"><?= Json::decode($membership->name, true)[Yii::$app->language] ?></h2>
					</div>
				</div>
			</div>
			<div class="separator"></div>
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12">
						<h4 class="text-primary-1"><?= Json::decode($membership->description, true)[Yii::$app->language] ?></h4>
					</div>
				</div>
				<br><br>
				<div class="row">
					<div class="col-xs-12 text-right">
						<h4><?= Yii::t('app', 'Price:') ?> <?= number_format($membership->price, 2) ?> <?= $membership->currency->code ?></h4>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 text-right">
						<h4><?= Yii::t('app', 'Commission:') ?> <?= number_format(($membership->commission_percent)*100, 0)?>%</h4>
					</div>
				</div>
				
				<?php if ($membership->id == $doctor->membership->id): ?>
					<button type="button" class="btn btn-primary pull-right btn-xs"><i class="fa fa-check"></i><?= Yii::t('app', 'Current Membership') ?></button>
				<?php else: ?>
					<a href="/memberships/subscribe?id=<?= $membership->id ?>" class="btn btn-tertiary pull-right btn-xs"><?= Yii::t('app', 'Subscribe') ?></a>
				<?php endif; ?>
				
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>