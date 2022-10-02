<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Doctor */
?>
<div class="doctor-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'license_number',
            'resume:ntext',
            'notes:ntext',
            'bank_data',
            'appointment_price',
            'appointment_anticipation',
            'user_id',
            'postal_address_id',
            'tax_data_id',
            'currency_id',
            'gender',
            'appointment_duration',
        ],
    ]) ?>

</div>
