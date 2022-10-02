<?php

namespace doctors\controllers;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use common\models\Specialty;
use common\models\Address;
use common\models\User;
use common\models\Doctor;
use common\models\Specialty2doctor;
use common\models\Procedure2doctor;
use common\models\Certification2doctor;
use common\models\Certification;
use common\models\DoctorVideo;
use common\models\DoctorPicture;
use doctors\models\search\OfficeSearch;
use doctors\models\search\OperatingRoomSearch;
use doctors\models\DoctorHoursForm;
use doctors\models\DayOff;
use common\models\DoctorWorkingHour;

class DoctorsController extends \yii\web\Controller
{
	const PROFILE = 'profile';
	const OFFICES = 'offices';
	const OPERATING_ROOMS = 'operating_rooms';
	const HOURS = 'hours';
	
	public function behaviors(){
		return [
				'access' => [
						'class' => AccessControl::className(),
						'rules' => [
								[
										'allow' => true,
										'roles' => ['@'],
								],
						],
				],
		];
	}
	
	public function actionProfile($page=self::PROFILE)
    {
    	$activePage = $page;
    	
    	
    	if (Yii::$app->request->isPost){
    		$post = Yii::$app->request->post();    		
    		$user = Yii::$app->user->identity;
    		$doctor = $user->doctor;
    		
    		$activePage = Yii::$app->request->post('activePage');
    		switch ($activePage){
    			case self::PROFILE: 
    				return $this->saveProfile($post, $user, $doctor);
    			case self::OFFICES:
    				break;
    			case self::OPERATING_ROOMS:
    				break;
    			case self::HOURS:
    				return $this->saveHours($post, $user, $doctor);
    		}
    	}
    		
    	return $this->renderProfile($activePage);
    }
    
