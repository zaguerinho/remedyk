<?php
use yii\bootstrap\Html;
use yii\helpers\Url;

return [
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'datetime',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'notes',
    ],    
    [
        'class'=>'\kartik\grid\DataColumn',
    	'label' => Yii::t('app', 'Patient Name'),
        'attribute'=>'patient.user.name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
    	'label' => Yii::t('app', 'Appointment Date'),
        'attribute' => 'appointment.date',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'appointment_id',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
    	'template' => '{print}   {edit}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons' =>
        [
        		'print' => function($url, $model, $key) {
        		return Html::a('<span class="text-primary-1 fa fa-print"></span>',
        				['prescriptions/view-pdf', 'id' => $model['id']],
        				[			
        						'title'=>Yii::t('app', 'Print Preview'),
        						'target' => '_blank',
        						'data-pjax' => '0',
        				]);
        		},
        		
        		'edit' => function($url, $model, $key) {
        		return Html::a('<span class="text-primary-1 fa fa-pencil"></span>',
        				['prescriptions/update', 'id' => $model['id']],
        				[
        						'role'=>'modal-remote',
        						'title'=>Yii::t('app', 'Modify'),
        						'data-toggle'=>'tooltip',
        						
        				]);
        		},
        		],
        
    ],

];   