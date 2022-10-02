<?php
namespace patients\components;

use Yii;
use yii\base\Component;
use common\models\User;

class CheckActivation extends Component{
	public function init() {
		if (! Yii::$app->user->isGuest){
			//Redirect to activate account if it is not activated yet
			$user = Yii::$app->user->identity;
			/* @var \common\models\User $user */
			if ($user->status == User::STATUS_TO_CONFIRM 
			&& Yii::$app->request->url != '/site/activation-pending' 
			&& Yii::$app->request->url != '/site/activate-account' 
			&& Yii::$app->request->url != '/site/logout'){
				
				return Yii::$app->response->redirect('/site/activation-pending');
			}
			
			if ($user->status == User::STATUS_DELETED
					&& Yii::$app->request->url != '/site/account-disabled'
					&& Yii::$app->request->url != '/site/logout'){
						
						return Yii::$app->response->redirect('/site/account-disabled');
			}
			
			//Redirect to create a branch
			if ($user->profileType() == User::STAFF) {
				return Yii::$app->response->redirect(Yii::$app->params['adminDomain']);
			}
			if ($user->profileType() == User::DOCTOR) {
				return Yii::$app->response->redirect(Yii::$app->params['doctorsDomain']);
			}
			
		}
		
		parent::init();
	}
}