    /**
     * Saves the doctor profile data
     * 
     * @param array $post
     * @param User $user
     * @param Doctor $doctor
     */
    public function saveProfile($post, $user, $doctor){
    	$address = $doctor->postalAddress ? $doctor->postalAddress : new Address();
    	
    	if ($user->load($post) && $doctor->load($post) && $user->validate() && $doctor->validate()){
    		try {
    			$transaction = Yii::$app->db->beginTransaction();
    			// Save User and Doctor
    			$valid = $user->save(false);
    			$valid = $valid && $doctor->save(false);
    			if ($valid){
    				$flag = true;
    				//Save DoctorAddress
    				$address = $doctor->postalAddress ? $doctor->postalAddress : new Address();
    				if ($address->load($post)){
    					$flag = $flag && $address->save();
    					$doctor->postal_address_id = $address->id;
    					$flag = $flag && $doctor->save();
    				}
    				
    				//Save DoctorSpecialties
    				if (!isset($post['specialty']))
    					$post['specialty'] = [];
    				$newSpecialtyIds = $this->newItemIds(Specialty2doctor::class, $doctor->specialties, $post['specialty'], ['doctor_id' => $doctor->id], 'specialty_id');
    				foreach ($newSpecialtyIds as $id){
    					$doctorSpecialty = new Specialty2doctor([
    							'doctor_id' => $doctor->id,
    							'specialty_id' => $id,
    							'is_active' => true,
    							'is_main' => false,
    					]);
    					$flag = $flag && $doctorSpecialty->save();
    				}
    				
    				//Save DoctorProcedures
    				if (!isset($post['procedure']))
    					$post['procedure'] = [];
    				$newProcedureIds = $this->newItemIds(Procedure2doctor::class, $doctor->procedures, $post['procedure'], ['doctor_id' => $doctor->id], 'procedure_id');
    				foreach ($post['procedure'] as $id => $value){
    					$changed = false;
    					if (in_array($id, $newProcedureIds)){
	    					$doctorProcedure = new Procedure2doctor([
	    							'doctor_id' => $doctor->id,
	    							'procedure_id' => $id,
	    							'price' => $post['procedure_price'][$id],
	    							'specialty_id' => $post['procedure_specialty_id'][$id],
	    							'currency_id' => $post['procedure_currency_id'][$id],
	     					]);
	    					$changed = true;
    					}
    					else {
    						//Check if anything changed
    						$doctorProcedure = Procedure2doctor::find()->where(['doctor_id' => $doctor->id, 'procedure_id' => $id])->one();
    						if ($doctorProcedure->price != $post['procedure_price'][$id]){
    							$doctorProcedure->price = $post['procedure_price'][$id];
    							$changed = true;	
    						}
    						if ($doctorProcedure->currency_id != $post['procedure_currency_id'][$id]){
    							$doctorProcedure->currency_id = $post['procedure_currency_id'][$id];
    							$changed = true;
    						}
    						if ($doctorProcedure->specialty_id != $post['procedure_specialty_id'][$id]){
    							$doctorProcedure->specialty_id = $post['procedure_specialty_id'][$id];
    							$changed = true;
    						}
    					}
    					if ($changed){
    						$flag = $flag && $doctorProcedure->save();
    					}
    				}
    				
    				//Save Doctor Certifications
    				$certifications = $doctor->certifications;
    				$postedCertifications = isset($post['certification'])?$post['certification']:[];
    				
    				$deletedCertificationNames = array_diff(ArrayHelper::map($certifications, 'name', 'name'), array_values($postedCertifications));
    				if ($deletedCertificationNames){
    					$deletedCertificationIds = [];
    					$deletedCcertifications = Certification::find()->where(['name' => $deletedCertificationNames])->all();
    					foreach ($deletedCcertifications as $certification){	
    						$deletedCertificationIds[] = $certification->id;
    					}
    					Certification2doctor::deleteAll(['doctor_id' => $doctor->id, 'certification_id' => $deletedCertificationIds]);
    				}
    				$newCertificationNames = array_diff(array_values($postedCertifications), ArrayHelper::map($certifications, 'name', 'name'));
    				foreach ($newCertificationNames as $name){
    					$certification = Certification::find()->where(['name' => $name])->one();
    					if (!$certification){
    						$certification = new Certification(['name' => $name]);
    						$flag = $flag && $certification->save();
    					}
    					$doctorCertification = new Certification2doctor([
    							'doctor_id' => $doctor->id,
    							'certification_id' => $certification->id,
    					]);
    					$flag = $flag && $doctorCertification->save();
    				}
    				
    				//Save Doctor Video
    				$doctorVideo = $doctor->doctorVideos ? $doctor->doctorVideos[0] : new DoctorVideo();
    				if ($doctorVideo->url != $post['video_id']){
    					$doctorVideo->url = $post['video_id'];
    					$doctorVideo->doctor_id = $doctor->id;
    					$doctorVideo->name = 'promo_video_'.$doctor->id;
    					$flag = $flag && $doctorVideo->save();
    				}
    				
    				
    				
    				//Save Doctor Pictures
    				$postPictures = isset($post['picture'])? $post['picture'] : [];
    				$deletedPictureIds = array_diff(array_keys(ArrayHelper::map($doctor->doctorPictures, 'id', 'id')), array_values($postPictures));
    				foreach ($deletedPictureIds as $id){
    					$picture = DoctorPicture::find()->where(['doctor_id' => $doctor->id, 'id' => $id])->one();
    					$picture->delete();
    				}
    				$newPictureIds = $this->newItemIds(DoctorPicture::class, $doctor->doctorPictures, array_flip($postPictures), ['doctor_id' => $doctor->id], 'id');
    				foreach ($newPictureIds as $pictureData){
    					$picture = new DoctorPicture([
    							'doctor_id' => $doctor->id,
    							'base64data' => $pictureData
    					]);
    					$flag = $flag && $picture->save();
    				}
    				
    				if ($flag){
    					$transaction->commit();
    					Yii::$app->session->setFlash('success', Yii::t('app', 'Your profile data was saved successfully'));
    					return $this->redirect(['profile', 'page' => self::PROFILE]);
    				}
    			}
    			else {
    				$transaction->rollBack();
    			}
    		}
    		catch (Exception $e){
    			$transaction->rollBack();
    		}
    		
    	}
    	//return $this->render('profile', ['activePage' => self::PROFILE]);
    	return $this->renderProfile(self::PROFILE);
    }
    
