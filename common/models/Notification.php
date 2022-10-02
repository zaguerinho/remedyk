<?php

namespace common\models;

use common\daemons\WebsocketClient;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property string $text
 * @property string $target_url
 * @property string $datetime
 * @property string $seen_at
 * @property string $visited_at
 * @property integer $target_id
 * @property string $fa_icon_class
 *
 * @property User $target
 */
class Notification extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['datetime', 'seen_at', 'visited_at', 'fa_icon_class'], 'safe'],
            [['target_id'], 'integer'],
            [['target_url'], 'string', 'max' => 255],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['target_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'text' => Yii::t('app', 'Text'),
            'target_url' => Yii::t('app', 'Target Url'),
            'datetime' => Yii::t('app', 'Datetime'),
            'seen_at' => Yii::t('app', 'Seen At'),
            'visited_at' => Yii::t('app', 'Visited At'),
            'target_id' => Yii::t('app', 'Target ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(User::className(), ['id' => 'target_id'])->inverseOf('notifications');
    }
    
    public function afterSave($insert, $changedAttributes){
    	if ($insert){
    		$WebSocketClient = new WebsocketClient(Yii::$app->params['websocketServer'], Yii::$app->params['websocketPort']);
    		$user = $this->target;
    		$notification = [
    				'id' => $this->id,
    				'target_id' => $this->target_id,
    				'text' => $this->localized_text,
    				'datetime' => date('M-d-Y h:i A', strtotime($this->datetime)),
					'unseen_count' => $user->getUnseenNotificationsCount(),
    				'fa_icon_class' => $this->fa_icon_class,
    				'target_url' => $this->target_url,
    				
    		];
    		
    		$entryData = ['action' => 'notification', 'notification' => $notification];    		
    		$result = $WebSocketClient->sendData(json_encode($entryData));
    		unset($WebSocketClient);	
    	}
    	elseif(isset($changedAttributes['visited_at']) && $changedAttributes['visited_at'] != $this->visited_at){
    		$WebSocketClient = new WebsocketClient(Yii::$app->params['websocketServer'], Yii::$app->params['websocketPort']);
    		
    		$entryData = ['action' => 'visited', 'id' => $this->id, 'target_id' => $this->target_id];
    		$result = $WebSocketClient->sendData(json_encode($entryData));
    		unset($WebSocketClient);	
    	}
    	return parent::afterSave($insert, $changedAttributes);
    }
    
    public function getLocalized_text(){
    	return Json::decode($this->text, true)[Yii::$app->language];
    }
    
}
