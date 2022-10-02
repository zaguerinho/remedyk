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
	
	use kartik\password\StrengthValidator;
	use yii\base\Model;
	
	
	class NewPassword extends Model{
		public $newPass;
		
		public function rules(){
			return [
				[
					['newPass'],
					'required',
				],
				[
					'newPass',
					'string',
				],
				[
					'newPass',
					'safe',
				],
				[['newPass'], StrengthValidator::className(), 'preset' => 'normal', 'userAttribute' => 'username'],
			];
		}
		
		public function attributeLabels(){
			return [
				'newPass' => \Yii::t('app', 'New password'),
			];
		}
		
	}