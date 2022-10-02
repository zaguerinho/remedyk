<?php
	
use common\models\Doctor;
use common\models\User;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$admin = '';
if (User::getUserIdentity()->isStaff()){
	$admin = ' {access}';
}
	return [
		
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'user.first_name',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'user.last_name',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'license_number',
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'resume',
			'value'     => function($model){
				/* @var $model \common\models\Doctor */
				return substr($model->resume, 0, 50);
			},
		],
		[
			'class'     => '\kartik\grid\DataColumn',
			'attribute' => 'appointment_price',
		],
		[
			'class'		=> '\kartik\grid\DataColumn'	,
			'attribute' => 'status',
			'format'    => 'raw',
			'label'     => Yii::t('app', 'Status'),
			'value'		=> function($model, $key, $index, $widget){
					$doctor = Doctor::findOne($model['id']);
					$user = $doctor->user;
					$status = $user->status;
					switch($status){
						case User::STATUS_DELETED:
							return '<span class="text-bold text-rejected">' . Yii::t('app', 'Banned/Inactive') . '</span>';
						case User::STATUS_TO_CONFIRM:
							return '<span class="text-bold text-cancelled">' . Yii::t('app', 'Activation Pending') . '</span>';
						case User::STATUS_ACTIVE:
							return '<span class="text-bold text-confirmed">' . Yii::t('app', 'Active') . '</span>';
							
						
					}
			}
		],
		[
			'class'    => 'kartik\grid\ActionColumn',
			'dropdown' => false,
			'vAlign'   => 'middle',
			'template' => '{view}'.$admin.'{message}',
			'urlCreator' => function($action, $model, $key, $index) {
			return Url::to([$action,'id'=>$key]);
			},
			'buttons' =>
			[
					'access' => function($url, $model, $key) { 
						$doctor = Doctor::findOne($model['id']);
						$user = $doctor->user;
						return Html::a('<span class="text-primary-1 fa fa-key"></span>',
									['users/update', 'id' => $user->id],
									[
										'role' => 'modal-remote',
										'title'=>Yii::t('app', 'Enable/Disable'),
										'data-toggle'=>'tooltip',
									]);
					},
					'view' => function($url, $model, $key) {
						return Html::a('<span class="text-primary-1 fa fa-eye"></span>',
								['doctors/view', 'id' => $model['id']],
								[
										'data-pjax' => '0',
										'title'=>Yii::t('app', 'View'),
										'data-toggle' => 'tooltip'
										
										
								]);
					},
					'message'          => function($url, $model, $key){
					$doctor = Doctor::findOne($model['id']);
					$id = $doctor->user->id;
					return Html::a('<span class="text-primary-1 fa fa-comment"></span>',
							['#'],
							[
									//'role'=>'modal-remote',
									'title'       => Yii::t('app', 'Message'),
									//'data-toggle' => 'control-sidebar',
									'onclick' => 'return gotoChat('.$id.');'
							]);
					},
			]
		],
	
	];