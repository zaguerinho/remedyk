<?php

/* @var $this yii\web\View */
/* @var $model common\models\OperatingRoom */

?>
<div class="operating-room-create">
    <?= $this->render('_form', [
        'model' => $model,
    	'address' => $address,
    ]) ?>
</div>
