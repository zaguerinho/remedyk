<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Appointment */
?>
<div class="appointment-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'is_procedure:boolean',
            'date',
            'is_waiting',
            'is_done:boolean',
            'notes:ntext',
            'is_active:boolean',
            'price',
            'status',
            'confirmation_datetime',
            'cancel_datetime',
            'is_quot:boolean',
            'patient_id',
            'doctor_id',
            'office_id',
            'operating_room_id',
            'procedure2doctor_id',
            'currency_id',
            'start_time',
            'end_time',
            'changed_by',
            'created_at',
        ],
    ]) ?>

</div>
