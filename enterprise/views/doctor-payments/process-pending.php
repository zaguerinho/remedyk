<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use dmstr\widgets\Alert;
use yii\base\Widget;

?>
<?php $form = ActiveForm::begin(); ?>
<?= Alert::widget(); ?>
<div class="col-xs-6">
	<div class="form-group no-margin">

		<?= Html::fileInput('DoctorPayment[receiptBase64data]', null, [
				'id' => 'receiptBase64data',
				'value' => $model->receipt_name,
				'accept' => 'image/*,application/pdf',
				'onchange' => 'changeFile(this)'
		]) ?>
		
	    <input type="text" readonly="" class="form-control" placeholder="<?= Yii::t('app', 'Browse Files...') ?>" id="file_name" value="<?= $model->receipt_name ?>">
		<div class="help-block"></div>
	</div>
	
</div>
<div class="col-xs-6 text-right">
	<div class="form-group no-margin">	
		<div class="input-group">
			<button type="button" id="add_picture_button" class="btn btn-tertiary btn-xs" onclick="selectFile()"><?= Yii::t('app', 'Attach File') ?></button>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
<script>
function selectFile(){
	$('#receiptBase64data').click();
}

function changeFile(input){
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			// Put files in their places
			// The file comes in e.target.reult base64encoded
			// The name comes in e.target.fileName as string
			$('#file_name').val(e.target.fileName);
        };
        
        reader.fileName = input.files[0].name;
        reader.readAsDataURL(input.files[0]);
        console.log('Boo');
	}
}

</script>