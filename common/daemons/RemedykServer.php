<?php
namespace common\daemons;

use consik\yii2websocket\WebSocketServer;
use consik\yii2websocket\events\WSClientEvent;
use Ratchet\ConnectionInterface;

class RemedykServer extends WebSocketServer {
	public function init() {
		parent::init();
		$this->on(self::EVENT_CLIENT_CONNECTED, function(WSClientEvent $e) {
			$e->client->name = null;
			echo "Client Connected\n";
		});
		
		$this->on(self::EVENT_CLIENT_DISCONNECTED, function(WSClientEvent $e) {
			$e->client->name = null;
			echo "Client Disconnected\n";
		});
		
	}
	
	protected function getCommand(ConnectionInterface $from, $msg){
		$request = json_decode($msg, true);
		return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
	}
	
	public function commandChat(ConnectionInterface $client, $msg){
		//Expect message in json format with a 'message' field which holds the model of the Message created json_encoded
		//{'action': 'chat', 'message': $message->toJson()}
		$request = json_decode($msg, true);
		
		//First answer to PHP client so it can go on
		$client->send(json_encode(['type' => 'acknowledge']));
		
		//echo json_encode($request). "\n";
		
		//... Process the message and send it to the destination
		
		$message = $request['message'];
		
		
		$response = [
				'type' => 'chat',
				'message' => [
					'id' => $message['id'],
					'from_id' => $message['from_id'],
					'temp_id' => $message['temp_id'],
					'fromPicture' => $message['fromPicture'],
					'sent_at' => $message['sent_at'],
					'message' => $message['message'],
				],
			
			];
		
		foreach ($this->clients as $remedykClient) {
			if ($remedykClient->name == $message['to_id']){
				$to_response = $response;
				$to_response['message']['mine'] = false;
				$to_response['message']['unread'] = $message['unread'];
				$remedykClient->send( json_encode($to_response));
			}
			if ($remedykClient->name == $message['from_id']){
				$from_response = $response;
				$from_response['message']['mine'] = true;
				$remedykClient->send( json_encode($from_response));
			}
		}
		
	}
	
	public function commandUnreaded(ConnectionInterface $client, $msg){
		//Expect the unreaded message count in 'count' {'action': 'unreaded', 'count': $count, 'to_id': $user_id
		$request = json_decode($msg, true);
		
		//First answer to PHP client so it can go on
		$client->send(json_encode(['type' => 'acknowledge']));
		
		//echo json_encode($request). "\n";
		
		$response = ['type' => 'unreaded', 'count' => $request['count']];
		foreach ($this->clients as $remedykClient){
			if ($remedykClient->name == $request['to_id']){
				$remedykClient->send(json_encode($response));
			}
		}
	}
	
	public function commandUnseen(ConnectionInterface $client, $msg){
		//Expect the unreaded message count in 'count' {'action': 'unseen', 'count': $count, 'target_id': $user_id
		$request = json_decode($msg, true);
		
		//First answer to PHP client so it can go on
		$client->send(json_encode(['type' => 'acknowledge']));
		
		//echo json_encode($request). "\n";
		
		$response = ['type' => 'unseen', 'count' => $request['count']];
		foreach ($this->clients as $remedykClient){
			if ($remedykClient->name == $request['target_id']){
				$remedykClient->send(json_encode($response));
			}
		}
	}
	
	public function commandNotification(ConnectionInterface $client, $msg){
		//Expect the notification in json format with a 'notification' field which holds the model of the Notification created json_encoded
		//{'action': 'notification', 'notification': $notification->toJson()}
		$request = json_decode($msg, true);
		
		//First answer to PHP client so it can go on
		$client->send(json_encode(['type' => 'acknowledge']));
		
		echo json_encode($request). "\n";
		
		//... Process the notification and send it to the destination
		$notification = $request['notification'];
		$response = [
			'type' => 'notification',
			'notification' => [
				'id' => $notification['id'],
				'target_id' => $notification['target_id'],
				'text' => $notification['text'],
				'datetime' => $notification['datetime'],
				'unseen_count' => $notification['unseen_count'],
				'fa_icon_class' => $notification['fa_icon_class'],
				'target_url' => $notification['target_url'],
			]
		];
		
		foreach ($this->clients as $remedykClient){
			if ($remedykClient->name == $notification['target_id']){
				$remedykClient->send(json_encode($response));
			}
		}
		
	}
	
	public function commandVisited(ConnectionInterface $client, $msg){
		//Expect the visited notification in json format with a 'id' field which holds the id of the Notification visited and a target_id with the user id to send the response
		//{'action': 'visited', 'id': $notification->id, 'target_id': $notification->target_id}
		$request = json_decode($msg, true);
		//echo json_encode($request). "\n";
		
		//First answer to PHP client so it can go on
		$client->send(json_encode(['type' => 'acknowledge']));
		
		$response = ['type' => 'visited', 'id' => $request['id']];
		
		foreach ($this->clients as $remedykClient){
			if ($remedykClient->name == $request['target_id']){
				$remedykClient->send(json_encode($response));
			}
		}
	}
	
	public function commandRegister(ConnectionInterface $client, $msg){
		//Expect the identification in json format with a 'user_id' field which holds the user id of the currently logued in user
		
		$request = json_decode($msg, true);
		// echo json_encode($request). "\n";
		
		$result = ['message' => 'User registered', 'type' => 'register'];
		
		if (!empty($request['user_id']) && $id = trim($request['user_id'])) {
			$client->name = $id;
		} else {
			$result['message'] = 'Invalid user_id';
		}
		
		$client->send( json_encode($result) );
		
	}
}