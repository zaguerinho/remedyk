<?php

use yii\bootstrap\Html;

?>
	<?= Yii::t('app', 'Congratulations Dr.') . ' ' . Html::encode($user->first_name) . ' ' . $user->last_name . '!'  ?>,</h3>
	
	<?= Yii::t('app', 'You are almost there. ') ?></p>
    <?= Yii::t('app', 'Remedyk will now verify your data and activate your account.') ?>

    <?= Yii::t('app', 'Best of the wishes') ?>,
    
    <?= Yii::t('app', 'The Remedyk Team') ?>
    
    Copyright (C) <?= date('Y') ?> Remedyk
</div>
