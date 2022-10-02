<?php
	
	/* @var $this yii\web\View */
	/* @var $model common\models\Specialty */
?>
<div class="specialty-view">
    <div class="row">
        <div class="col-md-12">
            <div class="col-xs-12">
                <h4 class="no-margin"><?= Yii::t('app', 'View Details') ?></h4>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group no-margin">
                        <label class="control-label"><?= $model->attributeLabels()['name'] ?></label>
                        <div><?= $model->localized_name ?></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group no-margin">
                        <label class="control-label"><?= $model->attributeLabels()['specialist_name'] ?></label>
                        <div><?= $model->localized_specialist_name ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group no-margin">
                        <label class="control-label"><?= $model->attributeLabels()['is_active'] ?></label>
                        <div><?= $model->is_active ? Yii::t('app', 'Yes') : Yii::t('app', 'No') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
