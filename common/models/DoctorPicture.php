<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "doctor_picture".
 *
 * @property integer $id
 * @property string $url
 * @property string $name
 * @property string $mime_type
 * @property integer $doctor_id
 *
 * @property Doctor $doctor
 */
class DoctorPicture extends \common\models\BaseModel
{
	
	public $base64data;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doctor_picture';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doctor_id'], 'integer'],
        	[['base64data'], 'safe'],
            [['url', 'name', 'mime_type'], 'string', 'max' => 255],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
            'name' => Yii::t('app', 'Name'),
            'mime_type' => Yii::t('app', 'Mime Type'),
            'doctor_id' => Yii::t('app', 'Doctor'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('doctorPictures');
    }
    
    public function getPicture(){
    	return Yii::$app->params['doctorsDomain'].$this->url;
    }
    
    public function beforeSave($insert){
    	if ($this->base64data){
    		$base64data = $this->base64data;
    		
    		list($type, $pict) = explode(';', $base64data);
    		list(, $pict) = explode(',', $pict);
    		
    		$webroot = Yii::getAlias('@webroot');
    		$prev_picture = $this->url;
    		if ($prev_picture){
    			// Delete previous picture
    			if (file_exists($webroot.$prev_picture))
    				unlink($webroot.$prev_picture);
    		}
    		
    		$ext = explode('/', $type)[1];
    		switch ($type){
    			case 'data:image/png':
    				$ext = 'png';
    				break;
    			default:
    				$ext = 'jpg';
    				break;
    				
    		}
    		
    		$pict = base64_decode($pict);
    		$uploads = Yii::$app->params['uploadsDir'];
    		if (!is_dir($webroot.$uploads))
    			mkdir($webroot.$uploads);
			
    		list($usec, $sec) = explode(' ', microtime());
    		$filename = $uploads.$this->doctor_id.'_'.date('YmdHis').$usec.'.'.$ext;
    		file_put_contents($webroot.$filename, $pict);
    		$this->url = $filename;
    		
    	}
    	return parent::beforeSave($insert);
    }
    public function beforeDelete(){
    	if ($this->url){
    		$webroot = Yii::getAlias('@webroot');
    		$url = $this->url;
    		// Delete picture
    		if (file_exists($webroot.$url))
    			unlink($webroot.$url);
    	}
    	return parent::beforeDelete();
    }
}
