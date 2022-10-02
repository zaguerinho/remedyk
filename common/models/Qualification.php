<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "qualification".
 *
 * @property integer $id
 * @property integer $rate
 * @property boolean $is_active
 * @property integer $doctor_id
 * @property integer $patient_id
 *
 * @property Doctor $doctor
 * @property Patient $patient
 */
class Qualification extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qualification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rate', 'doctor_id', 'patient_id'], 'integer'],
            [['is_active'], 'boolean'],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patient::className(), 'targetAttribute' => ['patient_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'rate' => Yii::t('app', 'Rate'),
            'is_active' => Yii::t('app', 'Is Active'),
            'doctor_id' => Yii::t('app', 'Doctor ID'),
            'patient_id' => Yii::t('app', 'Patient ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('qualifications');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::className(), ['id' => 'patient_id'])->inverseOf('qualifications');
    }
}
