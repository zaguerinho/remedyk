<?php
use yii\bootstrap\Html;

?>

<?= Yii::t('app', 'Dear') . ' ' . Html::encode($user->first_name) . ' ' . $user->last_name  ?>,
	
	<?= Yii::t('app', 'Your account have been disabled because you violated the Remedyk norms. ') ?>
    
    <?= Yii::t('app', 'If you want your account back, please contact us at the following email:') ?>

    <?= Yii::$app->params['supportEmail'] ?>
    
    <?= Yii::t('app', 'Best of the wishes') ?>,
    
   	<?= Yii::t('app', 'The Remedyk Team') ?>
    
    Copyright (C) <?= date('Y') ?> Remedyk