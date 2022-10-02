<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "configuration".
 *
 * @property integer $id
 * @property string $param_code
 * @property string $param_label
 * @property string $param_value
 * @property integer $app
 * @property integer $configuration_category_id
 *
 * @property ConfigurationCategory $configurationCategory
 */
class Configuration extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'configuration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app', 'configuration_category_id'], 'integer'],
            [['param_code', 'param_label', 'param_value'], 'string', 'max' => 255],
            [['param_code'], 'unique'],
            [['configuration_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConfigurationCategory::className(), 'targetAttribute' => ['configuration_category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'param_code' => Yii::t('app', 'Param Code'),
            'param_label' => Yii::t('app', 'Param Label'),
            'param_value' => Yii::t('app', 'Param Value'),
            'app' => Yii::t('app', 'App'),
            'configuration_category_id' => Yii::t('app', 'Configuration Category ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigurationCategory()
    {
        return $this->hasOne(ConfigurationCategory::className(), ['id' => 'configuration_category_id'])->inverseOf('configurations');
    }
}
