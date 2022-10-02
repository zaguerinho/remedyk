<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use common\models\Comment;

return [
	[
		'class'=>'\kartik\grid\DataColumn',
		'label' => Yii::t('app', 'From'),
		'attribute'=>'from.name',
	],
	[
		'class'=>'\kartik\grid\DataColumn',
		'label' => Yii::t('app', 'To'),
		'attribute'=>'target.name',
	],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'datetime',
    	'format' =>  ['date', 'php:M-d-Y h:i A'],
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
    	'label' => Yii::t('app', 'Comment Preview'),
        'attribute'=>'textPreview',
    ],
    [
    		'class'=>'\kartik\grid\DataColumn',
    		'attribute'=>'status',
    		'format'    => 'raw',
    		'label'     => Yii::t('app', 'Status'),
    		'value'     => function($model, $key, $index, $widget){
	    		switch ($model['status']){
	    			case Comment::STATUs_APPROVED:
	    				return '<span class="text-bold text-accepted">' . Yii::t('app', 'Accepted') . '</span>';
	    			case Comment::STATUS_BANNED:
	    				return '<span class="text-bold text-rejected">' . Yii::t('app', 'Banned') . '</span>';
	    			case Comment::STATUS_PENDING:
	    				return '<span class="text-bold text-requested">' . Yii::t('app', 'Pending') . '</span>';return '<span class="text-bold text-requested">' . Yii::t('app', 'Pending') . '</span>';
	    		}
    		}
    ],
    [
    		'class'=>'\kartik\grid\DataColumn',
    		'attribute'=>'ban_reason',
    ],
    [
    		'class' => 'kartik\grid\ActionColumn',
    		'dropdown' => false,
    		'vAlign'=>'middle',
    		'template'   => '{edit} {message}',
    		'urlCreator' => function($action, $model, $key, $index) {
    		return Url::to([$action,'id'=>$key]);
    		},
    		'buttons'    => [
    				'edit'    => function($url, $model, $key){
    					return Html::a('<span class="text-primary-1 fa fa-pencil"></span>',
    							['moderations/update', 'id' => $model['id']],
    							[
    									'role'        => 'modal-remote',
    									'title'       => Yii::t('app', 'Moderate'),
    									'data-toggle' => 'tooltip',
    									
    							]);
    				},
    				'message'    => function($url, $model, $key){	
    					$comment = Comment::findOne($model['id']);
    					$id = $comment->from->id;
	    				return Html::a('<span class="text-primary-1 fa fa-comment"></span>',
	    						['#'],
	    						[
	    								//'role'=>'modal-remote',
	    								'title'       => Yii::t('app', 'Message to Comment Author'),
	    								'onclick' => 'return gotoChat('.$id.');'
	    						]);
    				},		
    		],
    ],

];   