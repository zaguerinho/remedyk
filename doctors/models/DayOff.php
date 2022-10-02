<?php
namespace doctors\models;

use Yii;
use yii\base\Model;

class DayOff extends Model {
	public $date;
	public $from;
	public $to;
	
	public function rules(){
		return [
			[['date', 'from', 'to'], 'safe'],
		];
	}
	
	public function attributeLabels(){
		return [
			'date' => Yii::t('app', 'Date'),
			'from' => Yii::t('app', 'From hour'),
			'to' => Yii::t('app', 'To hour'),
		];
	}
}