<?php
namespace common\controllers;

use yii\bootstrap\Html;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\web\Controller;
use common\models\User;
use Yii;
use yii\web\Response;
use common\models\Notification;
use common\models\search\NotificationSearch;
use yii\web\NotFoundHttpException;

class NotificationsController extends Controller {
	
	public function behaviors()
	{
		return [
				/*'corsFilter' => [
					 'class' => Cors::className(),
					 'only' => ['ajax-clear-unseen-notifications', 'ajax-visit-link'],
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
										'allow' => true,
										'roles' => ['@'],
								],
								
								
						],
				]
		];
	}
	
	
	public function actionIndex(){
		$searchModel = new NotificationSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('@doctors/views/notifications/index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
		]);
	}
	
	public function actionDelete($id){
		if (Yii::$app->request->isAjax){
			Yii::$app->response->format = Response::FORMAT_JSON;
			
			$model = $this->findModel($id);
			if ($model->delete()){
				return [
						'forceReload'=> '#crud-datatable-pjax',
						'title' => Yii::t('app', 'Notification'),
						'content' => Yii::t('app', 'The Notification was deleted'),
						'footer' => Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"])
				];
			}
		}
	}
	
	
	public function actionAjaxClearUnseenNotifications(){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$user = User::getUserIdentity();
		$user->clearUnseenNotifications();
		return ['count' => $user->getUnseenNotificationsCount()];
	}
		
	public function actionAjaxVisitLink($id){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$notification = Notification::findOne($id);
		$notification->visited_at = date('Y-m-d H:i:s');
		$notification->save();
		return ['href' => $notification->target_url];
	}
	
	
	/**
	 * Finds the Notification model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Notification the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Notification::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}