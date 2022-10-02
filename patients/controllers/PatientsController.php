<?php

namespace patients\controllers;

use Yii;
use yii\bootstrap\Html;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\Appointment;
use common\models\Doctor;
use common\models\User;

class PatientsController extends \yii\web\Controller
{
	
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
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
	
	/**
	 * Sends an appointment (Creates it or modifies it) 
	 * you need to specify at least one of the two parameters doctor_id or appointment_id
	 * 
	 * @param integer | null $doctor_id The id of the doctor to send the appointment
	 * @param integer | null $appointment_id (optional) The Id of the appointment to modify
	 * @param string | null $dateTime (optional) The date and time to set to the appointment
	 * @return string[] The ajax response for Ajax Crud
	 */
	public function actionSendAppointment($doctor_id=null, $office_id=null, $appointment_id=null, $dateTime=null)
    {
    	$request = Yii::$app->request;
    	if ($appointment_id){// We are updating an existing appointment
    		$appointment = Appointment::findOne($appointment_id);
    		$doctor_id = $appointment->doctor_id;;
    	}
    	else {
    		
    		$appointment = new Appointment(['doctor_id' => $doctor_id]);
    	}
    	if ($dateTime){
    		$date = date('Y-m-d', strtotime($dateTime));
    		$start_time = date('H:i:s', strtotime($dateTime));
    		$appointment->date = $date;
    		$appointment->start_time = $start_time;
    		$appointment->calculateEndTime();
    	}
    	if ($office_id){
    		$appointment->office_id = $office_id;
    		$appointment->location_id = Appointment::LOCATION_TYPE_OFFICE.'-'.$office_id;
    	}
    	
    	$doctor = Doctor::findOne($doctor_id);
    	
    	if ($appointment->isNewRecord){
    		$appointment->price = $doctor->appointment_price ?: 0.00;
    		$appointment->currency_id = $doctor->currency_id?:1;
    	}
    	
    	if ($request->isAjax){
    		Yii::$app->response->format = Response::FORMAT_JSON;
    		
    		if ($request->isPost){
    			$patient = User::getUserIdentity()->patient;
    			$post = $request->post();
    			if ($appointment->load($post)){
    				$appointment->patient_id = $patient->id;
    				$appointment->status = Appointment::STATUS_REQUESTED;
    				if ($appointment->save()){
    					return [
    							'title'=> Yii::t('app', 'Send Appointment'),
    							'content' => Yii::t('app', 'Appointment Sent Successfully'),
    							'footer' => Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-secondary', 'data-dismiss' => "modal"])
    					];
    				}
    			}
    		}
    		
    		return [
    				
    				'title'=> Yii::t('app', "Send Appointment "),
    				'content'=>$this->renderAjax('send-appointment', [
    						'appointment' => $appointment,
    						'doctor' => $doctor,
    				]),
    				'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
    				' '.Html::button(Yii::t('app', 'Send'), ['class' => 'btn btn-primary', 'id' => 'pay_submit', 'type' => "submit"])
    		];
    	}
       
    }

}
