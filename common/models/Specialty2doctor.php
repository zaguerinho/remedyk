<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "specialty2doctor".
 *
 * @property integer $id
 * @property integer $specialty_id
 * @property integer $doctor_id
 * @property boolean $is_active
 * @property boolean $is_main
 *
 * @property Doctor $doctor
 * @property Specialty $specialty
 */
class Specialty2doctor extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'specialty2doctor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['specialty_id', 'doctor_id'], 'integer'],
            [['is_active', 'is_main'], 'boolean'],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
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
            'specialty_id' => Yii::t('app', 'Specialty ID'),
            'doctor_id' => Yii::t('app', 'Doctor ID'),
            'is_active' => Yii::t('app', 'Is Active'),
            'is_main' => Yii::t('app', 'Is Main'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('specialty2doctors');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialty()
    {
        return $this->hasOne(Specialty::className(), ['id' => 'specialty_id'])->inverseOf('specialty2doctors');
    }
}
