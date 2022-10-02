<?php
namespace doctors\models\search;

use common\models\Appointment;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\base\Model;

class NextAppointmentsSearch extends Appointment {
	
	public function scenarios(){
		return Model::scenarios();
	}
	
	public function search($params){
		//$params = [[confirmed] => false, [requested] => true, [accepted] => false, [rejected] => false, [cancelled] => false, [_pjax] => #pjax-appointments]
		$doctor = Yii::$app->user->identity->doctor;
		$query = Appointment::find();
		$query->where(['doctor_id' => $doctor->id]);
		//$query->andWhere(['!=', 'status', Appointment::STATUS_CLOSED]);
		$query->andWhere(['>=', 'date', date('Y-m-d')]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);
		
		if (isset($params['confirmed'])){ //Checkboxes changed
			$statuses = [];
			if ($params['confirmed'] == 'true')
				$statuses[] = Appointment::STATUS_CONFIRMED;
			if ($params['requested'] == 'true')
				$statuses[] = Appointment::STATUS_REQUESTED;
			if ($params['accepted'] == 'true'){
				$statuses[] = Appointment::STATUS_ACCEPTED;
				$statuses[] = Appointment::STATUS_CONFIRMED_BY_DOCTOR;
			}
			if ($params['rejected'] == 'true')
				$statuses[] = Appointment::STATUS_REJECTED;
			if ($params['cancelled'] == 'true')
				$statuses[] = Appointment::STATUS_CANCELLED;
			
			$query->andFilterWhere(['status' => $statuses]);
		}
		else {//Default configuration
			$statuses = [Appointment::STATUS_CONFIRMED];
			$query->andFilterWhere(['status' => $statuses]);
		}
		
		
		return $dataProvider;
	}
}