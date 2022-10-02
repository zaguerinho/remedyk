<?php
namespace common\models;
use Yii;
use yii\base\Model;

class ConektaCardForm extends Model {
	public $name, $number, $cvc, $exp_month, $exp_year;
	
	public function rules(){
		return [
				[['name', 'number', 'cvc', 'exp_month', 'exp_year'], 'required', 'message' => Yii::t('app', 'This field is required')],
				[['number'], 'match', 'pattern' => '/^[0-9]{4,4}\-[0-9]{4,4}\-[0-9]{4,4}\-[0-9]{4,4}$|^[0-9]{16,16}$/', 'message' => Yii::t('app', 'Incorrect card number')],
				[['name'], 'string'],
				[['cvc'], 'match', 'pattern' => '/^[0-9]{3,4}$/', 'message' => Yii::t('app', 'Incorrect validation key (3 or 4 numbers)')],
				[['exp_year'], 'integer', 'min' => date('Y'), 'max' => 9999, 'message' => Yii::t('app', 'Must be a valid date in the future')],
				[['exp_month'], 'integer', 'min' => 1, 'max' => 12, 'message' => Yii::t('app', 'Month has to be between 1 and 12')],
				[['exp_month'], 
						'integer', 'min' => date('m'), 'message' => Yii::t('app', 'Must be a valid date in the future'), 
						'when' => function($model){ return $model->exp_year == date('Y'); }, 
						'whenClient' => 'function(attribute, value){ return $("#conektacardform-exp_year").val() <= '.date('Y').'; }'
				]
		];
	}
	
	public function attributeLabels(){
		return [
			'name' => Yii::t('app', 'Cardholder\'s name'),
			'number' => Yii::t('app', 'Card number'),
			'cvc' => Yii::t('app', 'CVC'),
			'exp_month' => Yii::t('app', 'Month'),
			'exp_year' => Yii::t('app', 'Year'),
		];
	}
}