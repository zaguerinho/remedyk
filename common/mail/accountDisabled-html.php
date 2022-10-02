<?php
use yii\bootstrap\Html;

?>
<div class="password-reset">
	<h3><?= Yii::t('app', 'Dear') . ' ' . Html::encode($user->first_name) . ' ' . $user->last_name  ?>,</h3>
	
	<p> <?= Yii::t('app', 'Your account have been disabled because you violated the Remedyk norms. ') ?></p>
    <p> <?= Yii::t('app', 'If you want your account back, please contact us at the following email:') ?></p>

    <p><?= Html::a(Html::encode(Yii::$app->params['supportEmail']), 'mailto:'.Yii::$app->params['supportEmail']) ?></p>
    
    <p><?= Yii::t('app', 'Best of the wishes') ?>,</p>
    <br>
    <p><?= Yii::t('app', 'The Remedyk Team') ?></p>
    
    <small>Copyright &copy; <?= date('Y') ?> Remedyk </small>
</div>