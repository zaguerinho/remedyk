<?php


/* @var $this yii\web\View */
/* @var $model common\models\Office */

?>
<div class="office-create">
    <?= $this->render('_form', [
        'model' => $model,
    	'address' => $address,
    ]) ?>
</div>
