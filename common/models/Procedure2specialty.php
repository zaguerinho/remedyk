<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "procedure2specialty".
 *
 * @property integer $id
 * @property integer $procedure_id
 * @property integer $specialty_id
 * @property boolean $is_active
 *
 * @property Procedure $procedure
 * @property Specialty $specialty
 */
class Procedure2specialty extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'procedure2specialty';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['procedure_id', 'specialty_id'], 'integer'],
            [['is_active'], 'boolean'],
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
            'procedure_id' => Yii::t('app', 'Procedure ID'),
            'specialty_id' => Yii::t('app', 'Specialty ID'),
            'is_active' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcedure()
    {
        return $this->hasOne(Procedure::className(), ['id' => 'procedure_id'])->inverseOf('procedure2specialties');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialty()
    {
        return $this->hasOne(Specialty::className(), ['id' => 'specialty_id'])->inverseOf('procedure2specialties');
    }
}
