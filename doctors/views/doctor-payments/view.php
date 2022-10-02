<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DoctorPayment */
?>
<div class="doctor-payment-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'invoice_url:url',
            'invoice_name',
            'paid_on',
            'status',
            'amount',
            'doctor_id',
            'user_id',
            'notes:ntext',
            'receipt_url:url',
            'receipt_name',
        ],
    ]) ?>

</div>
