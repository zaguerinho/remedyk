<?php
	
	namespace patients\controllers;

use Conekta\Conekta;
use Conekta\Customer;
use common\models\Appointment;
use Yii;
use yii\bootstrap\Html;
use yii\web\Response;
use Conekta\Order;
use common\models\User;
use Conekta\ProcessingError;
use Conekta\Handler;
use Conekta\ParameterValidationError;
use common\models\Commission;
use common\helpers\SycetHelper;
								
	
	class AppointmentsController extends \doctors\controllers\AppointmentsController{
		
		
		
		
		public function actionConfirm($id){
			
			if (Yii::$app->request->isAjax){
				Yii::$app->response->format = Response::FORMAT_JSON;
				$model = $this->findModel($id);
				$partial_price = number_format($model->price * Yii::$app->params['initialPercent'], 2);	
				
				return [
						'title' => Yii::t('app', 'Appointment Payment'),
						'content' => $this->renderAjax('@patients/views/appointments/payment', [
								'appointment' => $model,
								'partial_price' => $partial_price
						]),
						'footer' => Html::button(Yii::t('app', 'Close'),['class'=>'btn btn-secondary','data-dismiss'=>"modal"]).' '.
						Html::a(Yii::t('app', 'Return'), ['view', 'id' => $model->id], ['class' => 'btn btn-rejected', 'role' => 'modal-remote'])
				];
				
			}
			else {
				if (Yii::$app->request->isPost){
					$appointment = $this->findModel($id);
					$post = Yii::$app->request->post();
					
					Conekta::setApiKey(Yii::$app->params['conektaPrivateKey']);
					Conekta::setApiVersion('2.0.0');
					
					if (isset($post['conektaTokenId'])){
						$user = User::getUserIdentity();
						try {
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
							
							$order = Order::create([
									'currency' => $appointment->currency->code,
									'line_items' => [
											[
													'name' => 'appointment',
													'unit_price' => ($appointment->price) * 100 * Yii::$app->params['initialPercent'], //30 % in cents
													'quantity' => 1,
													'sku' => 'appointment-'.$appointment->id
											]
									],
									'customer_info' => [
											'customer_id' => $customer->id,
									],
									'charges' => [
											[
												'payment_method' => [
														'type' => 'default'
												]
											]
									]
							]);
							
							
							
							$commission = $this->processPayment($order, $appointment, 1);
							
							
							if ($commission) {
								//return $this->redirect(['/payments/view', 'id' => $commission->id]);
								return $this->redirect('/payments/index');
							}
						
						}
						catch (ProcessingError $e){
							Yii::$app->session->setFlash('error', $e->getMessage());
						}
						catch (ParameterValidationError $e){
							Yii::$app->session->setFlash('error', $e->getMessage());
						}
						catch (Handler $e){
							Yii::$app->session->setFlash('error', $e->getMessage());
						}
					
						
					}
				}
				return $this->render('@patients/views/appointments/payment-error');
			}
			
		}
		
		public function processPayment($order, $appointment, $retry){
			
			if ($retry > 3){
				Yii::$app->session->setFlash('error', Yii::t('app', 'We could not make the charge to your credit/debit card. Please try again with other one.'));
				return false;
			}
			$flag = false;
			$status = $order->payment_status;
			switch ($status){
				case 'paid':
				case 'pre_authorized':
					$commission = new Commission([
					'amount' => $order->amount / 100,
					'appointment_id' => $appointment->id,
					'paid_on' => date('Y-m-d'),
					'payment_method_id' => 1,
					'percent' => Yii::$app->params['initialPercent'],
					'conekta_order_id' => $order->id,
					'status' => Commission::STATUS_PENDING_APPOINTMENT
					]);
					
					$flag = $commission->save();
					$flag = $flag && SycetHelper::createCfdiForPatient($commission);
					
					$appointment->status = Appointment::STATUS_CONFIRMED;
					$appointment->confirmation_datetime = date('Y-m-d H:i:s');
					$flag = $flag && $appointment->save();
					
					break;
				case 'declined':
					$charge = $order->createCharge([
						[
							'payment_method' => [
								'type' => 'default',
							]
						]
					]);
					$flag = $this->processPayment($order, $appointment, $retry+1);		
					break;
				case 'expired':
				case 'voided':
					Yii::$app->session->setFlash('error', Yii::t('app', 'Your credit/debit card expired. Please try again with other one.'));
					$flag = false;
				case 'payment_pending':
					break;
				case 'refunded':
				case 'partially_refunded':
				case 'charged_back':
					break;
					
			}
			if ($flag) 
				return $commission;
		}
		
	}