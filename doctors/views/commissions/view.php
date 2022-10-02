<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Commission */
?>
<div class="commission-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'appointment_id',
            'amount',
            'percent',
            'paid_on',
            'status',
        ],
    ]) ?>

</div>
