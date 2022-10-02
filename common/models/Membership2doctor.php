<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "membership2doctor".
 *
 * @property integer $membership_id
 * @property integer $doctor_id
 * @property string $paid_on
 * @property boolean $active
 * @property string $contract_date
 *
 * @property Doctor $doctor
 * @property Membership $membership
 */
class Membership2doctor extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'membership2doctor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['membership_id', 'doctor_id'], 'required'],
            [['membership_id', 'doctor_id'], 'integer'],
            [['paid_on', 'contract_date'], 'safe'],
            [['active'], 'boolean'],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['membership_id'], 'exist', 'skipOnError' => true, 'targetClass' => Membership::className(), 'targetAttribute' => ['membership_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'membership_id' => Yii::t('app', 'Membership ID'),
            'doctor_id' => Yii::t('app', 'Doctor ID'),
            'paid_on' => Yii::t('app', 'Paid On'),
            'active' => Yii::t('app', 'Active'),
            'contract_date' => Yii::t('app', 'Contract Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('membership2doctors');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembership()
    {
        return $this->hasOne(Membership::className(), ['id' => 'membership_id'])->inverseOf('membership2doctors');
    }
}
