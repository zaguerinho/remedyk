<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "operating_room".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $is_active
 * @property integer $doctor_id
 * @property integer $address_id
 *
 * @property Appointment[] $appointments
 * @property Address $address
 * @property Doctor $doctor
 */
class OperatingRoom extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operating_room';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active'], 'boolean'],
            [['doctor_id', 'address_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'name' => Yii::t('app', 'Operating Room Name'),
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
        return $this->hasMany(Appointment::className(), ['operating_room_id' => 'id'])->inverseOf('operatingRoom');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'address_id'])->inverseOf('operatingRooms');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('operatingRooms');
    }
}
