<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Office */
?>
<div class="office-update">

    <?= $this->render('_form', [
        'model' => $model,
    	'address' => $address,
    ]) ?>

</div>
