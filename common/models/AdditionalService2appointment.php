<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "additional_service2appointment".
 *
 * @property integer $id
 * @property integer $additional_service_id
 * @property integer $appointment_id
 * @property integer $price_assigned_by
 * @property string $notes
 * @property string $price
 *
 * @property AdditionalService $additionalService
 * @property Appointment $appointment
 * @property User $priceAssignedBy
 */
class AdditionalService2appointment extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'additional_service2appointment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['additional_service_id', 'appointment_id', 'price_assigned_by'], 'integer'],
            [['notes'], 'string'],
            [['price'], 'number'],
            [['additional_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdditionalService::className(), 'targetAttribute' => ['additional_service_id' => 'id']],
            [['appointment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Appointment::className(), 'targetAttribute' => ['appointment_id' => 'id']],
            [['price_assigned_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['price_assigned_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'additional_service_id' => Yii::t('app', 'Additional Service ID'),
            'appointment_id' => Yii::t('app', 'Appointment ID'),
            'price_assigned_by' => Yii::t('app', 'Price Assigned By'),
            'notes' => Yii::t('app', 'Notes'),
            'price' => Yii::t('app', 'Price'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalService()
    {
        return $this->hasOne(AdditionalService::className(), ['id' => 'additional_service_id'])->inverseOf('additionalService2appointments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointment()
    {
        return $this->hasOne(Appointment::className(), ['id' => 'appointment_id'])->inverseOf('additionalService2appointments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceAssignedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'price_assigned_by'])->inverseOf('additionalService2appointments');
    }
}
