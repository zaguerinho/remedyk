<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "clinical_story_type".
 *
 * @property integer $id
 * @property string $name
 *
 * @property ClinicalStory[] $clinicalStories
 * @property string $localized_name
 */
class ClinicalStoryType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clinical_story_type';
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
    public function getClinicalStories()
    {
        return $this->hasMany(ClinicalStory::className(), ['clinical_story_type_id' => 'id'])->inverseOf('clinicalStoryType');
    }
    
    public function getLocalized_name(){
    	return Json::decode($this->name, true)[Yii::$app->language];
    }
}
