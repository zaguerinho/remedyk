<?php
use yii\bootstrap\Html;

$this->title = Yii::t('app', 'Account Disabled');
$user = Yii::$app->user->identity;
?>

<div class="panel">
	<div class="panel-head">
		<div class="row">
			<div class="col-xs-12">
				<h2 class="panel-header-text"><?= Yii::t('app', 'Account Disabled') ?></h2>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-12">
				<h3><?= Yii::t('app', 'Dear Dr.') . ' ' . $user->first_name . ' ' . $user->last_name . ','?></h3>
				<p><?= Yii::t('app', 'You account have been disabled because you violated the Remedyk norms. ') ?> </p>
				<p> <?= Yii::t('app', 'If you want your account back, please contact us at the following email:') ?></p>

    			<p><?= Html::a(Html::encode(Yii::$app->params['supportEmail']), 'mailto:'.Yii::$app->params['supportEmail']) ?></p>
			</div>
		</div>
	</div>
</div>