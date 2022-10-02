<?php

namespace doctors\controllers;

use Yii;
use common\models\Address;
use common\models\OperatingRoom;
use doctors\models\search\OperatingRoomSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * OperatingRoomsController implements the CRUD actions for OperatingRoom model.
 */
class OperatingRoomsController extends Controller
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
     * Lists all OperatingRoom models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new OperatingRoomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single OperatingRoom model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
    	$model = $this->findModel($id);
    	$address = $model->address ? $model->address : new Address();
    	$request = Yii::$app->request;
    	if($request->isAjax){
    		Yii::$app->response->format = Response::FORMAT_JSON;
    		return [
    				'title'=> Yii::t('app', "View Operating Room Details"),
    				'content'=>$this->renderAjax('view', [
    						'model' => $model,
    						'address' => $address,
    				]),
    				'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
    				Html::a(Yii::t('app', 'Update'),['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
    		];
    	}else{
    		return $this->render('view', [
    				'model' => $this->findModel($id),
    		]);
    	}
    }

    /**
     * Creates a new OperatingRoom model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    	$request = Yii::$app->request;
    	$model = new OperatingRoom(['is_active' => 1]);
    	$address = new Address();
    	$doctor = Yii::$app->user->identity->doctor;
    	if($request->isAjax){
    		/*
    		 *   Process for ajax request
    		 */
    		Yii::$app->response->format = Response::FORMAT_JSON;
    		
    		if ($request->isPost){
    			if ($model->load($request->post()) && $model->validate()){
    				$transaction = Yii::$app->db->beginTransaction();
    				$valid = $address->load($request->post()) && $address->save();
    				$model->address_id = $address->id;
    				$model->doctor_id = $doctor->id;
    				$valid = $valid && $model->save(false);
    				if ($valid){
    					$transaction->commit();
    					return [
    							'forceReload'=>'#operating-rooms-crud-datatable-pjax',
    							'title'=> Yii::t('app', "Add new Operating Room"),
    							'content'=>'<span class="text-success">'.Yii::t('app', 'Operating Room added successfully').'</span>',
    							'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
    							Html::a(Yii::t('app', 'Create More'),['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
    							
    					];
    				}
    				$transaction->rollBack();
    				
    			}
    		}
    		return [
    				'title'=> Yii::t('app', "Add new Operating Room"),
    				'content'=>$this->renderAjax('create', [
    						'model' => $model,
    						'address' => $address,
    				]),
    				'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
    				Html::button(Yii::t('app', 'Save'),['class'=>'btn btn-primary','type'=>"submit"])
    		];
    		
    	}
       
    }

    /**
     * Updates an existing OperatingRoom model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
    	$request = Yii::$app->request;
    	$model = $this->findModel($id);
    	
    	$address = $model->address ? $model->address : new Address();
    	$doctor = Yii::$app->user->identity->doctor;
    	if($request->isAjax){
    		/*
    		 *   Process for ajax request
    		 */
    		Yii::$app->response->format = Response::FORMAT_JSON;
    		
    		if ($request->isPost){
    			if ($model->load($request->post()) && $model->validate()){
    				$transaction = Yii::$app->db->beginTransaction();
    				$valid = $address->load($request->post()) && $address->save();
    				$model->address_id = $address->id;
    				$model->doctor_id = $doctor->id;
    				$valid = $valid && $model->save(false);
    				if ($valid){
    					$transaction->commit();
    					return [
    							'forceReload'=>'#operating-rooms-crud-datatable-pjax',
    							'title'=> Yii::t('app', "View Operating Room Details"),
    							'content'=>$this->renderAjax('view', [
    									'model' => $model,
    									'address' => $address,
    							]),
    							'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
    							Html::a(Yii::t('app', 'Update'),['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
    							
    					];
    				}
    				$transaction->rollBack();
    				
    			}
    		}
    		return [
    				'title'=> Yii::t('app', "Update Operating Room Data"),
    				'content'=>$this->renderAjax('update', [
    						'model' => $model,
    						'address' => $address,
    				]),
    				'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
    				Html::button(Yii::t('app', 'Save'),['class'=>'btn btn-primary','type'=>"submit"])
    		];
    		
    	}
    }

    /**
     * Delete an existing OperatingRoom model.
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
            return ['forceClose'=>true,'forceReload'=>'#operating-rooms-crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

     /**
     * Delete multiple existing OperatingRoom model.
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
            return ['forceClose'=>true,'forceReload'=>'#operating-roomscrud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
       
    }

    /**
     * Finds the OperatingRoom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OperatingRoom the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OperatingRoom::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
