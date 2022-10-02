<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Prescription */
?>
<div class="prescription-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'datetime',
            'notes:ntext',
            'is_active:boolean',
            'patient_id',
            'doctor_id',
            'appointment_id',
        ],
    ]) ?>

</div>
