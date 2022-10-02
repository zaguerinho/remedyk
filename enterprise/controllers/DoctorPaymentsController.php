<?php

namespace enterprise\controllers;

use Yii;
use common\models\DoctorPayment;
use common\models\User;
use enterprise\models\search\DoctorPaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Exception;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use common\models\Commission;

/**
 * DoctorPaymentsController implements the CRUD actions for DoctorPayment model.
 */
class DoctorPaymentsController extends Controller
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
     * Lists all DoctorPayment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DoctorPaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single DoctorPayment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "DoctorPayment #".$id,
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

    public function actionViewInvoice($id){
    	$model = $this->findModel($id);
    	Yii::$app->response->sendFile($model->downloadInvoiceUrl, $model->invoice_name);
    }
    public function actionViewReceipt($id){
    	$model = $this->findModel($id);
    	Yii::$app->response->sendFile($model->downloadReceiptUrl, $model->receipt_name);
    }

    /**
     * Creates a new DoctorPayment model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new DoctorPayment();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($request->isPost){
            	$post = $request->post();
            	if ($model->load($post) && $model->validate()){
            		$transaction = Yii::$app->db->beginTransaction();
            		try {
            			$model->status = DoctorPayment::STATUS_INVOICE_REQUEST;
            			$model->user_id = User::getUserIdentity()->id;
            			$valid = $model->save(false);
            			$valid = $valid && $this->updateCommissions($model->id, $post);
            			if ($valid){
            				$transaction->commit();
            				return [
            						'forceReload'=>'#crud-datatable-pjax',
            						'title'=> Yii::t('app', "Create new Doctor Payment"),
            						'content'=>'<span class="text-success">'.Yii::t('app', 'Create Doctor Payment success').'</span>',
            						'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
            						Html::a(Yii::t('app', 'Create More'),['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])

            				];
            			}
            			$transaction->rollBack();
            		}
            		catch (Exception $e){
            			$transaction->rollBack();
            		}
            	}
            }

            return [
            		'title'=> Yii::t('app', "Create new Doctor Payment"),
            		'content'=>$this->renderAjax('create', [
            				'model' => $model,
            		]),
            		'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).' '.
            		Html::button(Yii::t('app', 'Create'),['class'=>'btn btn-primary','type'=>"submit"])

            ];
        }
    }


    public function actionProcessPending($id){
    	$model = $this->findModel($id);
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	if (Yii::$app->request->isPost){
    		$post = Yii::$app->request->post();
    		if ($_FILES && $_FILES['DoctorPayment'] && $_FILES['DoctorPayment']['tmp_name'] && $_FILES['DoctorPayment']['type']['receiptBase64data']){
    			$model->status = DoctorPayment::STATUS_PAID;
    			$model->paid_on = date('Y-m-d H:i:s');
    			if ($model->save()){
    				return [
    						'forceReload'=>'#crud-datatable-pjax',
    						'title'=> Yii::t('app', "Process pending Doctor Payment"),
    						'content'=>'<span class="text-success">'.Yii::t('app', 'Payment was processed successfully').'</span>',
    						'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"])

    				];
    			}

    		}
    		Yii::$app->session->setFlash('error', Yii::t('app', 'You must upload your payment evidence'));
    		$model->addError('receiptBase64data', Yii::t('app', 'You must upload your payment evidence'));
    	}
    	return [
    			'title'=> Yii::t('app', "Process pending Doctor Payment"),
    			'content'=>$this->renderAjax('process-pending', [
    					'model' => $model,
    			]),
    			'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
    			Html::button(Yii::t('app', 'Pay'),['class'=>'btn btn-primary','type'=>"submit"])
    	];

    }

    public function actionAjaxLoadCommissions($doctor_id, $currency_id, $doctor_payment_id=null){
    	Yii::$app->response->format = Response::FORMAT_JSON;

    	if (!$doctor_id || !$currency_id){
    		return [];
    	}
    	if ($doctor_payment_id){
    		$query = Commission::find()->where(['or', ['commission.doctor_payment_id' => $doctor_payment_id], ['commission.doctor_payment_id' => null]]);
    	}
    	else {
    		$query = Commission::find()->where(['commission.doctor_payment_id' => null]);
    	}

    	$query->leftJoin('appointment', 'appointment.id = commission.appointment_id');
    	$query->andWhere(['appointment.doctor_id' => $doctor_id])
    	->andWhere(['appointment.currency_id' => $currency_id])
    	->andWhere(['commission.status' => [Commission::STATUS_INVOICE_REQUEST, Commission::STATUS_PENDING_PAYMENT]]);

    	$commissions = $query->all();

    	$result  = [];
    	foreach ($commissions as $commission){
    		/* @var \common\models\Commission $commission */
    		$appointment = $commission->appointment;
    		$commission_percent = $appointment->doctor->getMembership()->commission_percent;
    		$commission_amount = ($appointment->price)*$commission_percent;
    		$currency = $appointment->currency->code;
    		$item = [];
    		$item['id'] = $commission->id;
    		$item['appointment_id'] = $appointment->id;
    		$item['patient'] = $appointment->patient->user->name;
    		$item['selected'] = ($commission->doctor_payment_id != null);
    		$item['appointment_date'] = date('M-d-Y', strtotime($appointment->date));
    		$item['payment_date'] = date('M-d-Y', strtotime($commission->paid_on));
    		$item['appointment_price'] = number_format($appointment->price, 2).' '.$currency;
    		$item['paid_amount'] = number_format($commission->amount, 2).' '.$currency;
    		$item['paid_percent'] = ($commission->percent * 100).'%';
    		$item['currency'] = $currency;
    		$item['commission_percent'] = ($commission_percent*100).'%';
    		$item['commission_amount'] = number_format($commission_amount, 2).' '.$currency;
    		$item['pay_to_doctor'] = /*($appointment->price)*/($commission->amount) - $commission_amount;
    		$item['pay_to_doctor_text'] = number_format($item['pay_to_doctor'], 2).' '.$currency;

    		$result[] = $item;
    	}
    	return $result;

    }

    public function updateCommissions($doctor_payment_id, $post){
    	$flag = true;
    	$commissions = Commission::find()->where(['or', ['commission.doctor_payment_id' => $doctor_payment_id], ['commission.doctor_payment_id' => null]])->all();
    	foreach ($commissions as $commission){
    		if (isset($post['commission'][$commission->id]) && $post['commission'][$commission->id] == 'on'){
    			$commission->status = Commission::STATUS_INVOICE_REQUEST;
    			$commission->doctor_payment_id = $doctor_payment_id;
    		}
    		else {
    			$commission->status = Commission::STATUS_PENDING_PAYMENT;
    			$commission->doctor_payment_id = null;
    		}
    		$flag = $flag && $commission->save();
    	}
    	return $flag;
    }

    /**
     * Updates an existing DoctorPayment model.
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

            if ($request->isPost){
            	$post = $request->post();
            	if ($model->load($post) && $model->validate()){
            		$transaction = Yii::$app->db->beginTransaction();
            		try {
            			$model->status = DoctorPayment::STATUS_INVOICE_REQUEST;
            			$model->user_id = User::getUserIdentity()->id;
            			$valid = $model->save(false);
            			$valid = $valid && $this->updateCommissions($model->id, $post);
            			if ($valid){
            				$transaction->commit();
            				return [
            						'forceReload'=>'#crud-datatable-pjax',
            						'title'=> Yii::t('app', "Update Doctor Payment #").$id,
            						'content'=>'<span class="text-success">'.Yii::t('app', 'Updated Doctor Payment successfully').'</span>',
            						'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"])
            				];
            			}
            			$transaction->rollBack();
            		}
            		catch (Exception $e){
            			$transaction->rollBack();
            		}
            	}
            }
            return [
            		'title'=> Yii::t('app', "Update Doctor Payment #").$id,
            		'content'=>$this->renderAjax('update', [
            				'model' => $model,
            		]),
            		'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
            		Html::button(Yii::t('app', 'Update'),['class'=>'btn btn-primary','type'=>"submit"])
            ];

        }
    }



    /**
     * Delete an existing DoctorPayment model.
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
     * Delete multiple existing DoctorPayment model.
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
     * Finds the DoctorPayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DoctorPayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DoctorPayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
