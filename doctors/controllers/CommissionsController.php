<?php
	
	namespace doctors\controllers;
	
	use Yii;
	use common\models\Commission;
	use doctors\models\search\CommissionSearch;
	use yii\web\Controller;
	use yii\web\NotFoundHttpException;
	use yii\filters\VerbFilter;
	
	/**
	 * CommissionsController implements the CRUD actions for Commission model.
	 */
	class CommissionsController extends Controller{
		
		/**
		 * @inheritdoc
		 */
		public function getViewPath(){
			return '@doctors/views/commissions';
		}
		
		/**
		 * @inheritdoc
		 */
		public function behaviors(){
			return [
				'verbs' => [
					'class'   => VerbFilter::className(),
					'actions' => [
						'delete'      => ['post'],
						'bulk-delete' => ['post'],
					],
				],
			];
		}
		
		/**
		 * Lists all Commission models.
		 *
		 * @return mixed
		 */
		public function actionIndex(){
			$searchModel  = new CommissionSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			
			return $this->render('index',
				[
					'searchModel'  => $searchModel,
					'dataProvider' => $dataProvider,
				]);
		}
		
		
		/**
		 * Finds the Commission model based on its primary key value.
		 * If the model is not found, a 404 HTTP exception will be thrown.
		 *
		 * @param integer $id
		 *
		 * @return Commission the loaded model
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		protected function findModel($id){
			if(($model = Commission::findOne($id)) !== null){
				return $model;
			}
			else{
				throw new NotFoundHttpException('The requested page does not exist.');
			}
		}
	}
