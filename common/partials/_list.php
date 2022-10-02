<?php

use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var string $listLabel
 * @var string $listItemName
 * @var string[] $selectItems
 * @var string[] $listInitialItems 
 */ 

$js = <<<JS

function add_{$listItemName}(id, name){
	let items = $('#{$listItemName}_items');
	let item ='<div class="row">'+
		'<div class="col-xs-12">'+
			'<div class="form-group no-margin">'+
				'<div class="input-group">'+
					'<input type="text" name="{$listItemName}['+id+']" id="{$listItemName}_'+id+'" class="form-control" value="'+name+'" readonly>'+
					'<span class="input-group-btn"><button type="button" class="btn btn-secondary" onclick="remove_{$listItemName}(this)"><i class="fa fa-close"></i></button></span>'+
				'</div>'+
			'</div>'+
		'</div>'+
	 '</div>';	
	
	if ($('#{$listItemName}_'+id).val() === undefined && id != ''){ //Add it
		items.append(item);
	}	
}
function remove_{$listItemName}(sender){
	$(sender).parent().parent().parent().parent().parent().remove();
}

JS;

$this->registerJs($js, View::POS_END, 'list-'.$listItemName);

?>
<div class="row">
	<div class="col-xs-12">
		<h3><?= $listLabel ?></h3>
	</div>
</div>
<div class="row">
	<div class="col-xs-8">
		<div class="form-group no-margin">
			<?= Html::dropDownList($listItemName.'_list', null, $selectItems, ['class' => 'form-control', 'id' => $listItemName.'_list']); ?>
		</div>
	</div>
	
	<div class="col-xs-4 text-right">
		<div class="form-group no-margin">	
			<div class="input-group">
				<button type="button" id="add_<?= $listItemName ?>_button" class="btn btn-tertiary btn-xs" onclick="add_<?= $listItemName ?>($('#<?= $listItemName ?>_list').val(), $('#<?= $listItemName ?>_list option:selected').html())"><?= Yii::t('app', 'Add') ?></button>
			</div>
		</div>
	</div>
</div>	

<div id="<?= $listItemName ?>_items">
	<?php foreach ($listInitialItems as $id => $name): ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="form-group no-margin">
				<div class="input-group">
					<?= Html::textInput($listItemName.'['.$id.']', Json::decode($name, true)[Yii::$app->language], ['readonly' => true, 'class' => 'form-control', 'id' => $listItemName.'_'.$id]); ?>
					<span class="input-group-btn"><button type="button" class="btn btn-secondary" onclick="remove_<?= $listItemName ?>(this)"><i class="fa fa-close"></i></button></span>
				</div>
			</div>
		</div>
	</div>	
	<?php endforeach; ?>
</div>
