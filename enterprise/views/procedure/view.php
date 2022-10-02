<?php
	
	use yii\widgets\DetailView;
	
	/* @var $this yii\web\View */
	/* @var $model common\models\Procedure */
?>
<div class="procedure-view">
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
                <div class="col-md-4">
                    <div class="form-group no-margin">
                        <label class="control-label"><?= $model->attributeLabels()['is_treatment'] ?></label>
                        <div><?= $model->is_treatment ? Yii::t('app', 'Yes') : Yii::t('app', 'No') ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group no-margin">
                        <label class="control-label"><?= $model->attributeLabels()['is_surgery'] ?></label>
                        <div><?= $model->is_surgery ? Yii::t('app', 'Yes') : Yii::t('app', 'No') ?></div>
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
