<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property string $datetime
 * @property string $ban_reason
 * @property string $text
 * @property integer $from_id
 * @property integer $target_id
 * @property integer $parent_comment_id
 * @property integer $approved_by
 * @property integer $banned_by
 *
 * @property Comment $parentComment
 * @property Comment[] $comments
 * @property User $from
 * @property User $target
 * @property User $approvedBy
 * @property User $bannedBy
 */
class Comment extends \common\models\BaseModel
{
	
	const STATUS_PENDING = '';
	const STATUs_APPROVED = 'A';
	const STATUS_BANNED = 'B';
	
	public $status;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime', 'status'], 'safe'],
            [['text'], 'string'],
            [['from_id', 'target_id', 'parent_comment_id', 'approved_by', 'banned_by'], 'integer'],
            [['ban_reason'], 'string', 'max' => 255],
            [['parent_comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::className(), 'targetAttribute' => ['parent_comment_id' => 'id']],
            [['from_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['from_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['target_id' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
            [['banned_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['banned_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'datetime' => Yii::t('app', 'Datetime'),
            'ban_reason' => Yii::t('app', 'Ban Reason'),
            'text' => Yii::t('app', 'Text'),
            'from_id' => Yii::t('app', 'From'),
            'target_id' => Yii::t('app', 'Target'),
            'parent_comment_id' => Yii::t('app', 'Parent Comment ID'),
            'approved_by' => Yii::t('app', 'Approved By'),
            'banned_by' => Yii::t('app', 'Banned By'),
        	'status' => Yii::t('app', 'Change Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentComment()
    {
        return $this->hasOne(Comment::className(), ['id' => 'parent_comment_id'])->inverseOf('comments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['parent_comment_id' => 'id'])->inverseOf('parentComment');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFrom()
    {
        return $this->hasOne(User::className(), ['id' => 'from_id'])->inverseOf('commentsFrom');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarget()
    {
        return $this->hasOne(User::className(), ['id' => 'target_id'])->inverseOf('commentsTo');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'approved_by'])->inverseOf('commentsApproved');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBannedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'banned_by'])->inverseOf('commentsBanned');
    }
    
    public function getTextPreview(){
    	if (strlen($this->text) > 30){
    		return substr($this->text, 0, 27).'...';
    	}
    	return $this->text;
    	
    }
    
    public function afterFind(){
    	if ($this->approved_by && !$this->banned_by){
    		$this->status = self::STATUs_APPROVED;
    	}
    	if (!$this->approved_by && $this->banned_by){
    		$this->status = self::STATUS_BANNED;
    	}
    	if (!$this->approved_by && !$this->banned_by){
    		$this->status = self::STATUS_PENDING;
    	}
    	return parent::afterFind();
    }
    public function beforeSave($insert){
    	switch ($this->status){
    		case self::STATUS_PENDING:
    			$this->approved_by = null;
    			$this->banned_by = null;
    			$this->ban_reason = null;
    			break;
    		case self::STATUs_APPROVED:
    			$this->approved_by = User::getUserIdentity()->id;
    			$this->banned_by = null;
    			$this->ban_reason = null;
    			break;
    		case self::STATUS_BANNED:
    			$this->approved_by = null;
    			$this->banned_by = User::getUserIdentity()->id;
    			break;
    	}
    	return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes){
    	if (!$insert && (array_key_exists('approved_by', $changedAttributes) || array_key_exists('banned_by', $changedAttributes))){
    		switch ($this->status){
    			case self::STATUs_APPROVED:
    				$notification = new Notification([
    					'target_id' => $this->from_id,
    					'text' => Json::encode([
    						'en' => 'Your review for the doctor '.$this->target->name.' was approved.',
    						'es' => 'Su reseña para el doctor '.$this->target->name.' ha sido aprobada.'
    					]),
    					'fa_icon_class' => 'fa fa-comments-o text-accepted',
    					'target_url' => '/doctors/profile?id='.$this->target->doctor->id,
    				]);
    				$notification->save();
    				break;
    			case self::STATUS_BANNED:
    				$notification = new Notification([
    				'target_id' => $this->from_id,
    				'text' => Json::encode([
    				'en' => 'Your review for the doctor '.$this->target->name.' was banned. ('.$this->ban_reason.')',
    				'es' => 'Su reseña para el doctor '.$this->target->name.' ha sido excluido. ('.$this->ban_reason.')',
    						]),
    						'fa_icon_class' => 'fa fa-comments-o text-rejected',
    						'target_url' => '/doctors/profile?id='.$this->target->doctor->id,
    						]);
    				$notification->save();
    				break;
    			
    		}
    	}
    	return parent::afterSave($insert, $changedAttributes);
    }
}
