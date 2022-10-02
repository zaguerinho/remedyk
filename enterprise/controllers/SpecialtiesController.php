<?php
	
	namespace enterprise\controllers;
	
	use Yii;
	use common\models\Specialty;
	use common\models\search\SpecialtySearch;
	use yii\web\Controller;
	use yii\web\NotFoundHttpException;
	use yii\filters\VerbFilter;
	use \yii\web\Response;
	use yii\helpers\Html;
	
	/**
	 * SpecialtyController implements the CRUD actions for Specialty model.
	 */
	class SpecialtiesController extends Controller{
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
		 * Lists all Specialty models.
		 *
		 * @return mixed
		 */
		public function actionIndex(){
			$searchModel  = new SpecialtySearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			
			return $this->render('index',
				[
					'searchModel'  => $searchModel,
					'dataProvider' => $dataProvider,
				]);
		}
		
		
		/**
		 * Displays a single Specialty model.
		 *
		 * @param integer $id
		 *
		 * @return mixed
		 */
		public function actionView($id){
			$request = Yii::$app->request;
			if($request->isAjax){
				Yii::$app->response->format = Response::FORMAT_JSON;
				
				return [
					'title'   => "Specialty #" . $id,
					'content' => $this->renderAjax('view',
						[
							'model' => $this->findModel($id),
						]),
					'footer'  => Html::button('Close',
							['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) . Html::a('Edit',
							['update', 'id' => $id],
							['class' => 'btn btn-primary', 'role' => 'modal-remote']),
				];
			}
			else{
				return $this->render('view',
					[
						'model' => $this->findModel($id),
					]);
			}
		}
		
		/**
		 * Creates a new Specialty model.
		 * For ajax request will return json object
		 * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
		 *
		 * @return mixed
		 */
		public function actionCreate(){
			$request = Yii::$app->request;
			$model   = new Specialty();
			
			if($request->isAjax){
				/*
				*   Process for ajax request
				*/
				Yii::$app->response->format = Response::FORMAT_JSON;
				if($request->isGet){
					return [
						'title'   => "Create new Specialty",
						'content' => $this->renderAjax('create',
							[
								'model' => $model,
							]),
						'footer'  => Html::button('Close',
								['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
									 . Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"]),
					
					];
				}
				else if($model->load($request->post()) && $model->save()){
					return [
						'forceReload' => '#crud-datatable-pjax',
						'title'       => "Create new Specialty",
						'content'     => '<span class="text-success">Create Specialty success</span>',
						'footer'      => Html::button('Close',
								['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
										 . Html::a('Create More',
								['create'],
								['class' => 'btn btn-primary', 'role' => 'modal-remote']),
					
					];
				}
				else{
					return [
						'title'   => "Create new Specialty",
						'content' => $this->renderAjax('create',
							[
								'model' => $model,
							]),
						'footer'  => Html::button('Close',
								['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
									 . Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"]),
					
					];
				}
			}
			else{
				/*
				*   Process for non-ajax request
				*/
				if($model->load($request->post()) && $model->save()){
					return $this->redirect(['view', 'id' => $model->id]);
				}
				else{
					return $this->render('create',
						[
							'model' => $model,
						]);
				}
			}
			
		}
		
		/**
		 * Updates an existing Specialty model.
		 * For ajax request will return json object
		 * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
		 *
		 * @param integer $id
		 *
		 * @return mixed
		 */
		public function actionUpdate($id){
			$request = Yii::$app->request;
			$model   = $this->findModel($id);
			
			if($request->isAjax){
				/*
				*   Process for ajax request
				*/
				Yii::$app->response->format = Response::FORMAT_JSON;
				if($request->isGet){
					return [
						'title'   => "Update Specialty #" . $id,
						'content' => $this->renderAjax('update',
							[
								'model' => $model,
							]),
						'footer'  => Html::button('Close',
								['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
									 . Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"]),
					];
				}
				else if($model->load($request->post()) && $model->save()){
					return [
						'forceReload' => '#crud-datatable-pjax',
						'title'       => "Specialty #" . $id,
						'content'     => $this->renderAjax('view',
							[
								'model' => $model,
							]),
						'footer'      => Html::button('Close',
								['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) . Html::a('Edit',
								['update', 'id' => $id],
								['class' => 'btn btn-primary', 'role' => 'modal-remote']),
					];
				}
				else{
					return [
						'title'   => "Update Specialty #" . $id,
						'content' => $this->renderAjax('update',
							[
								'model' => $model,
							]),
						'footer'  => Html::button('Close',
								['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
									 . Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"]),
					];
				}
			}
			else{
				/*
				*   Process for non-ajax request
				*/
				if($model->load($request->post()) && $model->save()){
					return $this->redirect(['view', 'id' => $model->id]);
				}
				else{
					return $this->render('update',
						[
							'model' => $model,
						]);
				}
			}
		}
		
		/**
		 * Delete an existing Specialty model.
		 * For ajax request will return json object
		 * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
		 *
		 * @param integer $id
		 *
		 * @return mixed
		 */
		public function actionDelete($id){
			$request = Yii::$app->request;
			$this->findModel($id)
				->delete()
			;
			
			if($request->isAjax){
				/*
				*   Process for ajax request
				*/
				Yii::$app->response->format = Response::FORMAT_JSON;
				
				return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
			}
			else{
				/*
				*   Process for non-ajax request
				*/
				return $this->redirect(['index']);
			}
			
		}
		
		/**
		 * Delete multiple existing Specialty model.
		 * For ajax request will return json object
		 * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
		 *
		 * @param integer $id
		 *
		 * @return mixed
		 */
		public function actionBulkDelete(){
			$request = Yii::$app->request;
			$pks     = explode(',', $request->post('pks')); // Array or selected records primary keys
			foreach($pks as $pk){
				$model = $this->findModel($pk);
				$model->delete();
			}
			
			if($request->isAjax){
				/*
				*   Process for ajax request
				*/
				Yii::$app->response->format = Response::FORMAT_JSON;
				
				return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
			}
			else{
				/*
				*   Process for non-ajax request
				*/
				return $this->redirect(['index']);
			}
			
		}
		
		/**
		 * Finds the Specialty model based on its primary key value.
		 * If the model is not found, a 404 HTTP exception will be thrown.
		 *
		 * @param integer $id
		 *
		 * @return Specialty the loaded model
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		protected function findModel($id){
			if(($model = Specialty::findOne($id)) !== null){
				return $model;
			}
			else{
				throw new NotFoundHttpException('The requested page does not exist.');
			}
		}
	}
