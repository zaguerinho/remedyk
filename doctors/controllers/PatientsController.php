<?php

namespace doctors\controllers;

use Yii;
use common\models\Patient;
use doctors\models\search\PatientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use common\models\Appointment;
use common\models\ClinicalStory;
use common\models\User;
use yii\data\ActiveDataProvider;

use yii\base\Exception;
use common\models\Procedure2doctor;
use common\models\ClinicalStoryAttachment;

use common\models\Commission;

/**
 * PatientsController implements the CRUD actions for Patient model.
 */
class PatientsController extends Controller
{
	public function getViewPath(){
		return '@doctors/views/patients';
	}
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Patient models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new PatientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single Patient model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $action=null)
    {   
    	$checks = Yii::$app->request->get();
    	if (isset($checks['id'])){
    		unset($checks['id']);
    	}
    	if (isset($checks['action'])){
    		unset($checks['action']);
    	}
    	
    	$model = $this->findModel($id);
    	$doctor = Yii::$app->user->identity->doctor;
    	
    	$clinicalStory = new ClinicalStory();
    	
    	if (!is_null($action) && !(Yii::$app->request->isPost)){
    		switch ($action){
    			case Appointment::STATUS_OPEN:
    				$appointment = $model->getNextAppointment();
    				if ($appointment->status != Appointment::STATUS_CONFIRMED){
    					Yii::$app->session->setFlash('error', Yii::t('app', 'The appointment needs to be confirmed before you can open it'));
    				}
    				else{
    					$appointment->status = $action;
    					$appointment->save();
    				}
    				break;
    			case Appointment::STATUS_CLOSED:
    				$appointment = $model->getOpenAppointment();
    				/* @var \common\models\Appointment $appointment */
    				if (!$appointment){
    					
    				}
    				elseif (!$appointment->clinicalStories){
    					Yii::$app->session->setFlash('error', Yii::t('app', 'There are no reords written for this appointment, there should be at least one'));
    				}
    				else {
    					$appointment->status = $action;
    					$appointment->save();
    					$commissions = $appointment->commissions;
    					foreach ($commissions as $commission){
    						$commission->status =  Commission::STATUS_PENDING_PAYMENT;
    						$commission->save();
    					}
    				}
    				break;
    		}
    	}
    	
    	$status = $model->getAppointmentStatus();
    	
    	$story_types = [];
    	if (count($checks) > 0){
    		
    		foreach ($checks as $key => $value){
    			if ($value == 'true'){
    				$story_type_id = explode('_', $key)[2];
    				$story_types[] = $story_type_id;
    			}
    		}
    	}
    	if (!$status){
    		$where = '0=1';
    	}
    	elseif ($status == Appointment::STATUS_CONFIRMED || $status == Appointment::STATUS_OPEN|| User::getUserIdentity()->isStaff()){
    		
    		$where = ['patient_id' => $model->id];
    	}
    	else {
    		$where = ['patient_id' => $model->id, 'doctor_id' => $doctor->id];
    	}
    	
    	if ($where != '0=1' && count($story_types) > 0){
    		$where['clinical_story_type_id'] = $story_types;
    	}
    	
    	
    	$query = ClinicalStory::find()->where($where);
    	$dataProvider = new ActiveDataProvider(['query' => $query]);
    	
    	if (Yii::$app->request->isPost){
    		$post = Yii::$app->request->post();
    		if ($clinicalStory->load($post)){
    			try {
    				
    				$appointment = $model->getOpenAppointment();    				
    				
    				$transaction = Yii::$app->db->beginTransaction();
    				
    				$clinicalStory->appointment_id = $appointment->id;
    				$clinicalStory->patient_id = $model->id;
    				$clinicalStory->doctor_id = $doctor->id;
    				$clinicalStory->registered_on = date('Y-m-d');
    				
    				$valid = $clinicalStory->save();
    				
    				
    				$specialty_id = $post['Procedure2doctor']['specialty_id'] ?: null;
    				$procedure_id = $post['Procedure2doctor']['procedure_id'] ?: null;
    				$procedure2doctor = Procedure2doctor::find()->where(['specialty_id' => $specialty_id, 'procedure_id' => $procedure_id])->one();
    				if (!$procedure2doctor){
    					$appointment->procedure2doctor_id = null;
    					$valid = $valid && $appointment->save();
    				}
    				elseif ($procedure2doctor->id != $appointment->procedure2doctor_id){
    					$appointment->procedure2doctor_id = $procedure2doctor->id;
    					$valid = $valid && $appointment->save();
    				}
    				if ($valid){
    					$flag = true;
    					// Save the attachements
    					if (isset($post['attachment'])){
	    					foreach ($post['attachment'] as $index => $attachment){
	    						$filename = $post['attachment_name'][$index];
	    						$story_attachment = new ClinicalStoryAttachment([
	    								'base64data' => $attachment,
	    								'name' => $filename,
	    								'clinical_story_id' => $clinicalStory->id,
	    						]);
	    						$flag = $flag && $story_attachment->save();
	    					}
    					}
    					
    					if ($flag){
    						$transaction->commit();
    						return $this->redirect(['view', 'id' => $id]);
    					}
    				}
    				$transaction->rollBack();
    			}
    			catch (Exception $e){
    				$transaction->rollBack();
    			}
    		}
    	}
    	
        return $this->render('view', [
        	'model' => $model,
            'clinicalStory' => $clinicalStory,
        	'dataProvider' => $dataProvider,
        ]);
        
    }

    /**
     * Creates a new Patient model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Patient();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Patient",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Patient",
                    'content'=>'<span class="text-success">Create Patient success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new Patient",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }
    
    public function actionSendAppointment($id=null, $appointment_id=null){
    	
    	
    	if ($id != null){
    		$patient = $this->findModel($id);
    		$appointment = $patient->getNextAppointment();
    	}
    	else {
    		$patient = null;
    		$appointment = new Appointment();
    	}
    	
    	if ($appointment_id!=null){
    		$appointment = Appointment::findOne($appointment_id);
    		$patient = $appointment->patient;
    	}
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	$doctor = Yii::$app->user->identity->doctor;
    	/* @var $doctor \common\models\Doctor */
    	
    	if (!$doctor->offices) {
    		return [
    				'title'=> Yii::t('app', 'Send Appointment'),
    				'content' => Yii::t('app', 'You need to configure your offices and hours first or you will not be able to send appointments'),
    				'footer' => '<div class="row"><div class="col-xs-12">'.Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-secondary', 'data-dismiss' => "modal"]).
    				' '.Html::a(Yii::t('app', 'Configure Now'), ['/doctors/profile', 'page' => DoctorsController::OFFICES], ['class' => 'btn btn-primary']).'</div></div>'
    		];
    	}
    	if (!$doctor->getFirstAvailable(null, 1)){
    		return [
    				'title'=> Yii::t('app', 'Send Appointment'),
    				'content' => Yii::t('app', 'You need to configure your working hours and prices first or you will not be able to send appointments'),
    				'footer' => '<div class="row"><div class="col-xs-12">'.Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-secondary', 'data-dismiss' => "modal"]).
    				' '.Html::a(Yii::t('app', 'Configure Now'), ['/doctors/profile', 'page' => DoctorsController::HOURS], ['class' => 'btn btn-primary']).'</div></div>'
    		];
    	}
    	
    	if (!$doctor->patients){
    		return [
    				'title'=> Yii::t('app', 'Send Appointment'),
    				'content' => Yii::t('app', 'You don\'t have any patients yet. You can only send appointments to your patients (those who have had at least one appointment with you)'),
    				'footer' => '<div class="row"><div class="col-xs-12">'.Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-secondary', 'data-dismiss' => "modal"]).'</div></div>'
    		];
    	}
    	
    	if ($appointment->isNewRecord){
    		if ($patient)
    			$appointment->patient_id = $patient->id;
    		$appointment->doctor_id = $doctor->id;
    		$appointment->price = $doctor->appointment_price;
    		$appointment->currency_id = $doctor->currency_id;
    	}
    	
    	
    	if (Yii::$app->request->isPost){
    		$post = Yii::$app->request->post();
    		if (isset($post['Appointment']['id'])){
    			$appointment = Appointment::findOne($post['Appointment']['id']);
    			$patient = $appointment->patient;
    		}
    		elseif (!$patient && isset($post['Appointment']['patient_id'])){
    			$patient = $this->findModel($post['Appointment']['patient_id']);
    			$appointment = $patient->getNextAppointment();
    		}
    		
    			
    		if ($appointment->load($post)){
    			$appointment->doctor_id = $doctor->id;
    			if ($patient)
    				$appointment->patient_id = $patient->id;
    			
    			$appointment->status = Appointment::STATUS_REQUESTED;
    			if ($appointment->save()){
    				
    				return [
    						'title'=> Yii::t('app', 'Send Appointment'),
    						'content' => Yii::t('app', 'Appointment Sent Successfully'),
    						'footer' => '<div class="row"><div class="col-xs-12">'.Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-secondary', 'data-dismiss' => "modal"]).'</div></div>'
    				];
    			}
    		}
    	}
    	
    	$appointment->date = date('m/d/Y', strtotime($appointment->date));
    	return [
    			'title'=> Yii::t('app', 'Send Appointment'),
    			'content'=>$this->renderAjax('/modals/_send_appointment', [
    					'appointment' => $appointment,    					
    			]),
    			'footer'=> '<div class="row"><div class="col-xs-12">'.Html::button(Yii::t('app', 'Back'), ['class' => 'btn btn-secondary', 'data-dismiss' => "modal"]).
    			' '.Html::button(Yii::t('app', 'Send'), ['class' => 'btn btn-primary', 'type' => "submit"]).'</div></div>'
    			
    	];       
    }
    
    public function actionAjaxRecalculateTimes($location_id){
    	$doctor = User::getUserIdentity()->doctor;
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	$office_id = $location_id ? explode('-', $location_id)[1] : null;
    	$availableTimes = $doctor->getFirstAvailable($office_id);
    	$appointmentTimes = [];
    	$duration = $doctor->appointment_duration;
    	foreach ($availableTimes as $time){
    		$date = date('m/d/Y', strtotime($time));
    		$startTime = date('h:i A', strtotime($time));
    		$hours = date('H', strtotime($duration));
    		$minutes = date('i', strtotime($duration));
    		$endTime = date('h:i A', strtotime($startTime." +{$hours} hours +{$minutes} minutes"));
    		$appointmentTimes[$time] = $date.' ('.$startTime.' - '.$endTime.')';
    	}
    	
    	return $appointmentTimes;
    }

    /**
     * Updates an existing Patient model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Patient #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Patient #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update Patient #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    	
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing Patient model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

     /**
     * Delete multiple existing Patient model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
       
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

    /**
     * Finds the Patient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Patient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Patient::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
