<?php
use johnitvn\ajaxcrud\CrudAsset;
use kartik\grid\GridView;
CrudAsset::register($this);
?>

<div class="row">
	<div class="col-xs-12">
	
		<div id="officesAjaxCrudDatatable">
	        <?= GridView::widget([
	            'id'=>'offices-crud-datatable',
	            'dataProvider' => $dataProvider,
	          	//'filterModel' => $searchModel,
	            'pjax'=>true,
	            'columns' => require(__DIR__.'/_columns.php'),
	            'toolbar'=> [
	                ['content'=> false
	                ],
	            ],          
	            'bordered' => false,
	        	'striped' => false,
	        	'hover' => true,
	            'condensed' => false,
	            'responsive' => true,          
	            'panel' => [
	                'type' => false, 
	                'heading' => false,
	                'before'=> false,
	                'after'=> false,
	            	'footer'=>false
	            ]
	        ])?>
		</div>
		    
    </div>
</div>