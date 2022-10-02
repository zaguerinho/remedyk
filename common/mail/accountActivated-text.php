<?php
use yii\bootstrap\Html;

?>

    <?= Yii::t('app', 'Dear') . ' ' . Html::encode($user->first_name) . ' ' . $user->last_name ?>,
	
	<?= Yii::t('app', 'Your account has been activated. ') ?></p>
    <?= Yii::t('app', 'Now you can enjoy the features Remedyk gives to you. Follow this link and login with your credentials to start now:') ?>

    <?= Yii::$app->params['patientsDomain'] ?>
    
    <?= Yii::t('app', 'Best of the wishes') ?>,
    
    <?= Yii::t('app', 'The Remedyk Team') ?>
    
    Copyright (C) <?= date('Y') ?> Remedyk
</div>