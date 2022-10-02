<?php
$this->title = Yii::t('app', 'Activate your account');
$user = Yii::$app->user->identity;
?>

<div class="panel">
	<div class="panel-head">
		<div class="row">
			<div class="col-xs-12">
				<h2 class="panel-header-text"><?= Yii::t('app', 'Welcome to Remedyk!') ?></h2>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-12">
				<p><?= Yii::t('app', 'You are almost there, ') . $user->name ?> </p>
				<p><?= Yii::t('app', 'The Remedyk team will contact you soon to activate your account. Thank you!')  ?></p>	
			</div>
		</div>
	</div>
</div>