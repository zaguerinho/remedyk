<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tax_regime".
 *
 * @property integer $id
 * @property string $name
 *
 * @property TaxData[] $taxDatas
 */
class TaxRegime extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_regime';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxDatas()
    {
        return $this->hasMany(TaxData::className(), ['tax_regime_id' => 'id'])->inverseOf('taxRegime');
    }
}
