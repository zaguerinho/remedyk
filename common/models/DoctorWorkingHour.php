<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "doctor_working_hour".
 *
 * @property integer $id
 * @property integer $week_day
 * @property integer $month_day
 * @property integer $month
 * @property integer $year
 * @property string $start_time
 * @property string $end_time
 * @property boolean $is_working_hour
 * @property boolean $is_active
 * @property boolean $is_enabled
 * @property integer $doctor_id
 * @property integer $office_id
 * @property integer $shift
 *
 * @property Doctor $doctor
 * @property Office $office
 */
class DoctorWorkingHour extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doctor_working_hour';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['week_day', 'month_day', 'month', 'year', 'doctor_id', 'office_id', 'shift'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['is_working_hour', 'is_active', 'is_enabled'], 'boolean'],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['office_id'], 'exist', 'skipOnError' => true, 'targetClass' => Office::className(), 'targetAttribute' => ['office_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'week_day' => Yii::t('app', 'Week Day'),
            'month_day' => Yii::t('app', 'Month Day'),
            'month' => Yii::t('app', 'Month'),
            'year' => Yii::t('app', 'Year'),
            'start_time' => Yii::t('app', 'Start Time'),
            'end_time' => Yii::t('app', 'End Time'),
            'is_working_hour' => Yii::t('app', 'Is Working Hour'),
            'is_active' => Yii::t('app', 'Is Active'),
            'is_enabled' => Yii::t('app', 'Is Enabled'),
            'doctor_id' => Yii::t('app', 'Doctor'),
            'office_id' => Yii::t('app', 'Office'),
        	'shift' => Yii::t('app', 'Shift'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('doctorWorkingHours');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::className(), ['id' => 'office_id'])->inverseOf('doctorWorkingHours');
    }
    
    public function appliesToDate($date){
    	$month_day = date('d', strtotime($date));
    	$month = date('m', strtotime($date));
    	$year = date('Y', strtotime($date));
    	$week_day = date('w', strtotime($date));
    	$applies = true;
    	
    	if ((!is_null($this->year) && $this->year != $year)
    		|| (!is_null($this->month) && $this->month != $month)
    		|| (!is_null($this->month_day) && $this->month_day != $month_day)
    		|| (!is_null($this->week_day) && $this->week_day != $week_day)
    	){
    		$applies = false;
    	}
    	
    	return $applies;
    	
    }
}
