<?php

use yii\bootstrap\Html;

?>
<div class="password-reset">
    <h3><?= Yii::t('app', 'Congratulations Dr.') . ' ' . Html::encode($user->first_name) . ' ' . $user->last_name . '!'  ?>,</h3>
	
	<p> <?= Yii::t('app', 'You are almost there. ') ?></p>
    <p> <?= Yii::t('app', 'Remedyk will now verify your data and activate your account.') ?></p>

   
    
    <p><?= Yii::t('app', 'Best of the wishes') ?>,</p>
    <br>
    <p><?= Yii::t('app', 'The Remedyk Team') ?></p>
    
    <small>Copyright &copy; <?= date('Y') ?> Remedyk </small>
</div>
