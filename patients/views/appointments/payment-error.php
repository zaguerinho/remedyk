<?php
	$this->title = Yii::t('app', 'Error on payment');
?>

	<div class="panel">
        <div class="panel panel-header">
            <div class="row">
                <div class="col-xs-3">
                    <button onclick="window.location.href =  '/site/index';" class="btn btn-action"><i
                                class="fa fa-arrow-left"></i> <?= Yii::t('app', 'Back') ?></button>
                </div>
                <div class="col-xs-9 text-right">

                </div>
            </div>
        </div>

        <div class="panel-body">
        	<?= Yii::t('app', 'There was a problem trying to make your payment. Pleas try again.') ?>
        </div>
	</div>