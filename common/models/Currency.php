<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "currency".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $symbol
 *
 * @property Appointment[] $appointments
 * @property Doctor[] $doctors
 * @property Procedure2doctor[] $procedure2doctors
 * @property Membership[] $memberships
 * @property DoctorPayment[] $doctorPayments
 */
class Currency extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['code', 'symbol'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'symbol' => Yii::t('app', 'Symbol'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointments()
    {
        return $this->hasMany(Appointment::className(), ['currency_id' => 'id'])->inverseOf('currency');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctors()
    {
        return $this->hasMany(Doctor::className(), ['currency_id' => 'id'])->inverseOf('currency');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcedure2doctors()
    {
        return $this->hasMany(Procedure2doctor::className(), ['currency_id' => 'id'])->inverseOf('currency');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMemberships(){
    	return $this->hasMany(Membership::className(), ['currency_id' => 'id'])->inverseOf('currency');
    }
    
    public function getLocalized_name(){
    	return Json::decode($this->name, true)[Yii::$app->language];
    }
    
    public function getDoctorPayments()
    {
    	return $this->hasMany(DoctorPayment::className(), ['currency_id' => 'id'])->inverseOf('currency');
    }
}
