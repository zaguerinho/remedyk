<?php
use yii\bootstrap\Html;

?>
<div class="password-reset">
    <h3><?= Yii::t('app', 'Dear') . ' ' . Html::encode($user->first_name) . ' ' . $user->last_name ?>,</h3>
	
	<p> <?= Yii::t('app', 'Your account has been activated. ') ?></p>
    <p> <?= Yii::t('app', 'Now you can enjoy the features Remedyk gives to you. Follow this link and login with your credentials to start now:') ?></p>

    <p><?= Html::a(Html::encode('Remedyk Site'), Yii::$app->params['patientsDomain']) ?></p>
    
    <p><?= Yii::t('app', 'Best of the wishes') ?>,</p>
    <br>
    <p><?= Yii::t('app', 'The Remedyk Team') ?></p>
    
    <small>Copyright &copy; <?= date('Y') ?> Remedyk </small>
</div>