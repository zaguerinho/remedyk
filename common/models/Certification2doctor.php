<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "certification2doctor".
 *
 * @property integer $id
 * @property integer $doctor_id
 * @property integer $certification_id
 *
 * @property Certification $certification
 * @property Doctor $doctor
 */
class Certification2doctor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'certification2doctor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doctor_id', 'certification_id'], 'integer'],
            [['certification_id'], 'exist', 'skipOnError' => true, 'targetClass' => Certification::className(), 'targetAttribute' => ['certification_id' => 'id']],
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
            'doctor_id' => Yii::t('app', 'Doctor'),
        		'certification_id' => Yii::t('app', 'Certification'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertification()
    {
        return $this->hasOne(Certification::className(), ['id' => 'certification_id'])->inverseOf('certification2doctors');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('certification2doctors');
    }
}
