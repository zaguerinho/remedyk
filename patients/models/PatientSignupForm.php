<?php
namespace patients\models;

use common\models\User;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use common\models\Patient;
use Yii;

/**
 * Signup form
 */
class PatientSignupForm extends SignupForm
{
	public $gender;
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = parent::rules();
		return ArrayHelper::merge($rules, [
				[['gender'], 'string'],
		]);
	}
	
	/**
	 * Signs user up and creates the asociated patient.
	 *
	 * @return User|null the saved model or null if saving fails
	 */
	public function signup()
	{
		$transaction = Yii::$app->db->beginTransaction();
		try {
			if ($user = parent::signup()){
				//Create the patient record too
				
				$patient = new Patient();
				$patient->gender = $this->gender;
				$patient->link('user', $user);
				
				if ($patient->save()){
					$transaction->commit();
					return $user;
				}
				
				$transaction->rollBack();
				return null;
			}
		}
		catch (Exception $e){
			$transaction->rollBack();
			return null;
		}
	}
}
