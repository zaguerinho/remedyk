<?php

/* @var $this yii\web\View */

use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use common\models\ConektaCardForm;
use yii\widgets\MaskedInput;
use yii\web\YiiAsset;

$conektaPublicKey = Yii::$app->params['conektaPublicKey'];
if (!Yii::$app->request->isAjax){ 
	$this->registerJsFile('https://cdn.conekta.io/js/latest/conekta.js', ['position' => View::POS_HEAD]);
	$conektaJs = <<<JS
	Conekta.setPublicKey('{$conektaPublicKey}');
	
	  var conektaSuccessResponseHandler = function(token) {
	    var form = $("#card-form");
	    //Inserta el token_id en la forma para que se envíe al servidor
	     form.append($('<input type="hidden" name="conektaTokenId" id="conektaTokenId">').val(token.id));
	    form.get(0).submit(); //Hace submit
	  };
	  var conektaErrorResponseHandler = function(response) {
	    var form = $("#card-form");
	    form.find(".card-errors").text(response.message_to_purchaser);
	    form.find("button").prop("disabled", false);
	  };
	
	  //jQuery para que genere el token después de dar click en submit
	  $(function () {
	    $("#card-form").submit(function(event) {
	      var form = $(this);
	      // Previene hacer submit más de una vez
	      form.find("button").prop("disabled", true);
	      Conekta.token.create(form, conektaSuccessResponseHandler, conektaErrorResponseHandler);
	      return false;
	    });
	  });
JS;
	
	$this->registerJs($conektaJs, View::POS_END );
}
$model = new ConektaCardForm ();
?>
<?php $form = ActiveForm::begin(['id' => 'card-form']); ?>
	<div class="row">
		<div class="col-xs-12" style="border: 1px solid #707EA0;">
			<h4><?= Yii::t('app', 'Enter your billing information below') ?></h4>
			<span class="card-errors"></span>
			<div class="row">
				<div class="col-xs-12">
			  		<?= $form->field($model, 'name', ['options' => ['class' => 'form-group no-margin']])->textInput(['name' => false, 'data-conekta' => 'card[name]']) ?>
			  	</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
			  		<?=$form->field ( $model, 'number', [ 'options' => [ 'class' => 'form-group no-margin' ] ] )->widget ( MaskedInput::className (), [ 'mask' => '9999-9999-9999-9999','clientOptions' => [ 'removeMaskOnSubmit' => true ],'options' => [ 'name' => false,'data-conekta' => 'card[number]','class' => 'form-control' ] ] )?>
			  	</div>
			</div>
			<div class="row">
				<div class="col-xs-6">
			  		<?= $form->field($model, 'cvc', ['options' => ['class' => 'form-group no-margin']])->textInput(['name' => false, 'data-conekta' => 'card[cvc]']) ?>
			  	</div>
			</div>
			<div class="row">
			
				<div class="col-xs-4">
					<label class="control-label"><?= Yii::t('app', 'Expiration') ?></label>
			  		<?= $form->field($model, 'exp_month', ['options' => ['class' => 'form-group no-margin']])->textInput(['name' => false, 'data-conekta' => 'card[exp_month]']) ?>
			  	</div>
				<div class="col-xs-8">
					<label class="control-label">&nbsp;</label>
			  		<?= $form->field($model, 'exp_year', ['options' => ['class' => 'form-group no-margin']])->textInput(['name' => false, 'data-conekta' => 'card[exp_year]']) ?>
			  	</div>
			</div>
			
			<?php if (isset($submitText)): ?>
			<div class="row">
				<div class="col-xs-12 text-right">
			  		<?= Html::submitButton($submitText, ['class' => 'btn btn-primary btn-xs']) ?>
			  	</div>
			</div>
			<?php endif; ?>
			
		</div>
	</div>	
<?php ActiveForm::end(); ?>
<?php if (Yii::$app->request->isAjax){ ?>
<script>
$.getScript('https://cdn.conekta.io/js/latest/conekta.js', function() {
    //script is loaded and executed put your dependent JS here
	Conekta.setPublicKey('<?= $conektaPublicKey ?>');

	var conektaSuccessResponseHandler = function(token) {
	  var form = $("#card-form");
	  //Inserta el token_id en la forma para que se envíe al servidor
	  form.append($('<input type="hidden" name="conektaTokenId" id="conektaTokenId">').val(token.id));
	  form.get(0).submit(); //Hace submit
	};
	var conektaErrorResponseHandler = function(response) {
	  var form = $("#card-form");
	  form.find(".card-errors").text(response.message_to_purchaser);
	  form.find("button").prop("disabled", false);
	};

	//jQuery para que genere el token después de dar click en submit
	$(function () {
	  $("#card-form").off().on('submit', function(event) {
		  var form = $(this);
		    // Previene hacer submit más de una vez
		    form.find("button").prop("disabled", true);
		    Conekta.token.create(form, conektaSuccessResponseHandler, conektaErrorResponseHandler);
		    return false;
	  });
	});
});	
</script>
<?php }
