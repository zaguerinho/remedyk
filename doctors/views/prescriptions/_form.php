<?php
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use common\models\Medicine;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model common\models\Prescription */
/* @var $form yii\widgets\ActiveForm */

$doctor = Yii::$app->user->identity->doctor;
$myPatients = ArrayHelper::map($doctor->patients, 'id', function($elem){ return $elem->user->first_name . ' ' . $elem->user->last_name; });
$readOnly = $model->patient_id ? true : false;

$medicineArray = ['' => Yii::t('app', '- Select Medicine -')] + ArrayHelper::map(Medicine::find()->all(), 'id', function($elem){ return Json::decode($elem->name, true)[Yii::$app->language]; });
?>

<div class="prescription-form">

    <?php $form = ActiveForm::begin(['id' => 'prescription-form']); ?>
    	<div class="row">
    		<div class="col-md-7">
    			<?= $form->field($model, 'patient_id', ['options' => ['class' => 'form-group no-margin']])->dropDownList($myPatients, ['disabled' => $readOnly]) ?>
    		</div>
    		<div class="col-md-5">
    			<?= $form->field($model, 'datetime', ['options' => ['class' => 'form-group no-margin']])->widget(DateTimePicker::className(),[
				'options' => ['id' => 'day_off_date'],
				'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
				'removeButton' => false,
				'pluginOptions' => [
						'autoclose'=>true,
						'format' => 'mm/d/yyyy HH:ii p'
				]
				
		])?>
    		</div>
    	</div>
    	<div class="row">
    		<div class="col-md-12">
    			<?= $form->field($model, 'notes', ['options' => ['class' => 'form-group no-margin']])->textarea() ?>
    		</div>
    	</div>
<?= $form->field($model, 'is_active')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'doctor_id')->hiddenInput()->label(false) ?>

<?= $form->field($model, 'appointment_id')->hiddenInput()->label(false) ?>   	
		<?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                //'limit' => 4, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $prescriptionDetails[0],
                'formId' => 'prescription-form',
                'formFields' => [
                    'medicine_id',
                    'grammage',
                    'frequency',
                    'lapse',
                	'quantity',
                    'notes',
                    'prescription_id',
                ],
            ]); ?>
            
    	<div class="row">
    		<div class="col-xs-6"><h4><?= Yii::t('app', 'Medicines') ?></h4></div>
    		<div class="col-xs-6 text-right">
    			<button type="button" class="btn btn-xs btn-action add-item"><span><i class="fa fa-plus"></i></span> <?= Yii::t('app', 'Add Medicine') ?></button>
    		</div>
    	</div>
		<div class="separator"></div>
		
		
    	
    	<div class="container-items row">
    		<?php foreach ($prescriptionDetails as $i => $prescriptionDetail) : ?>
    			<div class="item well col-md-12">
    				<span class="close-icon remove-item"><i class="fa fa-times"></i></span>
    				 <?php
                     	// necessary for update action.
    				 	if (! $prescriptionDetail->isNewRecord) {
    				 		echo Html::activeHiddenInput($prescriptionDetail, "[{$i}]id", ['options' => ['class' => 'form-group no-margin']]);
                        }
                     ?>
    				<div class="row">
    					<div class="col-md-8">
    						<?= $form->field($prescriptionDetail, "[{$i}]medicine_id", ['options' => ['class' => 'form-group no-margin']])->dropDownList($medicineArray); ?>
    					</div>
    					<div class="col-md-4">
    						<?= $form->field($prescriptionDetail, "[{$i}]grammage", ['options' => ['class' => 'form-group no-margin']]) ?>
    					</div>
    				</div>
    				
    				<div class="row">
    					<div class="col-md-6">
    						<?= $form->field($prescriptionDetail, "[{$i}]frequency", ['options' => ['class' => 'form-group no-margin']]) ?>
    					</div>
    					<div class="col-md-6">
    						<?= $form->field($prescriptionDetail, "[{$i}]lapse", ['options' => ['class' => 'form-group no-margin']]) ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-md-12">
    						<?= $form->field($prescriptionDetail, "[{$i}]quantity", ['options' => ['class' => 'form-group no-margin']])  ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-md-12">
    						<?= $form->field($prescriptionDetail, "[{$i}]notes")->textarea() ?>
    					</div>    					
    				</div>
    			</div>
    		<?php endforeach; ?>
    	</div>
		<?php DynamicFormWidget::end() ?>
    

    

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
