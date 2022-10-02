<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tax_data".
 *
 * @property integer $id
 * @property string $name
 * @property string $rfc
 * @property integer $address_id
 * @property integer $tax_regime_id
 *
 * @property Doctor[] $doctors
 * @property Patient[] $patients
 * @property Address $address
 * @property TaxRegime $taxRegime
 */
class TaxData extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address_id', 'tax_regime_id'], 'integer'],
            [['name', 'rfc'], 'string', 'max' => 255],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['tax_regime_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaxRegime::className(), 'targetAttribute' => ['tax_regime_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'rfc' => Yii::t('app', 'Rfc'),
            'address_id' => Yii::t('app', 'Address ID'),
            'tax_regime_id' => Yii::t('app', 'Tax Regime ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctors()
    {
        return $this->hasMany(Doctor::className(), ['tax_data_id' => 'id'])->inverseOf('taxData');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatients()
    {
        return $this->hasMany(Patient::className(), ['tax_data_id' => 'id'])->inverseOf('taxData');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'address_id'])->inverseOf('taxDatas');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxRegime()
    {
        return $this->hasOne(TaxRegime::className(), ['id' => 'tax_regime_id'])->inverseOf('taxDatas');
    }
}
