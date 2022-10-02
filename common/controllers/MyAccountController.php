<?php
	
	
	/**
	 * SYCET by TJ ALTA TECNOLOGIA Y APLICACIONES, S.
	 * DE R.L. DE C.V. is licensed under a
	 * Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International
	 * License.
	 * Based on a work at http://sycet.net and its subdomains.
	 *
	 * @link      http://gotribit.com/
	 * @copyright Creative Commons Attribution-NonCommercial-NoDerivatives 4.0
	 *            International License
	 *
	 */
	
	namespace common\controllers;
	
	
	use common\models\NewPassword;
	use common\models\User;
	use Yii;
	use yii\web\Controller;
	
	
	abstract class MyAccountController extends Controller{
		
		
		/**
		 * @inheritDoc
		 */
		public function getViewPath(){
			
			return Yii::getAlias('@common/partials/my-account');
		}
		
		public function actionIndex(){
			return $this->redirect('password');
		}
		
		/**
		 * Method than takes the password param in $_POST to update de pass of the
		 * current user.
		 *
		 * @return string
		 */
		public function actionPassword(){
			
			$user = $this->getModel();
			
			if(\Yii::$app->request->post('NewPassword', false)){
				$user->setPassword(\Yii::$app->request->post('NewPassword')['newPass']);
				
				if($user->save())
					Yii::$app->session->setFlash('success', Yii::t('app', 'Your password was successfully updated.'));
				
				$this->redirect([
					'site/index',
				]);
			}
			
			return $this->render('password',
				[
					'user'  => $user,
					'model' => new NewPassword(),
				]);
		}
		
		
		public function getModel(){
			return User::findOne(\Yii::$app->user->getId());
		}
	}
