<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "prescription_detail".
 *
 * @property integer $id
 * @property string $frequency
 * @property string $lapse
 * @property string $notes
 * @property string $grammage
 * @property boolean $is_active
 * @property integer $prescription_id
 * @property integer $medicine_id
 * @property string $quantity
 *
 * @property Prescription $prescription
 * @property Medicine $medicine
 */
class PrescriptionDetail extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prescription_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notes', 'quantity'], 'string'],
            [['grammage'], 'number'],
            [['is_active'], 'boolean'],
            [['prescription_id'], 'integer'],
            [['frequency', 'lapse'], 'string', 'max' => 255],
            [['prescription_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prescription::className(), 'targetAttribute' => ['prescription_id' => 'id']],
        	[['medicine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Medicine::className(), 'targetAttribute' => ['medicine_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'frequency' => Yii::t('app', 'Frequency'),
            'lapse' => Yii::t('app', 'Lapse'),
            'notes' => Yii::t('app', 'Notes'),
            'grammage' => Yii::t('app', 'Grammage'),
            'is_active' => Yii::t('app', 'Is Active'),
            'prescription_id' => Yii::t('app', 'Prescription'),
        	'medicine_id' => Yii::t('app', 'Medicine'),
        	'quantity' => Yii::t('app', 'Quantity to buy'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrescription()
    {
        return $this->hasOne(Prescription::className(), ['id' => 'prescription_id'])->inverseOf('prescriptionDetails');
    }
    
    public function getMedicine(){
    	return $this->hasOne(Medicine::className(), ['id' => 'medicine_id'])->inverseOf('prescriptionDetails');
    }
}
