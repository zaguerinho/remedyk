<?php
namespace common\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\Message;
use common\models\User;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;

class ChatsController extends Controller{
	
	public function behaviors(){
		return [
				'access' => [
						'class' => AccessControl::className(),
						'rules' => [
								[
										'allow' => true,
										'roles' => ['@'],
								],
						],
				],
		];
	}
	
	public function actioAjaLoadTotalMessageCount(){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$me = User::getUserIdentity();
		$count = $me->getUnreadMessageCount();
		return ['count' => $count];
	}
	
	public function actionAjaxMarkAsRead($user_id){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$me = User::getUserIdentity();
		$messages = Message::find()->where(['from_id' => $user_id, 'to_id' => $me->id, 'readed_at' => null])->all();
		foreach ($messages as $message){
			$message->readed_at = date('Y-m-d H:i:s');
			$message->save();
		}
		return ['unreaded' => $me->getUnreadMessageCount(), 'unreaded_from_user' => $me->getUnreadMessageCount($user_id), 'user_id' => $user_id];
	}
	
	public function actionAjaxLoadActiveChatUsers(){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$me = User::getUserIdentity();
		$result = [];
		$activeChatUsers = $me->getActiveChatUsers();
		foreach ($activeChatUsers as $user){
			$unreadMessages = $me->getUnreadMessageCount($user->id);
			$user = [
					'chat_user_id'=>$user->id,
					'userPicture' =>$user->profilePicture,
					'userName' => $user->name,
					'unreadCount' => $unreadMessages
			];
			$result[] = $user;
		}
		return $result;
		
	}
	
	public function actionAjaxLoadMessages($user_id, $page=1){
		$me = User::getUserIdentity();
		$he = User::findIdentity($user_id);
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		$messages = Message::find()->where(['or', ['from_id' => $me->id, 'to_id' => $he->id], ['from_id' => $he->id, 'to_id' => $me->id]])->orderBy('sent_at desc')->limit(100)->offset(($page-1)*100)->all();
		$result = [];
		foreach ($messages as $message){
			/* @var \common\models\Message $message */
			$item = [
				'fromPicture' => $message->from->profilePicture,
				'message' => $message->message,
				'sent_at' => Yii::t('app', 'Sent at: '). date('M-d-Y h:i A', strtotime($message->sent_at)),
				'mine' => ($message->from_id == $me->id),
				'id' => $message->id
			];
			
			$result = ArrayHelper::merge([$item], $result);
		}
		return ['messages' => $result, 'page' => $page, 'person' => $he->name, 'person_id' => $he->id];
	}
	
	public function actionAjaxSendMessage(){
		Yii::$app->response->format = Response::FORMAT_JSON;
		if (Yii::$app->request->isPost){
			$post = Yii::$app->request->post();
			$me = User::getUserIdentity();
			$he = User::findIdentity($post['user_id']);
			$text = $post['text'];
			$temp_id = $post['temp_id'];
			$message = new Message([
					'from_id' => $me->id,
					'to_id' => $he->id,
					'message' => $text,
					'is_active' => true,
					'sent_at' => date('Y-m-d H:i:s'),
					'temp_id' => $temp_id,
			]);
			$message->save();
			return ['message' => $message->message, 'fromPicture' => $me->profilePicture, 'sent_at' => Yii::t('app', 'Sent at: '). date('M-d-Y h:i A', strtotime($message->sent_at)), 'id' => $message->id, 'temp_id' => $temp_id];
		}
		
	}
}