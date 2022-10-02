<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>
<div class="password-reset">
    <h3><?= Yii::t('app', 'Dear') . ' ' . Html::encode($user->first_name) . ' ' . $user->last_name . ':'  ?></h3>
	
	<p> <?= Yii::t('app', 'Your Remedyk membership was renewed successfully on ').date('Y-m-d') ?></p>
    <p> <?= Yii::t('app', '') ?></p>
    
    <p><?= Yii::t('app', 'Best of the wishes') ?>,</p>
    <br>
    <p><?= Yii::t('app', 'The Remedyk Team') ?></p>
    
    <small>Copyright &copy; <?= date('Y') ?> Remedyk </small>
</div>
