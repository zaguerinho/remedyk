<?php
namespace patients\models;

use yii\base\Model;
use common\models\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $repassword;
    public $phone;
    public $birth_date;
    public $first_name, $last_name;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app', 'This username has already been taken.')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
        	['email', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app', 'This email address has already been taken.')],

        	[['first_name', 'last_name', 'birth_date'], 'string'],
        	[['first_name', 'last_name'], 'required'],

        	['phone', 'match', 'pattern' => '/^(\(\d{3}\) \d{3}-\d{4}|\d{10})+$/i'],
        	['phone', 'filter', 'filter' => function ($value) { return str_replace(['(', ')', '-', ' '], "", $value); }],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        	[['repassword'], 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('app', 'Passwords do not match')],
        ];
    }

    public function attributeLabels(){

    	return ArrayHelper::merge((new User())->attributeLabels(), [
    		'password' => Yii::t('app', 'Password'),
    		'repassword' => Yii::t('app', 'Confirm Password')
    	]);
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->birth_date = $this->birth_date;
        $user->status = User::STATUS_TO_CONFIRM;

        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
