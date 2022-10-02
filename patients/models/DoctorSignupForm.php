<?php
namespace patients\models;

use common\models\User;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use common\models\Doctor;
use common\models\Notification;

/**
 * Signup form
 */
class DoctorSignupForm extends SignupForm
{
	public $license_number;
	public $gender;


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = parent::rules();
		return ArrayHelper::merge($rules, [
			[['license_number', 'gender'], 'string'],
		]);
	}

	/**
	 * Signs user up and creates the asociate doctor.
	 *
	 * @return User|null the saved model or null if saving fails
	 */
	public function signup()
	{
		$transaction = Yii::$app->db->beginTransaction();
		try {
			if ($user = parent::signup()){
				//Create the doctor record too

				$doctor = new Doctor();
				$doctor->license_number = $this->license_number;
				$doctor->gender = $this->gender;
				$doctor->link('user', $user);

				if ($doctor->save()){
					$transaction->commit();
					return $user;
				}
			}

			$transaction->rollBack();
			return null;
		}
		catch (Exception $e){
			dd($e);
			$transaction->rollBack();
			return null;
		}
	}
}
