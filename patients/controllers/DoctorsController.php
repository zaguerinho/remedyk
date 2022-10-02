<?php
	
	namespace patients\controllers;
	
	use Yii;
	use common\models\Doctor;
use common\models\User;
	use common\models\search\Doctor as DoctorSearch;
	use yii\web\Controller;
	use yii\web\NotFoundHttpException;
	use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\Comment;
			
	/**
	 * DoctorsController implements the CRUD actions for Doctor model.
	 */
	class DoctorsController extends Controller{
		public function getViewPath(){
			return '@patients/views/doctors';
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
		 * Lists all Doctor models.
		 *
		 * @return mixed
		 */
		public function actionIndex(){
			$searchModel  = new DoctorSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			
			return $this->render('index',
				[
					'searchModel'  => $searchModel,
					'dataProvider' => $dataProvider,
				]);
		}
		
		
		/**
		 * Displays a single Doctor model.
		 *
		 * @param integer $id
		 *
		 * @return mixed
		 */
		public function actionView($id){
			return $this->redirect(['profile', 'id' => $id]);
		}
		
		/**
		 * Finds the Doctor model based on its primary key value.
		 * If the model is not found, a 404 HTTP exception will be thrown.
		 *
		 * @param integer $id
		 *
		 * @return Doctor the loaded model
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		protected function findModel($id){
			if(($model = Doctor::findOne($id)) !== null){
				return $model;
			}
			else{
				throw new NotFoundHttpException('The requested page does not exist.');
			}
		}
		
		public function actionProfile($id, $office_id=null){
			$doctor = $this->findModel($id);
			
			return $this->render('profile', ['doctor' => $doctor, 'office_id' => $office_id]);
		}
		
		public function actionAjaxSendComment(){
			Yii::$app->response->format = Response::FORMAT_JSON;
			if (Yii::$app->request->isPost){
				$post = Yii::$app->request->post();
				$comment = new Comment([
						'target_id' => $post['target_id'],
						'from_id' => $post['from_id'],
						'text' => $post['text']
				]);
				if ($post['parent_comment_id'] != 'null'){
					$comment->parent_comment_id = $post['parent_comment_id'];
				}
				$user = User::findIdentity($comment->from_id);
				$user_name = $user->name;
				
				if ($comment->save()){
					return ['status' => 200, 'text' => Yii::t('app', 'Your comment is being moderated before it can be shown.'), 'parent_id' => $comment->parent_comment_id, 'name' => $user_name];
				}
				return ['status' => 500, 'text' => Yii::t('app', 'There was an error posting your comment. Please try again later.'), 'parent_id' => $comment->parent_comment_id, 'name' => $user_name];
			}
			
		}
		
		public function actionAjaxRate(){
			Yii::$app->response->format = Response::FORMAT_JSON;
			if (Yii::$app->request->isPost){
				$post = Yii::$app->request->post();
				$doctor = Doctor::findOne($post['doctor_id']);
				$patient = User::getUserIdentity()->patient;
				$patient->setRating($doctor->id, $post['value']);
				return ['avgRate' => $doctor->getRating()];
			}
		}
	}
