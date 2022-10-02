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

    <?= Yii::t('app', 'Congratulations') . ' ' . Html::encode($user->first_name) . ' ' . $user->last_name . '!'  ?>,
	
	<?= Yii::t('app', 'You are almost there. ') ?>
    <?= Yii::t('app', 'Follow the link below to activate your account:') ?>

    <?= $activateLink ?>
    
    <?= Yii::t('app', 'Best of the wishes') ?>,
    
    <?= Yii::t('app', 'The Remedyk Team') ?>
    
    Copyright (C) <?= date('Y')  ?> Remedyk 

