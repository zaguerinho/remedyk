<?php

namespace common\models;

use common\daemons\WebsocketClient;
use Yii;

/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property string $message
 * @property string $seen_at
 * @property string $sent_at
 * @property string $readed_at
 * @property boolean $is_active
 * @property integer $from_id
 * @property integer $to_id
 *
 * @property User $from
 * @property User $to
 */
class Message extends \common\models\BaseModel
{
	public $temp_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['seen_at', 'sent_at', 'readed_at', 'temp_id'], 'safe'],
            [['is_active'], 'boolean'],
            [['from_id', 'to_id'], 'integer'],
            [['from_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['from_id' => 'id']],
            [['to_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['to_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'message' => Yii::t('app', 'Message'),
            'seen_at' => Yii::t('app', 'Seen At'),
            'sent_at' => Yii::t('app', 'Sent At'),
            'readed_at' => Yii::t('app', 'Readed At'),
            'is_active' => Yii::t('app', 'Is Active'),
            'from_id' => Yii::t('app', 'From ID'),
            'to_id' => Yii::t('app', 'To ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFrom()
    {
        return $this->hasOne(User::className(), ['id' => 'from_id'])->inverseOf('messagesSent');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTo()
    {
        return $this->hasOne(User::className(), ['id' => 'to_id'])->inverseOf('messagesReceived');
    }
    public function afterSave($insert, $changedAttributes){
    	if ($insert){
    		$WebSocketClient = new WebsocketClient(Yii::$app->params['websocketServer'], Yii::$app->params['websocketPort']);
	    	$message = [
	    			'id' => $this->id,
	    			'from_id' => $this->from_id,
	    			'to_id' => $this->to_id,
	    			'fromPicture' => $this->from->profilePicture,
	    			'sent_at' => Yii::t('app', 'Sent at: '). date('M-d-Y h:i A', strtotime($this->sent_at)),
	    			'message' => $this->message,
	    			'temp_id' =>$this->temp_id,
	    			'unread' => $this->to->getUnreadMessageCount()
	    	];
	    	$entryData = ['action' => 'chat', 'message' => $message];

	    	$result = $WebSocketClient->sendData(json_encode($entryData));
	    	unset($WebSocketClient);
    	}
    	return parent::afterSave($insert, $changedAttributes);

    }
}
