<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "payment_method".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Commission[] $commissions
 */
class PaymentMethod extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_method';
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
    public function getCommissions()
    {
        return $this->hasMany(Commission::className(), ['payment_method_id' => 'id'])->inverseOf('paymentMethod');
    }
    
    public function getLocalized_name(){
    	return Json::decode($this->name, true)[Yii::$app->language];
    }
}
