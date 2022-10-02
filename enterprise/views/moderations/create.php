<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Comment */

?>
<div class="comment-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
