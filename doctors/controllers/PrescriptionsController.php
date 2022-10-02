<?php

namespace doctors\controllers;

use Yii;
use common\helpers\ReportsHelper;
use common\models\BaseModel;
use common\models\Prescription;
use doctors\models\search\PrescriptionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Exception;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use common\models\PrescriptionDetail;
use common\models\Appointment;
use kartik\mpdf\Pdf;

/**
 * PrescriptionsController implements the CRUD actions for Prescription model.
 */
class PrescriptionsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
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
     * Lists all Prescription models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PrescriptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewPdf($id){
    	$prescription = $this->findModel($id);
    	$doctor = $prescription->doctor;
    	$patient = $prescription->patient;
    	$genders = ['M' => Yii::t('app', 'Male'), 'F' => Yii::t('app', 'Female')];
    	$address = $patient->address;

    	$prescriptionDetails = $prescription->prescriptionDetails;

    	$map = [
    			'{id}' => $prescription->id,
    			'{datetime}' => date('Y-m-d', strtotime($prescription->datetime)). ' ' .Yii::t('app', 'at'). ' ' .date('h:i a', strtotime($prescription->datetime)),
    			'{patient_name}' => $patient->user->name,
    			'{patient_gender}' => $genders[$patient->gender],
    			'{patient_weight}' => $patient->weight?:Yii::t('app', 'Unknown'),
    			'{patient_height}' => $patient->height?:Yii::t('app', 'Unknown'),
    			'{patient_blood}' => $patient->blood_type?:Yii::t('app', 'Unknown'),
    			'{patient_age}' => $patient->user->age. ' '.Yii::t('app', 'years old'),
    			'{patient_email}' => $patient->user->email,
    			'{patient_phone}' => $patient->user->getPhoneText(),
    			'{patient_address}' => $address?$address->toString():Yii::t('app', 'Not set'),
    			'{general_notes}' => $prescription->notes,
    			'{doctor_name}' => $doctor->user->name,
    			'{doctor_license}' => $doctor->license_number,
    			'{prescription_details}' => [],

    	];


    	foreach ($prescriptionDetails as $item){
    		$medicine = $item->medicine;
    		$map['{prescription_details}'][] = [
    				'{medicine_name}' => Json::decode($medicine->name, true)[Yii::$app->language].' / '. $item->grammage. ' '.Yii::t('app', 'grams'),
    				'{quantity}' => $item->quantity,
    				'{frequency}' => $item->frequency,
    				'{lapse}' => $item->lapse,
    				'{notes}' => $item->notes,
    		];
    	}

    	$title = Yii::t('app', 'Prescription #'. $prescription->id);
    	$pdf = ReportsHelper::getPdfObject('prescriptions', $map, Pdf::FORMAT_LETTER, Pdf::ORIENT_PORTRAIT, 'I', $title, false, false);
    	$pdf->getApi()->title = $title;
    	echo $pdf->render();
    	Yii::$app->end();
    }

    /**
     * Displays a single Prescription model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Prescription #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Prescription model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($appointment_id=null, $reload=false)
    {
        $request = Yii::$app->request;
        $doctor = Yii::$app->user->identity->doctor;
        $model = new Prescription();
        if (!is_null($appointment_id)){
        	$appointment = Appointment::findOne($appointment_id);
        	$patient = $appointment->patient;
        	$model->appointment_id = $appointment->id;

        	$model->patient_id = $patient->id;
        }
        $model->datetime = date('m/d/Y h:i a');
        $model->is_active = true;
        $model->doctor_id = $doctor->id;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            $prescriptionDetails = [new PrescriptionDetail()];


            if ($request->isPost && $model->load($request->post())){

            	$prescriptionDetails = BaseModel::createMultiple(PrescriptionDetail::classname());
            	BaseModel::loadMultiple($prescriptionDetails, Yii::$app->request->post());

            	$valid = $model->validate();
            	$valid = BaseModel::validateMultiple($prescriptionDetails) && $valid;

            	if ($valid){
            		$transaction = Yii::$app->db->beginTransaction();

            		try {
            			if ($flag = $model->save(false)) {
            				foreach ($prescriptionDetails as $prescriptionDetail) {
            					if ($prescriptionDetail->medicine_id){

            						$prescriptionDetail->prescription_id = $model->id;
            						if (! ($flag = $prescriptionDetail->save(false))) {
            							$transaction->rollBack();
            							break;
            						}
            					}
            				}
            			}
            			if ($flag){
            				$transaction->commit();
            				 $data = [
            						'title'=> Yii::t('app', "Create new Prescription"),
            						'content'=>'<span class="text-success">'.Yii::t('app', 'Created Prescription successfully').'</span>',
            						'footer'=> Html::button(Yii::t('app', 'Back'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
            						Html::a(Yii::t('app', 'Create More'), ['/prescriptions/create', 'appointment_id' => $appointment_id, 'reload' => $reload],['class'=>'btn btn-primary','role'=>'modal-remote'])

            				];

                            if ($reload) { $data['forceReload'] = '#crud-datatable-pjax'; }
                            return $data;
            			}
            		}
            		catch (Exception $e){
            			$transaction->rollBack();
            		}

            	}


            }
            return [
            		'title'=> Yii::t('app', "Create new Prescription"),
            		'content'=>$this->renderAjax('create', [
            				'model' => $model,
            				'prescriptionDetails' => $prescriptionDetails,
            		]),
            		'footer'=> Html::button(Yii::t('app', 'Back'), ['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
            		Html::button(Yii::t('app', 'Save'), ['class'=>'btn btn-primary','type'=>"submit"])

            ];
        }

    }

    /**
     * Updates an existing Prescription model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->datetime = date('m/d/Y h:i a', strtotime($model->datetime));
        $prescriptionDetails  = $model->prescriptionDetails;
        if (! $prescriptionDetails){
        	$prescriptionDetails = [new PrescriptionDetail()];
        }
        $doctor = Yii::$app->user->identity->doctor;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isPost && $model->load($request->post())){
            	$oldIDs = ArrayHelper::map($prescriptionDetails, 'id', 'id');
            	$prescriptionDetails = BaseModel::createMultiple(PrescriptionDetail::classname(), $prescriptionDetails);
            	BaseModel::loadMultiple($prescriptionDetails, Yii::$app->request->post());
            	$deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($prescriptionDetails, 'id', 'id')));

            	$valid = $model->validate();
            	$valid = BaseModel::validateMultiple($prescriptionDetails) && $valid;

            	if ($valid){
            		$transaction = Yii::$app->db->beginTransaction();
            		try {
            			if ($flag = $model->save(false)) {
            				if (! empty($deletedIDs)) {
            					PrescriptionDetail::deleteAll(['id' => $deletedIDs]);
            				}

            				foreach ($prescriptionDetails as $prescriptionDetail) {
            					if ($prescriptionDetail->medicine_id){

            						$prescriptionDetail->prescription_id = $model->id;
            						if (! ($flag = $prescriptionDetail->save(false))) {
            							$transaction->rollBack();
            							break;
            						}
            					}
            				}

            				if ($flag){
            					$transaction->commit();
            					return [
            							'forceReload'=>'#crud-datatable-pjax',
            							'title'=> Yii::t('app', "Prescription #").$id,
            							'content'=>$this->renderAjax('view', [
            									'model' => $model,
            									'prescriptionDetails' => $prescriptionDetails,
            							]),
            							'footer'=> Html::button(Yii::t('app', 'Back'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
            							Html::a(Yii::t('app', 'Edit'),['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            					];
            				}
            			}
            		}
            		catch (Exception $e){
            			$transaction->rollBack();
            		}

            	}
            }


            return [
            		'title'=> Yii::t('app', "Update Prescription #").$id,
            		'content'=>$this->renderAjax('update', [
            				'model' => $model,
            				'prescriptionDetails' => $prescriptionDetails,
            		]),
            		'footer'=> Html::button(Yii::t('app', 'Back'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
            		Html::button(Yii::t('app', 'Save'),['class'=>'btn btn-primary','type'=>"submit"])
            ];
        }
    }

    /**
     * Delete an existing Prescription model.
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
     * Delete multiple existing Prescription model.
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

    /**
     * Finds the Prescription model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Prescription the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Prescription::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
