<?php

namespace doctors\controllers;

use Yii;
use kartik\dialog\Dialog;
use common\models\Appointment;
use doctors\models\search\AppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii2fullcalendar\models\Event;
use common\models\Doctor;
use common\models\AdditionalService2appointment;
use yii\helpers\ArrayHelper;

/**
 * AppointmentsController implements the CRUD actions for Appointment model.
 */
class AppointmentsController extends Controller
{
	public function getViewPath(){
		return '@doctors/views/appointments';
	}

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
        	/*'corsFilter' => [
        		'class' => Cors::className(),
        		'only' => ['ajax-calendar-events'],
        		'cors' => [
        			// restrict access to
        			'Origin' => [Yii::$app->params['doctorsDomain'], Yii::$app->params['patientsDomain'], Yii::$app->params['adminDomain']],
        						
        			//'Access-Control-Request-Method' => ['POST', 'PUT'],
        			// Allow only POST and PUT methods
        			'Access-Control-Request-Headers' => ['X-Wsse', 'X-PJAX-Container', 'X-PJAX'],
        			// Allow only headers 'X-Wsse'
        			//'Access-Control-Allow-Credentials' => true,
        			// Allow OPTIONS caching
        			//'Access-Control-Max-Age' => 3600,
        			// Allow the X-Pagination-Current-Page header to be exposed to the browser.
        			//'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
        		],
        	],*/
        	'access' => [
        		'class' => AccessControl::className(),
        		'rules' => [
        			[
        				'allow' =>true,
        				'actions' => ['ajax-calendar-events']
        			],
        			[
        				'allow' => true,
        				'roles' => ['@'],
        			],
        				
        			
        		],
        	]
        ];
    }

    /**
     * Lists all Appointment models.
     * @return mixed
     */
    public function actionIndex($id=null)
    {    
        $searchModel = new AppointmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        	'appointment_id' =>$id,
        ]);
    }


    /**
     * Displays a single Appointment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $referrer = $request->referrer;
        
        if (!Yii::$app->user->isGuest && stripos($referrer, '/doctors/profile?id=') === false/*&& Yii::$app->user->identity->isDoctor()*/){
        	$optionalArray = ['forceReload'=>'#pjax-appointments',];
        }
        else {
        	$optionalArray = [];
        }
        
        $cancelMessage = Yii::t('app', "Cancelling will represent a charge if you already paid. Are you sure want to Cancel this Appointment?");
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isDoctor()){
        	$cancelMessage = Yii::t('app', "Cancelling will be penalized by Remedyk staff. Are you sure want to Cancel this Appointment?");
        }
        
        $footerButtons = '';
        switch ($model->status) {
        	case Appointment::STATUS_REQUESTED:
        		$footerButtons = ' ';
        		if ($model->changed_by != Yii::$app->user->identity->id){// Not changed by the current user
        			$footerButtons .= Html::a(Yii::t('app', 'Reject'), ['reject','id'=>$id], ['class' => 'btn btn-rejected', 'role' => 'modal-remote']).' '.
          			Html::a(Yii::t('app', 'Accept'), ['accept','id'=>$id], ['class' => 'btn btn-accepted', 'role' => 'modal-remote']).' ';
            		
        		}
        		else {
        			$footerButtons .= Html::a(Yii::t('app', 'Cancel'), ['cancel','id'=>$id], ['class' => 'btn btn-cancelled', 'role' => 'modal-remote',
        					'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
        					'data-confirm-title'=> Yii::t('app', 'Are you sure?'),
        					'data-confirm-message'=> $cancelMessage,
        					'data-confirm-ok' => Yii::t('app', 'Cancel Anyway'),
        					'data-confirm-cancel' => Yii::t('app', 'Back')
        			]).' ';
        		}
        		$footerButtons .= Html::a(Yii::t('app', 'Change'),['/patients/send-appointment','appointment_id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote']);
        		break;
        	case Appointment::STATUS_ACCEPTED:
        	case Appointment::STATUS_CONFIRMED_BY_DOCTOR:
        		$footerButtons = ' ';
        		if ($model->changed_by != Yii::$app->user->identity->id){// Not changed by the current user
        			$footerButtons .= Html::a(Yii::t('app', 'Confirm'), ['confirm','id'=>$id], ['class' => 'btn btn-confirmed', 'role' => 'modal-remote']).' ';
        		}
        		$footerButtons .=
        		Html::a(Yii::t('app', 'Cancel'), ['cancel','id'=>$id], ['class' => 'btn btn-cancelled', 'role' => 'modal-remote',
        				'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
        				
        				'data-confirm-title'=> Yii::t('app', 'Are you sure?'),
        				'data-confirm-message'=> $cancelMessage,
        				'data-confirm-ok' => Yii::t('app', 'Cancel Anyway'),
        				'data-confirm-cancel' => Yii::t('app', 'Back')
        				
        		]).' '.            		
              		Html::a(Yii::t('app', 'Change'),['/patients/send-appointment','appointment_id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote']);
        		break;
        	case Appointment::STATUS_CONFIRMED:
        		$footerButtons = ' '.
          			Html::a(Yii::t('app', 'Cancel'), ['cancel','id'=>$id], ['class' => 'btn btn-cancelled', 'role' => 'modal-remote',
								'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
								'data-confirm-title'=> Yii::t('app', 'Are you sure?'),
								'data-confirm-message'=> $cancelMessage,
								'data-confirm-ok' => Yii::t('app', 'Cancel Anyway'),
								'data-confirm-cancel' => Yii::t('app', 'Back')
					]);            		
        		break;
        	
        	
        }
        
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ArrayHelper::merge($optionalArray, [
            		
                    'title'=> Yii::t('app', "Appointment "),
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
            		$footerButtons
                ]);    
        }else{
            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionConfirm($id){
    	if (Yii::$app->request->isAjax){
    		Yii::$app->response->format = Response::FORMAT_JSON;
    		$model = $this->findModel($id);
    		$model->status = Appointment::STATUS_CONFIRMED_BY_DOCTOR;
    		$model->confirmation_datetime = date('Y-m-d H:i:s');
    		if ($model->save()) {
    			return $this->actionView($id);
    		}
    	}
    	
    }
    
    public function actionCancel($id){
    	if (Yii::$app->request->isAjax){
    		Yii::$app->response->format = Response::FORMAT_JSON;
    		$model = $this->findModel($id);
    		$model->status = Appointment::STATUS_CANCELLED;
    		$model->cancel_datetime = date('Y-m-d H:i:s');
    		if ($model->save()) {
    			return $this->actionView($id);
    		}
    	}
    	
    }
    
    public function actionReject($id){
    	if (Yii::$app->request->isAjax){
    		Yii::$app->response->format = Response::FORMAT_JSON;
    		$model = $this->findModel($id);
    		$model->status = Appointment::STATUS_REJECTED;
    		if ($model->save()) {
    			return $this->actionView($id);
    		}
    	}
    	
    }
    
    public function actionAccept($id){
    	if (Yii::$app->request->isAjax){
    		Yii::$app->response->format = Response::FORMAT_JSON;
    		$model = $this->findModel($id);
    		$model->status = Appointment::STATUS_ACCEPTED;
    		if ($model->save()) {
    			return $this->actionView($id);
    		}
    	}
    	
    }

    public function actionDelete($id){
    	if (Yii::$app->request->isAjax){
    		Yii::$app->response->format = Response::FORMAT_JSON;
    		$model = $this->findModel($id);
    		if ($model->delete()){
    			return [
    					'title' => Yii::t('app', 'Appointment'),
    					'content' => Yii::t('app', 'The appointment was canceled'),
    					'footer' => Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"])
    			];
    		}
    	}
    }
    
    public function actionAjaxCalendarEvents($doctor_id=null, $patient_id=null, $office_id=null, $start=null, $end=null, $_=null, $accepted=null, $cancelled=null, $requested=null, $confirmed=null, $rejected=null, $month=null, $year=null){
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	if ($doctor_id == null){
    		$doctor_id = Yii::$app->user->identity->doctor->id;
    	}
    	$events = [];
    	$statuses = [1, 2, 3, 4, 5, 6];
    	if (!is_null($accepted)){ //Means will come all statuses
    		$statuses = [Appointment::STATUS_OPEN];
    		if ($accepted == "true") {$statuses[] = Appointment::STATUS_ACCEPTED; $statuses[] = Appointment::STATUS_CONFIRMED_BY_DOCTOR; }
    		if ($cancelled == "true") $statuses[] = Appointment::STATUS_CANCELLED;
    		if ($requested == "true") $statuses[] = Appointment::STATUS_REQUESTED;
    		if ($confirmed == "true") $statuses[] = Appointment::STATUS_CONFIRMED;
    		if ($rejected == "true") $statuses[] = Appointment::STATUS_REJECTED;
    	}
    	$appointments = Appointment::find()->where(['doctor_id' => $doctor_id, 'status' => $statuses])->all();
    	foreach ($appointments as $appointment){
    		/* @var $appointment \common\models\Appointment */
    		$event = new Event();
    		$event->id = $appointment->id;
    		$event->start = date('Y-m-d\TH:i:s', strtotime($appointment->date . 'T' . $appointment->start_time));
    		$event->end = date('Y-m-d\TH:i:s', strtotime($appointment->date . 'T' . $appointment->end_time));
    		
    		if ($patient_id != null && $appointment->patient_id != $patient_id){//Not your event (you can't see it)
    			$event->className = 'secondary text-bold';
    			$event->textColor = '#FFFFFF';
    			$event->title = Yii::t('app', 'Not Available');
    			$event->rendering = 'background';
    			
    		}
    		else {
	    		$event->color = '#FFFFFF';
	    		switch ($appointment->status){
	    			case Appointment::STATUS_ACCEPTED:
	    				$event->title = Yii::t('app', 'Accepted');
	    				$event->className = 'text-accepted text-bold';
	    				$event->url = Url::to(['/appointments/view', 'id' => $appointment->id]);
	    				break;
	    			case Appointment::STATUS_CONFIRMED_BY_DOCTOR:
	    				$event->title = Yii::t('app', 'Confirmed by Doctor');
	    				$event->className = 'text-accepted text-bold';
	    				$event->url = Url::to(['/appointments/view', 'id' => $appointment->id]);
	    				break;
	    			case Appointment::STATUS_CANCELLED:
	    				$event->title = Yii::t('app', 'Cancelled');
	    				$event->className = 'text-cancelled text-bold';
	    				break;
	    			case Appointment::STATUS_REQUESTED:
	    				$event->title = Yii::t('app', 'Requested');
	    				$event->className = 'text-requested text-bold';
	    				$event->url = Url::to(['/appointments/view', 'id' => $appointment->id]);
	    				break;
	    			case Appointment::STATUS_CONFIRMED:
	    				$event->title = Yii::t('app', 'Confirmed');
	    				$event->className = 'text-confirmed text-bold';
	    				$event->url = Url::to(['/appointments/view', 'id' => $appointment->id]);
	    				break;
	    			case Appointment::STATUS_REJECTED:
	    				$event->title = Yii::t('app', 'Rejected');
	    				$event->className = 'text-rejected text-bold';
	    				$event->url = Url::to(['/appointments/view', 'id' => $appointment->id]);
	    				break;
	    			case Appointment::STATUS_OPEN:
	    				$event->title = Yii::t('app', 'Open');
	    				$event->className = 'text-open text-bold';
	    				
	    				break;
	    		}
    		}
    		
    		
    		/*
    		 * backgroundColor
    		 * borderColor
    		 * textColor 
    		 * title
    		 * calssName
    		 * allDay (true or false)
    		 */
    		$events[] = $event;
    	}
    	
    	// Not working hours
    	$doctor = Doctor::findOne($doctor_id);
    	$notWorkingHours = $doctor->getUnavailableHours($month, $year, $office_id);
    	foreach ($notWorkingHours as $day => $ranges){
    		foreach ($ranges as $range){
    			$event = new Event();
    			$event->id = $year.$month.$day.$range[0];
    			$event->start = date('Y-m-d\TH:i:s', strtotime($year.'-'.$month.'-'.$day.'T'.$range[0]));
    			$event->end = date('Y-m-d\TH:i:s', strtotime($year.'-'.$month.'-'.$day.'T'.$range[1]));
    			
    			$event->className = 'secondary text-bold';
    			$event->textColor = '#FFFFFF';
    			$event->title = Yii::t('app', 'Not available');
    			$event->rendering = 'background';
    			
    			$events[] = $event;
    		}
    	}
    	return $events;
    }

    /**
     * Finds the Appointment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Appointment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Appointment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
