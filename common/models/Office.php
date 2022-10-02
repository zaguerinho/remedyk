<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "office".
 *
 * @property integer $id
 * @property string $title
 * @property boolean $is_active
 * @property integer $doctor_id
 * @property integer $address_id
 *
 * @property Appointment[] $appointments
 * @property DoctorWorkingHour[] $doctorWorkingHours
 * @property Address $address
 * @property Doctor $doctor
 */
class Office extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'office';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active'], 'boolean'],
            [['doctor_id', 'address_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Office Name'),
            'is_active' => Yii::t('app', 'Is Active'),
            'doctor_id' => Yii::t('app', 'Doctor'),
            'address_id' => Yii::t('app', 'Address'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointments()
    {
        return $this->hasMany(Appointment::className(), ['office_id' => 'id'])->inverseOf('office');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorWorkingHours()
    {
        return $this->hasMany(DoctorWorkingHour::className(), ['office_id' => 'id'])->inverseOf('office');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'address_id'])->inverseOf('offices');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('offices');
    }
}
