<?php
	/**
	 * SYCET by TJ ALTA TECNOLOGIA Y APLICACIONES, S. DE R.L. DE C.V. is licensed under a
	 * Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.
	 * Based on a work at http://sycet.net and its subdomains.
	 *
	 * @link      http://gotribit.com/
	 * @copyright Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License
	 *
	 */
	
	
	namespace common\models;
	
	use Yii;
	use yii\base\Model;
	
	
	class PasswordForgot extends Model{
		public $email;
		public $username;
		public $newPassword;
		public $newPasswordConfirm;
		
		public function rules(){
			return [
				[
					'email',
					'filter',
					'filter' => 'trim',
				],
				[
					'email',
					'required',
				],
				[
					'username',
					'required',
				],
				[
					'username',
					'exist',
					'targetClass' => Yii::$app->getUser()->identityClass,
					'message'     => Yii::t('app', 'User not found'),
				],
				[
					'email',
					'email',
				],
				[
					'email',
					'exist',
					'targetClass' => Yii::$app->getUser()->identityClass,
					'message'     => Yii::t('app', 'Email not found'),
				],
			];
		}
		
		public function attributeLabels(){
			return [
				'username' => Yii::t('app', 'User'),
				'email'    => Yii::t('app', 'Email'),
			
			];
		}
		
		public function handleToken(){
			$classname = Yii::$app->getUser()->identityClass;
			$user      = $classname::findOne([
				'status'   => User::STATUS_ACTIVE,
				'email'    => $this->email,
				'username' => $this->username,
			]);
			/* @var $user User */
			
			if(!$user)
				return false;
			
			$user->generatePasswordResetToken();
			$user->save();
			
			return $user;
		}
	}