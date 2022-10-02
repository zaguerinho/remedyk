<?php
	/**
	 * Created by PhpStorm.
	 * User: asaenz
	 * Date: 6/1/2018
	 * Time: 04:44 PM
	 */

	namespace patients\controllers;

	use Yii;
	use common\controllers\MyAccountController;
	use common\models\ClinicalStory;
	use common\models\User;
	use yii\web\Response;
	use yii\data\ActiveDataProvider;

	class ProfileController extends MyAccountController{

		public function actionIndex(){

			$model = User::getUserIdentity()->patient;

			$clinicalStory = new ClinicalStory();
			$query         = ClinicalStory::find()
				->where(['patient_id' => $model->id])
			;
			$dataProvider  = new ActiveDataProvider(['query' => $query]);

			return $this->render('/profile/index',
				[
					'model'         => $model,
					'clinicalStory' => $clinicalStory,
					'dataProvider'  => $dataProvider,
				]);

		}


		public function actionUpdate(){

			$model       = User::getUserIdentity()->patient;
			$transaction = \Yii::$app->getDb()
				->beginTransaction()
			;

			if(\Yii::$app->request->isPost){
				$post = &\Yii::$app->request->post();
				if($model->load($post))
					if($model->user->load($post))
						if($model->user->save()){
							$address = $model->getAddressOrCreateOne();
							$loaded  = $address->load($post);
							if($loaded){
								$saved = $address->save();
								if($saved){
									$model->address_id = $address->id;
									if($model->save()){
										$transaction->commit();

										return $this->redirect('index');
									}
								}
							}

						}
			}

			$transaction->rollBack();

			return $this->render('/profile/update',
				[
					'model' => $model,
				]);
		}

		public function actionAjaxShowClinicStoryDetails($id){
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	$clinicStory = ClinicalStory::findOne($id);
    	$user = $clinicStory->doctor->user;
    	$specialty_name = $clinicStory->appointment->procedure2doctor ? $clinicStory->appointment->procedure2doctor->specialty->localized_name : Yii::t('app', '(None)');
    	$clinical_story_type = $clinicStory->clinicalStoryType->localized_name;
    	$attachmentsArray = $clinicStory->clinicalStoryAttachments;
    	return [
    		'doctor_name' => $user->name,
    		'doctor_phone' => $user->phoneText,
			'doctor_email' => $user->email,
    		'specialty' => $specialty_name,
    		'registered_on' => date('m-d-Y', strtotime($clinicStory->registered_on)),
    		'notes' => $clinicStory->summary,
    		'clinical_story_type' => $clinical_story_type,
    		'attachments' => $attachmentsArray,
    	];
    }
	}