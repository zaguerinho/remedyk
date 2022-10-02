<?php

namespace enterprise\controllers;

use Yii;
use common\models\Comment;
use enterprise\models\search\CommentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * ModerationsController implements the CRUD actions for Comment model.
 */
class ModerationsController extends Controller
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
     * Lists all Comment models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Comment model.
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
            	if ($model->load($post) && $model->save()){
            		return [
            				'forceReload'=>'#crud-datatable-pjax',
            				'title'=> Yii::t('app', "Comment"),
            				'content'=>'<span class="text-success">'.Yii::t('app', 'Comment processed succesfully').'</span>',
            				'footer'=> Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"])
            		];   
            	}
            }
            
            return [
            		'title'=> Yii::t('app', "Comment"),
            		'content'=>$this->renderAjax('update', [
            				'model' => $model,
            		]),
            		'footer'=> Html::button('Close',['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).
            		Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
            ];   
            
        }
    }

    /**
     * Finds the Comment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
