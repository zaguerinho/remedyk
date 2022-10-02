<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "doctor_video".
 *
 * @property integer $id
 * @property string $url
 * @property string $name
 * @property string $mime_type
 * @property integer $doctor_id
 *
 * @property Doctor $doctor
 */
class DoctorVideo extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doctor_video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doctor_id'], 'integer'],
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
            'doctor_id' => Yii::t('app', 'Doctor ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('doctorVideos');
    }
    
    public function beforeSave($insert){
    	$this->url = $this->getVideoId($this->url);
    	return parent::beforeSave($insert);
    }
    
    public function getVideoId($text){
    	if (strpos(strtolower($text), 'youtu.be/') !== false || strpos(strtolower($text), 'youtube.com/embed/') !== false){
    		$id = explode('/', $text)[count(explode('/', $text)) - 1];
    		return $id;
    	}
    	elseif (strpos(strtolower($text), 'youtube.com/watch') !== false){
    		$query = explode('?', $text)[1];
    		$params = explode('&', $query);
    		foreach ($params as $item){
    			$param = explode('=', $item);
    			if ($param[0] == 'v'){
    				$id = $param[1];
    				return $id;
    			}
    		}
    	}
    	elseif (strpos($text, '/') === false
    			&& strpos($text, '?') === false
    			&& strpos($text, ':') === false
    			&& strpos($text, '&') === false) {
    		return $text;
    	}
    	return '';
    }
    
    public function getVideoEmbedUrl($text){
    	$id = $this->getVideoId(text);
    	if ($id == '')
    		return '';
    	return 'https://www.youtube.com/embed/'.$id;
    }
    
    public function getVideoUrl($text){
    	$id = $this->getVideoId(text);
    	if ($id == '')
    		return '';
    	return 'https://www.youtube.com/watch?v='.$id;
    }
}
