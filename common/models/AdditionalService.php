<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "additional_service".
 *
 * @property integer $id
 * @property string $name
 * @property string $price
 * @property boolean $is_active
 *
 * @property AdditionalService2appointment[] $additionalService2appointments
 */
class AdditionalService extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'additional_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['price'], 'number'],
            [['is_active'], 'boolean'],
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
            'price' => Yii::t('app', 'Price'),
            'is_active' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalService2appointments()
    {
        return $this->hasMany(AdditionalService2appointment::className(), ['additional_service_id' => 'id'])->inverseOf('additionalService');
    }
}
