<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Prescription */
?>
<div class="prescription-update">

    <?= $this->render('_form', [
        'model' => $model,
    	'prescriptionDetails' => $prescriptionDetails,
    ]) ?>

</div>
