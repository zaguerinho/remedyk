<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "configuration_category".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Configuration[] $configurations
 */
class ConfigurationCategory extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'configuration_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
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
    public function getConfigurations()
    {
        return $this->hasMany(Configuration::className(), ['configuration_category_id' => 'id'])->inverseOf('configurationCategory');
    }
}
