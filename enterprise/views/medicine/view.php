<?php
	
	/* @var $this yii\web\View */
	/* @var $model common\models\Medicine */
?>
<div class="medicine-view">
    <div class="row">
        <div class="col-md-12">
            <div class="col-xs-12">
                <h4 class="no-margin"><?= Yii::t('app', 'View Details') ?></h4>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group no-margin">
                        <label class="control-label"><?= $model->attributeLabels()['stores_equivalent_ids'] ?></label>
                        <div><?= $model->getLocalized_storesIds() ?></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group no-margin">
                        <label class="control-label"><?= $model->attributeLabels()['name'] ?></label>
                        <div><?= $model->getLocalized_name() ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group no-margin">
                        <label class="control-label"><?= $model->attributeLabels()['is_active'] ?></label>
                        <div><?= $model->is_active ? Yii::t('app', 'Yes') : Yii::t('app', 'No') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
