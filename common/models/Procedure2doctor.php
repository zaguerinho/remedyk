<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "procedure2doctor".
 *
 * @property integer $id
 * @property integer $procedure_id
 * @property integer $doctor_id
 * @property integer $currency_id
 * @property string $price
 * @property integer $specialty_id
 *
 * @property Appointment[] $appointments
 * @property Currency $currency
 * @property Doctor $doctor
 * @property Procedure $procedure
 * @property Specialty $specialty
 */
class Procedure2doctor extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'procedure2doctor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['procedure_id', 'doctor_id', 'currency_id', 'specialty_id'], 'integer'],
            [['price'], 'number'],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['procedure_id'], 'exist', 'skipOnError' => true, 'targetClass' => Procedure::className(), 'targetAttribute' => ['procedure_id' => 'id']],
        	[['specialty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Specialty::className(), 'targetAttribute' => ['specialty_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'procedure_id' => Yii::t('app', 'Procedure'),
            'doctor_id' => Yii::t('app', 'Doctor'),
            'currency_id' => Yii::t('app', 'Currency'),
            'price' => Yii::t('app', 'Price'),
        	'specialty_id' => Yii::t('app', 'Specialty'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointments()
    {
        return $this->hasMany(Appointment::className(), ['procedure2doctor_id' => 'id'])->inverseOf('procedure2doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id'])->inverseOf('procedure2doctors');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('procedure2doctors');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcedure()
    {
        return $this->hasOne(Procedure::className(), ['id' => 'procedure_id'])->inverseOf('procedure2doctors');
    }
    
    public function getSpecialty(){
    	return $this->hasOne(Specialty::className(), ['id' => 'specialty_id'])->inverseOf('procedure2doctors');
    }
    
    public function getPriceText(){
    	return number_format($this->price, 2).' '.$this->currency->code;
    }
}
