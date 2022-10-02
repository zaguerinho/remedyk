<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "certification".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Certification2doctor[] $certification2doctors
 */
class Certification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'certification';
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
    public function getCertification2doctors()
    {
        return $this->hasMany(Certification2doctor::className(), ['certification_id' => 'id'])->inverseOf('certification');
    }
}
