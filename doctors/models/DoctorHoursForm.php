<?php
namespace doctors\models;

use Yii;
use yii\base\Model;
use common\models\DoctorWorkingHour;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class DoctorHoursForm extends Model {
	const WEEK_DAYS = [
			'Sun', 
			'Mon', 
			'Tue', 
			'Wed', 
			'Thu', 
			'Fri', 
			'Sat'
	];
	
	
	public $first_from, $first_to, $second_from, $second_to, $third_from, $third_to, $closed;
	
	public function rules(){
		return [
			[['first_from', 'first_to', 'second_from', 'second_to', 'third_from', 'third_to', 'closed'], 'each', 'rule' => ['each', 'rule' => ['string']]],
			[['first_from', 'first_to', 'second_from', 'second_to', 'third_from', 'third_to', 'closed'], 'safe'],
		];
	}
	public function attributeLabels(){
		return [
			'first_from' => Yii::t('app', 'First Shift'),
			'first_to' => '',
			'second_from' => Yii::t('app', 'Second Shift'),
			'second_to' => '',
			'third_from' => Yii::t('app', 'Third Shift'),
			'third_to' => '',
			'closed' => Yii::t('app', 'Closed'),
		];
	}
	
	/**
	 * Loads data from the doctor's working hours
	 * 
	 * @param \common\models\Doctor $doctor
	 */
	public function loadFromDoctor($doctor){
		$this->first_from = 
		$this->second_from =
		$this->third_from = 
		$this->first_to = 
		$this->second_to = 
		$this->third_to = 
		$this->closed = []; 
		$workedDays = []; 
		foreach ($doctor->offices as $office){
			$this->first_from[$office->id] =
			$this->second_from[$office->id] =
			$this->third_from[$office->id] =
			$this->first_to[$office->id] =
			$this->second_to[$office->id] =
			$this->third_to[$office->id] =
			$this->closed[$office->id] = ['', '', '', '', '', '', ''];
			$workedDays[$office->id] = [false, false, false, false, false, false, false];
		}
		
		$doctor_id = $doctor->id;
		$doctorWorkingHours = DoctorWorkingHour::find()->where([
				'doctor_id' => $doctor_id,
				'is_active' => true,
				'is_enabled' => true,
				'is_working_hour' => true,
				'month' => null,
				'month_day' => null,
				'office_id' => array_keys(ArrayHelper::map($doctor->offices, 'id', 'id')),
				'year' => null
				
		])->andWhere(['not', ['shift' => null]])->orderBy(['week_day' => 'asc', 'start_time' => 'asc'])->all();
		if ($doctorWorkingHours){
			foreach($doctorWorkingHours as $time){
				/* @var \common\models\DoctorWorkingHour $time */
				if ($time->shift == 1){
					$this->first_from[$time->office_id][$time->week_day] = date('h:i A', strtotime($time->start_time));
					$this->first_to[$time->office_id][$time->week_day] = date('h:i A', strtotime($time->end_time));
				}
				elseif ($time->shift == 2){
					$this->second_from[$time->office_id][$time->week_day] = date('h:i A', strtotime($time->start_time));
					$this->second_to[$time->office_id][$time->week_day] = date('h:i A', strtotime($time->end_time));
				}
				elseif ($time->shift == 3){
					$this->third_from[$time->office_id][$time->week_day] = date('h:i A', strtotime($time->start_time));
					$this->third_to[$time->office_id][$time->week_day] = date('h:i A', strtotime($time->end_time));
				}
				
				$workedDays[$time->office_id][$time->week_day] = true; 
				
			}
		}
		foreach ($workedDays as $office_id => $workedDay){
			foreach ($workedDay as $index => $value){
				$closedDay = DoctorWorkingHour::find()->where([
						'doctor_id' => $doctor_id,
						'is_active' => true,
						'is_enabled' => true,
						'is_working_hour' => false,
						'month' => null,
						'month_day' => null,
						'office_id' => $office_id,
						'year' => null,
						'week_day' => $index,
						'start_time' => '00:00:00',
						'end_time' => '23:59:59'
				])->orderBy(['start_time' => 'asc'])->one();
				
				if ($closedDay) {
					$this->closed[$office_id][$index] = true;
				}
				else {
					$this->closed[$office_id][$index] = !$value;
				}
			}
		}
	}
	
	/**
	 * Saves the current data to the doctor's working hours
	 * 
	 * @param \common\models\Doctor $doctor
	 */
	public function saveToDoctor($doctor){
		$doctor_id = $doctor->id;
		$transaction = Yii::$app->db->beginTransaction();
		$flag = true;
		try {
			foreach ($doctor->offices as $office){
				for ($i = 0; $i < 7 ; $i++){
					
					
					
					// First Shift
					$firstShift = DoctorWorkingHour::find()->where([
							'doctor_id' => $doctor_id,
							'is_active' => true,
							'is_enabled' => true,
							'is_working_hour' => true,
							'month' => null,
							'month_day' => null,
							'office_id' => $office->id,
							'year' => null,
							'week_day' => $i,
							'shift' => 1
					])->orderBy(['start_time' => 'asc'])->one();
					
					if ($firstShift && (!$this->first_from[$office->id][$i] || !$this->first_to[$office->id][$i])){
						$deleted = $secondShift->delete();
						$flag = $flag && ($deleted != false);
					}
					elseif ($firstShift && $this->first_from[$office->id][$i] && $this->first_to[$office->id][$i]){
						$firstShift->start_time = date('H:i:s', strtotime($this->first_from[$office->id][$i]));
						$firstShift->end_time =date('H:i:s', strtotime( $this->first_to[$office->id][$i]));
						$flag = $flag && $firstShift->save();
					}
					elseif (!$firstShift && $this->first_from[$office->id][$i] && $this->first_to[$office->id][$i]) {
						$firstShift = new DoctorWorkingHour([
							'doctor_id' => $doctor_id,
							'is_active' => true,
							'is_enabled' => true,
							'is_working_hour' => true,
							'week_day' => $i,
							'office_id' => $office->id,
							'start_time' => date('H:i:s', strtotime($this->first_from[$office->id][$i])),
							'end_time' => date('H:i:s', strtotime($this->first_to[$office->id][$i])),
							'shift' => 1
						]);
						$flag = $flag && $firstShift->save();
					}
					
					// Second Shift
					$secondShift = DoctorWorkingHour::find()->where([
							'doctor_id' => $doctor_id,
							'is_active' => true,
							'is_enabled' => true,
							'is_working_hour' => true,
							'month' => null,
							'month_day' => null,
							'office_id' => $office->id,
							'year' => null,
							'week_day' => $i,
							'shift' => 2
					])->orderBy(['start_time' => 'asc'])->one();
					
					if ($secondShift && (!$this->second_from[$office->id][$i] || !$this->second_to[$office->id][$i])){
						$deleted = $secondShift->delete();
						$flag = $flag && ($deleted != false);
					}
					elseif ($secondShift && $this->second_from[$office->id][$i] && $this->second_to[$office->id][$i]){
						$secondShift->start_time = date('H:i:s', strtotime($this->second_from[$office->id][$i]));
						$secondShift->end_time = date('H:i:s', strtotime($this->second_to[$office->id][$i]));
						$flag = $flag && $secondShift->save();
					}
					elseif (!$secondShift && $this->second_from[$office->id][$i] && $this->second_to[$office->id][$i]) {
						$secondShift = new DoctorWorkingHour([
								'doctor_id' => $doctor_id,
								'is_active' => true,
								'is_enabled' => true,
								'is_working_hour' => true,
								'week_day' => $i,
								'office_id' => $office->id,
								'start_time' => date('H:i:s', strtotime($this->second_from[$office->id][$i])),
								'end_time' => date('H:i:s', strtotime($this->second_to[$office->id][$i])),
								'shift' => 2
						]);
						$flag = $flag && $secondShift->save();
					}
					
					// Third Shift
					$thirdShift = DoctorWorkingHour::find()->where([
							'doctor_id' => $doctor_id,
							'is_active' => true,
							'is_enabled' => true,
							'is_working_hour' => true,
							'month' => null,
							'month_day' => null,
							'office_id' => $office->id,
							'year' => null,
							'week_day' => $i,
							'shift' => 3
					])->orderBy(['start_time' => 'asc'])->one();
					if ($thirdShift && (!$this->third_from[$office->id][$i] || !$this->third_to[$office->id][$i])){
						$deleted = $thirdShift->delete();
						$flag = $flag && ($deleted != false);
					}
					elseif ($thirdShift && $this->third_from[$office->id][$i] && $this->third_to[$office->id][$i]){
						$thirdShift->start_time = date('H:i:s', strtotime($this->third_from[$office->id][$i]));
						$thirdShift->end_time = date('H:i:s', strtotime($this->third_to[$office->id][$i]));
						$flag = $flag && $thirdShift->save();
					}
					elseif (!$thirdShift && $this->third_from[$office->id][$i] && $this->third_to[$office->id][$i]) {
						$thirdShift = new DoctorWorkingHour([
								'doctor_id' => $doctor_id,
								'is_active' => true,
								'is_enabled' => true,
								'is_working_hour' => true,
								'week_day' => $i,
								'office_id' => $office->id,
								'start_time' => date('H:i:s', strtotime($this->third_from[$office->id][$i])),
								'end_time' => date('H:i:s', strtotime($this->third_to[$office->id][$i])),
								'shift' => 3
						]);
						$flag = $flag && $thirdShift->save();
					}
					
					// Closed Day
					$closedDay = DoctorWorkingHour::find()->where([
							'doctor_id' => $doctor_id,
							'is_active' => true,
							'is_enabled' => true,
							'is_working_hour' => false,
							'month' => null,
							'month_day' => null,
							'office_id' => $office->id,
							'year' => null,
							'week_day' => $i,
							'start_time' => '00:00:00',
							'end_time' => '23:59:59'
					])->orderBy(['start_time' => 'asc'])->one();
					
					if ($closedDay && !$this->closed[$office->id][$i]){
						$deleted = $closedDay->delete();
						$flag = $flag && ($deleted != false);
					}
					elseif (!$closedDay && $this->closed[$office->id][$i]) {
						$closedDay = new DoctorWorkingHour([
							'doctor_id' => $doctor_id,
							'is_active' => true,
							'is_enabled' => true,
							'is_working_hour' => false,
							'month' => null,
							'month_day' => null,
							'office_id' => $office->id,
							'year' => null,
							'week_day' => $i,
							'start_time' => '00:00:00',
							'end_time' => '23:59:59'
						]);
						$flag = $flag && $closedDay->save();
					}
					
				}
			}
			if ($flag){
				$transaction->commit();
				return true;
			}
			else {
				$transaction->rollBack();
			}
		}
		catch (Exception $e){
			$transaction->rollBack();
		}
		
		return false;
	}
}