    /**
     * Saves the doctor hours data
     *
     * @param array $post
     * @param \common\models\User $user
     * @param \common\models\Doctor $doctor
     */
    public function saveHours($post, $user, $doctor){
    	$doctorHoursForm = new DoctorHoursForm();
    	if ($doctorHoursForm->load($post) && $doctorHoursForm->validate()){
    		if ($doctorHoursForm->saveToDoctor($doctor)){
    			if ($doctor->load($post) && $doctor->save()){
    				Yii::$app->session->setFlash('success', Yii::t('app', 'Your schedule hours were saved successfully'));
    				return $this->redirect(['profile', 'page' => self::HOURS]);
    			}
    		} 		
    	}
    	return $this->renderProfile(self::HOURS);
    }
    
    private function newItemIds($class, $originalList, $postedList, $condition, $foreignKey){
    	$deletedIds = array_diff(array_keys(ArrayHelper::map($originalList, 'id', 'id')), array_keys($postedList));
    	if ($deletedIds){
    		$class::deleteAll(ArrayHelper::merge($condition, [$foreignKey => $deletedIds]));
    	}
    	$newIds = array_diff(array_keys($postedList), array_keys(ArrayHelper::map($originalList, 'id', 'id')));
    	return $newIds;
    }
    
    
    public function renderProfile($activePage){
    	$user = Yii::$app->user->identity;
    	$doctor = $user->doctor;
    	
    	$officesSearchModel = new OfficeSearch();
    	$officesDataProvider = $officesSearchModel->search(Yii::$app->request->queryParams);
    	$operatingRoomsSearchModel = new OperatingRoomSearch();
    	$operatingRoomsDataProvider = $operatingRoomsSearchModel->search(Yii::$app->request->queryParams);
    	$doctorHoursForm = new DoctorHoursForm();
    	$doctorHoursForm->loadFromDoctor($doctor);
    	
    	return $this->render('profile', [
    			'activePage' => $activePage,
    			'officesSearchModel' => $officesSearchModel,
    			'officesDataProvider' => $officesDataProvider,
    			'operatingRoomsSearchModel' => $operatingRoomsSearchModel,
    			'operatingRoomsDataProvider' => $operatingRoomsDataProvider,
    			'doctorHoursForm' => $doctorHoursForm
    			
    	]);
    }
    
    public function actionAjaxAddDayOff(){
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	if (Yii::$app->request->isPost){
    		$doctor = Yii::$app->user->identity->doctor;
    		$post = Yii::$app->request->post();
    		$dayOff  = new DayOff();
    		if ($dayOff->load($post)){
    			$month_day = date('d', strtotime($dayOff->date));
    			$month = date('m', strtotime($dayOff->date));
    			$year = date('Y', strtotime($dayOff->date));
    			$start_time = date('H:i:s', strtotime($dayOff->from));
    			$end_time = date('H:i:s', strtotime($dayOff->to));
    			$doctorWorkingHour = DoctorWorkingHour::find()->where([
    					'doctor_id' => $doctor->id,
    					'is_active' => true,
    					'is_enabled' => true,
    					'is_working_hour' => false,
    					'year' => $year,
    					'month' => $month,
    					'month_day' => $month_day
    			])->one();
    			if ($doctorWorkingHour){
    				$doctorWorkingHour->start_time = $start_time;
    				$doctorWorkingHour->end_time = $end_time;
    			}
    			else {
	    			$doctorWorkingHour = new DoctorWorkingHour([
	    				'doctor_id' => $doctor->id,
	    				'is_active' => true,
	    				'is_enabled' => true,
	    				'is_working_hour' => false,
	    				'start_time' => $start_time,
	    				'end_time' => $end_time,
	    				'year' => $year,
	    				'month' => $month,
	    				'month_day' => $month_day
	    			]);
    			}
    			if ($doctorWorkingHour->save()){
    				return ['status' => 200];
    			}
    		}
    	}
    	return ['status' => 500];
    }
    
    public function actionAjaxGetSpecialtyProcedures($id){
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	$specialty = Specialty::findOne($id);
    	$result = [];
    	if ($specialty){
    		$result = $result + ArrayHelper::map($specialty->procedures, 'id', function($element){ return Json::decode($element->name, true)[Yii::$app->language];});
    	}
    	
    	return ['results' => $result];
    }

}
