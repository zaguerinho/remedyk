<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "clinical_story_attachment".
 *
 * @property integer $id
 * @property string $url
 * @property string $name
 * @property string $mime_type
 * @property integer $clinical_story_id
 *
 * @property ClinicalStory $clinicalStory
 */
class ClinicalStoryAttachment extends \common\models\BaseModel
{
	public $base64data;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clinical_story_attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['clinical_story_id'], 'integer'],
        	[['base64data'], 'safe'],
            [['url', 'name', 'mime_type'], 'string', 'max' => 255],
            [['clinical_story_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClinicalStory::className(), 'targetAttribute' => ['clinical_story_id' => 'id']],
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
            'clinical_story_id' => Yii::t('app', 'Clinical Story'),
        ];
    }
    
    public function beforeSave($insert){
    	if ($this->base64data){
    		$base64data = $this->base64data;
    		    	
    		list($type, $content) = explode(';', $base64data);
    		$this->mime_type = $type;
    		
    		list(, $content) = explode(',', $content);
    		
    		$patientsWebroot = Yii::getAlias('@patients').'/web';
    		$prev_picture = $this->url;
    		if ($prev_picture){
    			// Delete previous picture
    			if (file_exists($patientsWebroot.$prev_picture))
    				unlink($patientsWebroot.$prev_picture);
    		}
    		
    		//$ext = explode('/', $type)[1];
    		$ext = pathinfo($this->name)['extension'];
    		
    		$content = base64_decode($content);
    		$uploads = Yii::$app->params['uploadsDir'];
    		if (!is_dir($patientsWebroot.$uploads))
    			mkdir($patientsWebroot.$uploads);
    		
    		list($usec, $sec) = explode(' ', microtime());
    		$filename = $uploads.$this->clinical_story_id.'_'.date('YmdHis').$usec.'.'.$ext;
    		file_put_contents($patientsWebroot.$filename, $content);
    		$this->url = $filename;
    			
    	}
    	return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicalStory()
    {
        return $this->hasOne(ClinicalStory::className(), ['id' => 'clinical_story_id'])->inverseOf('clinicalStoryAttachments');
    }
}
