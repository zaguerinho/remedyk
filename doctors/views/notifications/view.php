<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Notification */
?>
<div class="notification-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'text',
            'target_url:url',
            'datetime',
            'seen_at',
            'visited_at',
            'target_id',
            'fa_icon_class',
        ],
    ]) ?>

</div>
