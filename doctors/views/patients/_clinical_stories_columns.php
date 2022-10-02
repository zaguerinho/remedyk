<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\ClinicalStory;
use common\models\User;

return [
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'doctor.user.name',
    	'label' => Yii::t('app', 'Doctor')
    ],
	[
		'class'=>'\kartik\grid\DataColumn',
		'attribute'=>'clinicalStoryType.localized_name',
		'label' => Yii::t('app', 'Record Type')
	],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'appointment.procedure2doctor.specialty.localized_name',
    	'label' => Yii::t('app', 'Specialty')
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'registered_on',
    	'label' => Yii::t('app', 'Date'),
    	'format' =>['date', 'php:M-d-Y']
    ],
   
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
    	'template' => ' {message} {view}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons' =>
        [
        		'message' => function($url, $model, $key) {
        		$clinicalStory = ClinicalStory::findOne($model['id']);
        		$doctor = $clinicalStory->doctor;
        		$id = $doctor->id;
        		if ($doctor->user->id == User::getUserIdentity()->id){
        			return '';
        		}
        		return Html::a('<span class="text-primary-1 fa fa-comment"></span>',
        				['#'],
        				[
        						//'role'=>'modal-remote',
        						'title'       => Yii::t('app', 'Message'),
        						//'data-toggle' => 'control-sidebar',
        						'onclick' => 'return gotoChat('.$id.');'
        				]);
        		},
        		'view' => function($url, $model, $key) {
        		return Html::a('<span class="text-primary-1 fa fa-eye"></span>',
        				null,
        				[
        						'data-pjax' => false,
        						'title'=>Yii::t('app', 'View'),
        						'onclick' => new JsExpression("show_details($model->id)"),
        						'style' => 'cursor: pointer;',

        				]);
        		},
        ],
    ],

];   