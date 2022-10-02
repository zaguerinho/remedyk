<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $route
 * @property string $number
 * @property string $postal_code
 * @property string $country
 * @property string $administrative_area_level_1
 * @property string $administrative_area_level_3
 * @property string $locality
 * @property string $lat
 * @property string $lng
 * @property integer $type
 * @property string $url_gmaps
 *
 * @property Doctor[] $doctors
 * @property Office[] $offices
 * @property OperatingRoom[] $operatingRooms
 * @property Patient[] $patients
 * @property TaxData[] $taxDatas
 * @property string $text
 */
class Address extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['route', 'number', 'postal_code', 'country', 'administrative_area_level_1', 'administrative_area_level_3', 'locality', 'lat', 'lng', 'url_gmaps'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'route' => Yii::t('app', 'Street'),
            'number' => Yii::t('app', 'Number'),
            'postal_code' => Yii::t('app', 'Postal Code'),
            'country' => Yii::t('app', 'Country'),
            'administrative_area_level_1' => Yii::t('app', 'Administrative Area Level 1'),
            'administrative_area_level_3' => Yii::t('app', 'Administrative Area Level 3'),
            'locality' => Yii::t('app', 'Locality'),
            'lat' => Yii::t('app', 'Lat'),
            'lng' => Yii::t('app', 'Lng'),
            'type' => Yii::t('app', 'Type'),
            'url_gmaps' => Yii::t('app', 'Url Gmaps'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctors()
    {
        return $this->hasMany(Doctor::className(), ['postal_address_id' => 'id'])->inverseOf('postalAddress');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffices()
    {
        return $this->hasMany(Office::className(), ['address_id' => 'id'])->inverseOf('address');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperatingRooms()
    {
        return $this->hasMany(OperatingRoom::className(), ['address_id' => 'id'])->inverseOf('address');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatients()
    {
        return $this->hasMany(Patient::className(), ['address_id' => 'id'])->inverseOf('address');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxDatas()
    {
        return $this->hasMany(TaxData::className(), ['address_id' => 'id'])->inverseOf('address');
    }
    
    public function toString(){
    	if (!$this->country) {
    		return Yii::t('app','Not available');
    	}
    	
    	$str = $this->country;
    	
    	if ($this->administrative_area_level_1) {
    		$str = $this->administrative_area_level_1.", ".$str;
    		
    		if ($this->administrative_area_level_3) {
    			$str = $this->administrative_area_level_3.", ".$str;
    		} else {
    			if ($this->locality) {
    				$str = $this->locality.", ".$str;
    			}
    		}
    	} else {
    		if ($this->administrative_area_level_3) {
    			$str = $this->administrative_area_level_3.", ".$str;
    		} else {
    			if ($this->locality) {
    				$str = $this->locality.", ".$str;
    			}
    		}
    	}
    	
    	return $str;
    }
    
    public function getText(){
    	return $this->toString();
    }
}
