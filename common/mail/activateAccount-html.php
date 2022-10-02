<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user common\models\User */

if ($params){
	$activateLink = Yii::$app->params['patientsDomain']. Url::to(['/site/activate-account', 'token' => $user->password_reset_token, 'doctor_id' => $params->doctor_id, 'office_id' => $params->office_id]);
}
else {
	$activateLink = Yii::$app->params['patientsDomain']. Url::to(['/site/activate-account', 'token' => $user->password_reset_token]);
}
?>
<div class="password-reset">
    <h3><?= Yii::t('app', 'Congratulations') . ' ' . Html::encode($user->first_name) . ' ' . $user->last_name . '!'  ?>,</h3>
	
	<p> <?= Yii::t('app', 'You are almost there. ') ?></p>
    <p> <?= Yii::t('app', 'Follow the link below to activate your account:') ?></p>

    <p><?= Html::a(Html::encode($activateLink), $activateLink) ?></p>
    
    <p><?= Yii::t('app', 'Best of the wishes') ?>,</p>
    <br>
    <p><?= Yii::t('app', 'The Remedyk Team') ?></p>
    
    <small>Copyright &copy; <?= date('Y') ?> Remedyk </small>
</div>
