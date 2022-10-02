<?php
namespace common\models;

use common\daemons\WebsocketClient;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use yii\base\Exception;
use kartik\select2\Select2;
use phpDocumentor\Reflection\Types\Static_;
use Conekta\Customer;
use yii\helpers\ArrayHelper;


/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at

 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property string $phone
 * @property string $picture
 * @property string $conekta_customer_id
 *
 * @property string profileType
 * @property Doctor $doctor
 * @property Patient $patient
 * @property DoctorPayment[] $doctorPayments
 * @property Appointment[] $changedAppointments
 * @property Comment[] $commentsFrom
 * @property Comment[] $commentsTo
 * @property Comment[] $commentsApproved
 * @property Comment[] $commentsBanned
 *
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_TO_CONFIRM = 5;
    const STATUS_ACTIVE = 10;
    /**
     * @inheritdoc
     */

    var $keypassword = '';
    public $picture_file;

    const DOCTOR = 'doctor';
    const PATIENT = 'patient';
    const STAFF = 'staff';

    //scenarios
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_FORGOT_PASSWORD = 'forgot_password';
    const SCENARIO_RESET_PASSWORD = 'reset_password';

    const PHONE_FORMAT = '(999) 999-9999';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules() {
    	return [
    			[['username', 'email', 'first_name', 'last_name'], 'required'],
    			[['email'], 'email'],

    			[['status', 'created_at', 'updated_at'], 'integer'],
    			['phone', 'match', 'pattern' => '/^(\(\d{3}\) \d{3}-\d{4}|\d{10})+$/i'],
    			['phone', 'filter', 'filter' => function ($value) { return str_replace(['(', ')', '-', ' '], "", $value); }],
    			[['username', 'password_hash', 'password_reset_token', 'birth_date', 'conekta_customer_id'], 'string', 'max' => 255],
    			[['auth_key'], 'string', 'max' => 32],
    			[['username', 'email'], 'unique', 'except' => [self::SCENARIO_FORGOT_PASSWORD, self::SCENARIO_RESET_PASSWORD]],
    			['status', 'default', 'value' => self::STATUS_ACTIVE],
    			['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_TO_CONFIRM, self::STATUS_DELETED]],
    			[['id', 'username', 'email', 'status', 'created_at', 'updated_at', 'first_name', 'last_name', 'birth_date', 'picture', 'picture_file', 'phone'], 'safe'],

    			['username', 'exist', 'on' => [self::SCENARIO_FORGOT_PASSWORD, self::SCENARIO_RESET_PASSWORD]],
    			['keypassword', 'validateKeyPassword', 'on' => self::SCENARIO_RESET_PASSWORD],
    			[['username'], 'email', 'on' => self::SCENARIO_FORGOT_PASSWORD],
    	];
    }

    public function attributeLabels(){
    	return [
    		'username' => Yii::t('app', 'Username'),
    		'first_name' => Yii::t('app', 'First Name'),
    		'last_name' => Yii::t('app', 'Last Name'),
    		'status' => Yii::t('app', 'User Status'),
    		'email' => Yii::t('app', 'Email'),
    		'birth_date' => Yii::t('app', 'Birth Date'),
    		'picture' => Yii::t('app', 'Picture'),
    		'phone' => Yii::t('app', 'Cell Phone'),
    		'name' => Yii::t('app', 'Name'),
    		'conekta_customer_id' => Yii::t('app', 'Conekta Customer ID'),



    	];
    }

    public function scenarios() {
    	$scenarios = parent::scenarios();
    	$scenarios[self::SCENARIO_FORGOT_PASSWORD] = ['username','password_reset_token'];
    	$scenarios[self::SCENARIO_RESET_PASSWORD] = ['username','keypassword'];
    	return $scenarios;
    }

    /**
     * Validates the Key (password_hash or reset_password_token).
     * @param string $attribute
     * @param array|null $params
     * @return boolean
     */
    public function validateKeyPassword($attribute, $params) {
    	if (!$this->hasErrors()) {
    		$user = self::findByUsernameAndKeyword($this->username, $this->keypassword);
    		if($user === null){
    			$this->addError($attribute, $this->getAttributeLabel($attribute)." not match.");
    			return false;
    		}
    	}
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
    	return static::find()->where(['and', ['id' => $id], ['or', ['status' => self::STATUS_ACTIVE], ['status' => self::STATUS_DELETED], ['status' => self::STATUS_TO_CONFIRM]]])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }


    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
    	return static::find()->where(['and', ['username' => $username], ['or', ['status' => self::STATUS_ACTIVE], ['status' => self::STATUS_DELETED], ['status' => self::STATUS_TO_CONFIRM]]])->one();
    }

    /**
     * Find by username and keyword: password_hash or password_reset_token
     * @param string $username
     * @param string $keyword
     * @return User|null
     */
    public static function findByUsernameAndKeyword($username,$keyword) {
    	$user = self::findByUsername($username);
    	if(!$user)
    		return null;
    		if($user->password_reset_token === $keyword)
    			return $user;
    			if($user->validatePassword($keyword))
    				return $user;
    			return null;
    }

    public static function findByConektaCustomerId($conekta_customer_id){
    	return static::findOne(['conekta_customer_id' => $conekta_customer_id]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::find()->where([
        	'and',
            ['password_reset_token' => $token],
        	['or', ['status' => self::STATUS_ACTIVE], ['status' => self::STATUS_DELETED], ['status' => self::STATUS_TO_CONFIRM]]
        ])->one();
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function phoneFormat($phone) {
    	if($phone == NULL)
    		return '';
    		if(strlen($phone) != 10)
    			return $phone;
    			return '('.substr($phone,0,3).') '.substr($phone,3,3).'-'.substr($phone,6);
    }

    public function getPhoneText() {
    	return self::phoneFormat($this->phone);
    }

    public function afterSave($insert, $changedAttributes){
    	if (isset($changedAttributes['status'])){
    		switch($this->status){
    			case self::STATUS_ACTIVE:
    				Yii::$app
		    				->mailer
		    				->compose(
		    				['html' => 'accountActivated-html', 'text' => 'accountActivated-text'],
		    				['user' => $this]
		    				)
		    				->setFrom([Yii::$app->params['supportEmail'] => 'Remedyk robot'])
		    				->setTo($this->email)
		    				->setSubject(Yii::t('app','Remedyk Account activation'))
		    				->send();
    				break;
    			case self::STATUS_DELETED:
    				Yii::$app
	    				->mailer
	    				->compose(
	    				['html' => 'accountDisabled-html', 'text' => 'accountDisabled-text'],
	    				['user' => $this]
	    				)
	    				->setFrom([Yii::$app->params['supportEmail'] => 'Remedyk robot'])
	    				->setTo($this->email)
	    				->setSubject(Yii::t('app','Remedyk Account disabled'))
	    				->send();
    				break;
    		}
    	}
    }

    public function sendConfirmationMail($doctor_id=null, $office_id=null){
    	/* @var $user User */
    	$params = null;
    	if ($doctor_id && $office_id){
    		$params = new \stdClass();
    		$params->doctor_id = $doctor_id;
    		$params->office_id = $office_id;
    	}

    	if (!$this->status == User::STATUS_TO_CONFIRM) {
    		return false;
    	}

    	if (!User::isPasswordResetTokenValid($this->password_reset_token)) {
    		$this->generatePasswordResetToken();
    		if (!$this->save()) {
    			return false;
    		}
    	}

    	$templateName = 'activateAccount';
    	if ($this->isDoctor()){
    		$templateName = 'activateAccountDoctor';
    	}
    	return Yii::$app
    	->mailer
    	->compose(
    			['html' => $templateName.'-html', 'text' => $templateName.'-text'],
    			['user' => $this, 'params' => $params]
    			)
    			->setFrom([Yii::$app->params['supportEmail'] => 'Remedyk robot'])
    			->setTo($this->email)
    			->setSubject(Yii::t('app','Remedyk Account activation'))
    			->send();
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessagesReceived(){
    	return $this->hasMany(Message::className(), ['to_id' => 'id'])->inverseOf('to');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessagesSent(){
    	return $this->hasMany(Message::className(), ['from_id' => 'id'])->inverseOf('from');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications(){
    	return $this->hasMany(Notification::className(), ['target_id' => 'id'])->inverseOf('target');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor(){
    	return $this->hasOne(Doctor::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient(){
    	return $this->hasOne(Patient::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorPayments(){
    	return $this->hasMany(DoctorPayment::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChangedAppointments(){
    	return $this->hasMany(Appointment::className(), ['changed_by' => 'id'])->inverseOf('changedBy');
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommentsFrom(){
    	return $this->hasMany(Comment::className(), ['from_id' => 'id'])->inverseOf('from');
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommentsTo(){
    	return $this->hasMany(Comment::className(), ['target_id' => 'id'])->inverseOf('target');
    }
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommentsApproved(){
    	return $this->hasMany(Comment::className(), ['approved_by' => 'id'])->inverseOf('approvedBy');
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommentsBanned(){
    	return $this->hasMany(Comment::className(), ['banned_by' => 'id'])->inverseOf('bannedBy');
    }


    public static function getDoctors(){
    	return User::find()->innerJoin('doctor', 'doctor.user_id = "user".id')->all();
    }

    public static function getPatients(){
    	return User::find()->innerJoin('patient', 'doctor.user_id = "user".id')->all();
    }

    /**
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAdmins(){
    	return User::find()->leftJoin('doctor', 'doctor.user_id = "user".id')->leftJoin('patient', 'patient.user_id = "user".id')->where(['patient.id' => null, 'doctor.id' => null])->all();
    }

    public function isDoctor(){
    	return ($this->doctor != null);
    }

    public function isPatient(){
    	return ($this->patient != null);
    }

    public function isStaff() {
    	return !$this->isDoctor() && !$this->isPatient();
    }

    public function getProfileType(){
    	return $this->profileType();
    }

    public function profileType(){
    	if ($this->isStaff())
    		return self::STAFF;
    	if ($this->isDoctor())
    		return self::DOCTOR;
    	if ($this->patient)
    		return self::PATIENT;
    	throw new Exception(Yii::t('Unknown profile type'));
    }

    public function getProfilePicture(){
    	switch ($this->getProfileType()){
    		case self::DOCTOR:
    			return $this->doctor->getPicture();
    		case self::PATIENT:
    			return $this->patient->getPicture();
    		case self::STAFF:
    			$staffDomain = Yii::$app->params['adminDomain'];
    			return ($this->picture ? $staffDomain.$this->picture : $staffDomain.'/images/user_default.png');
    	}
    }

    public function setProfilePicture(){
    	$picture = UploadedFile::getInstance($this, 'picture_file');
    	if ($picture){

    		$webroot = Yii::getAlias('@webroot');

    		$uploadsDir = Yii::$app->params['uploadsDir'];

    		$previousPictureFile = $webroot.$this->picture;
    		if ($this->picture && file_exists($previousPictureFile))
    			unlink($previousPictureFile);
    		$filename = $uploadsDir . $this->id . '_' . date('YmdHis') . '.' . $picture->extension;
    		$picture->saveAs($webroot.$filename);

    		$this->picture = $filename;
    		return true;
    	}
    	return false;

    }

    public function beforeSave($insert){
    	$this->setProfilePicture();
    	return parent::beforeSave($insert);
    }

    public function getName(){
    	return $this->first_name . ' ' . $this->last_name;
    }

    public function getAge(){
    	if (!isset($this->birth_date))
    		return null;
    	$age = date('Y') - date('Y', strtotime($this->birth_date));
    	if (date('m') < date('m', strtotime($this->birth_date)) || (date('m') == date('m', strtotime($this->birth_date)) && date('d') < date('m', strtotime($this->birth_date))))
    		$age -= 1;
    	return $age;
    }

	/**
	 * @return null|self
	 */
    public static function getUserIdentity(){
    	return Yii::$app->getUser()->identity;
	}

	public function getActiveChatUsers(){
		$messages = Message::find()->select(['from_id', 'to_id', 'MAX(sent_at)'])->where(['or', ['from_id' => $this->id], ['to_id' => $this->id]])->orderBy('MAX(sent_at) desc')->groupBy(['from_id', 'to_id'])->all();
		$users = [];
		foreach ($messages as $message){
			if ($message->from_id == $this->id){
				if (!in_array($message->to_id, array_keys(ArrayHelper::map($users, 'id', 'id'))))
					$users[] = User::findOne($message->to_id);
			}
			else{
				if (!in_array($message->from_id, array_keys(ArrayHelper::map($users, 'id', 'id'))))
					$users[] = User::findOne($message->from_id);
			}
		}
		return $users;
	}

	public function getUnreadMessageCount($user_id=null){
		$query = Message::find()->where(['to_id' => $this->id, 'readed_at' => null]);
		if ($user_id){
			$query->andWhere(['from_id' => $user_id]);
		}
		$count = $query->count();
		return ($count > 0) ? $count : '';
	}

	public function getLastNotifications($page=1){
		$notifications = Notification::find()->where(['target_id' => $this->id, ])->orderBy('datetime desc')->limit(10)->offset(($page-1)*10)->all();
		return $notifications;
	}

	public function getUnseenNotificationsCount(){
		$count = Notification::find()->where(['target_id' => $this->id, 'seen_at' => null])->count();
		return ($count > 0) ? $count: "";
	}
	public function clearUnseenNotifications(){
		$notifications =  Notification::find()->where(['target_id' => $this->id, 'seen_at' => null])->all();
		$cleared = false;
		foreach ($notifications as $notification){
			$notification->seen_at = date('Y-m-d H:i:s');
			$notification->save();
			$cleared = true;
		}
		if ($cleared){
			$WebSocketClient = new WebsocketClient(Yii::$app->params['websocketServer'], Yii::$app->params['websocketPort']);

			$entryData = ['action' => 'unseen', 'count' => $this->getUnseenNotificationsCount(), 'target_id' => $this->id];
			$result = $WebSocketClient->sendData(json_encode($entryData));
			unset($WebSocketClient);
		}

	}

}
