<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Comment */
?>
<div class="comment-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'datetime',
            'ban_reason',
            'text:ntext',
            'from_id',
            'target_id',
            'parent_comment_id',
            'approved_by',
            'banned_by',
        ],
    ]) ?>

</div>
