<?php

namespace doctors\controllers;

use common\models\Membership;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use Conekta\Conekta;
use Conekta\Customer;
use Conekta\Plan;
use yii\web\Response;

class MembershipsController extends \yii\web\Controller
{
	public function behaviors(){
		return [
				'access' => [
						'class' => AccessControl::className(),
						'rules' => [
								[
										'actions' => ['charge'],
										'allow' => true,
								],
								[
										'actions' => ['index', 'subscribe'],
										'allow' => true,
										'roles' => ['@'],
								],
						],
				],
		];
	}
	
    public function actionIndex()
    {
    	$memberships = Membership::find()->orderBy('price')->all();
        return $this->render('index', ['memberships' => $memberships]);
    }

    public function actionSubscribe($id)
    {
    	$membership = Membership::findOne($id);
    	/* @var $user \common\models\User */
    	/* @var $doctor \common\models\Doctor */
    	$user = Yii::$app->user->identity;
    	$doctor = $user->doctor;
    	if (Yii::$app->request->isPost){
    		$post = Yii::$app->request->post();
    		
    		Conekta::setApiKey(Yii::$app->params['conektaPrivateKey']);
    		Conekta::setApiVersion('2.0.0');
    		if (isset($post['conektaTokenId'])){
    			if (isset($user->conekta_customer_id) && $user->conekta_customer_id){
    				$customer = Customer::find($user->conekta_customer_id);
    				
    				if ($customer->payment_sources[0])
    						$customer->payment_sources[0]->delete();
    				$customer->createPaymentSource([
    					'type' => 'card',
    					'token_id' => $post['conektaTokenId'],
    				]);
    				
    			}
    			else {
    				$customer = Customer::create([
    						'name' => $user->name,
    						'email' => $user->email,
    						'phone' => $user->phone,
    						'payment_sources' => [
    								[
    										'type' => 'card',
    										'token_id' => $post['conektaTokenId'],
    								]
    						]
    				]);
    				$user->conekta_customer_id = $customer->id;
    				$user->save();
    			}
    			
    			if ($doctor->getMembership()->id != $membership->id){ // Membership changed
    				
    				$plan = $membership->conektaPlan;
    				if ($plan){
	    				if ($customer->subscription && ($customer->subscription->status != "canceled")){
	    					/*$customer->subscription->update([
	    							'plan' => $plan->id,    					
	    					]);*/
	    					$customer->subscription->cancel();
	    				}
	    				//else{
	    					$customer->createSubscription([
	    						'plan' => $plan->id,
	    					]);
	    				//}
    				}
    				else {
    					if ($customer->subscription){
    						$customer->subscription->cancel();
    					}
    				}
    				
    				$doctorMembership = $doctor->membership2doctors[0];
    				$doctorMembership->membership_id = $membership->id;
    				$doctorMembership->save();
    				
    				return $this->redirect('index');
    			}
    			
    		}
    	}
        return $this->render('subscribe', ['membership' => $membership]);
    }
    
    public function beforeAction($action){
    	if ($action->id == 'charge'){
    		$this->enableCsrfValidation = false;
    	}
    	return parent::beforeAction($action);
    }
    
    public function actionCharge(){
    	$body = @file_get_contents('php://input');
    	$data = json_decode($body);
    	http_response_code(200); // Return 200 OK
    	
    	switch ($data->type){
    		case 'subscription.paid':
    			$message = 'membershipPaid';
    			$conekta_customer_id = $data->data->object->customer_id;
    			$user = User::findByConektaCustomerId($conekta_customer_id);
    			Yii::$app->response->statusCode = 200;
    			Yii::$app
    			->mailer
    			->compose(
    					['html' => $message.'-html', 'text' => $message.'-text'],
    					['user' => $user]
    					)
    					->setFrom([Yii::$app->params['supportEmail'] => 'Remedyk robot'])
    					->setTo($user->email)
    					->setSubject(Yii::t('app','Remedyk Membership payment'))
    					->send();
    			break;
    		case '':
    			break;
    	}
    	
    	
    	
    	
    	return;
    }
    
    

}
