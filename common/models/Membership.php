<?php

namespace common\models;

use Conekta\ResourceNotFoundError;
use Yii;
use yii\helpers\Json;
use Conekta\Plan;

/**
 * This is the model class for table "membership".
 *
 * @property integer $id
 * @property string $price
 * @property integer $picture_count
 * @property integer $extra_rank
 * @property string $name
 * @property string $description
 * @property integer $currency_id
 *
 * @property Membership2doctor[] $membership2doctors
 * @property Doctor[] $doctors
 * @property Currency $currency
 * @property Plan $conektaPlan
 * 
 */
class Membership extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'membership';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price', 'commission_percent'], 'number'],
            [['picture_count', 'extra_rank', 'currency_id'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
        	[['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'price' => Yii::t('app', 'Price'),
            'picture_count' => Yii::t('app', 'Picture Count'),
            'extra_rank' => Yii::t('app', 'Extra Rank'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        	'currency_id' => Yii::t('app', 'Currency'),
        	'commission_percent' => Yii::t('app', 'Commission Percent'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembership2doctors()
    {
        return $this->hasMany(Membership2doctor::className(), ['membership_id' => 'id'])->inverseOf('membership');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctors()
    {
        return $this->hasMany(Doctor::className(), ['id' => 'doctor_id'])->viaTable('membership2doctor', ['membership_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency(){
    	return $this->hasOne(Currency::className(), ['id' => 'currency_id'])->inverseOf('memberships');
    }
    
    public function getConektaPlan(){
    	if ($this->price == 0){
    		return null;
    	}
    	
    	try {
    		$plan = Plan::find('membership-'.$this->id);
    		//If something changed
    		if ('membership-'.$this->id != $plan->id
    		|| Json::decode($this->name, true)['en'] != $plan->name
    		|| $this->price*100 != $plan->amount
    		|| $this->currency->code != $plan->currency
    		|| 'month' != $plan->interval
    		){
	    		$plan->update([
	    			'id' => 'membership-'.$this->id,
	    			'name' => Json::decode($this->name, true)['en'],
	    			'amount' => $this->price*100,
	    			'currency' => $this->currency->code,
	    			'interval' => 'month',
	    		]);
    		}
    	}
    	catch (ResourceNotFoundError $e){
    		$plan = Plan::create([
    				'id' => 'membership-'.$this->id,
    				'name' => Json::decode($this->name, true)['en'],
    				'amount' => $this->price*100,
    				'currency' => $this->currency->code,
    				'interval' => 'month',
    				//'expiry_count' => 12, 
    		]);
    	}
    	return $plan;
    }
}
