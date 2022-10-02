<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "search_history".
 *
 * @property integer $id
 * @property string $query
 * @property string $datetime
 * @property integer $patient_id
 *
 * @property Patient $patient
 */
class SearchHistory extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['patient_id'], 'integer'],
            [['query'], 'string', 'max' => 255],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patient::className(), 'targetAttribute' => ['patient_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'query' => Yii::t('app', 'Query'),
            'datetime' => Yii::t('app', 'Datetime'),
            'patient_id' => Yii::t('app', 'Patient ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::className(), ['id' => 'patient_id'])->inverseOf('searchHistories');
    }
}
