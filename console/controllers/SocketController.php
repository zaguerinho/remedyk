<?php
namespace console\controllers;

use common\daemons\RemedykServer;
use consik\yii2websocket\WebSocketServer;
use consik\yii2websocket\events\WSClientMessageEvent;
use Yii;
use yii\console\Controller;

class SocketController extends Controller {
	public function actionStart()
	{
		$server = new RemedykServer();
		$server->port = Yii::$app->params['websocketPort']; //This port must be busy by WebServer and we handle an error
		
		$server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN_ERROR, function($e) use($server) {
			echo "Error opening port " . $server->port . "\n";
			$server->port += 1; //Try next port to open
			$server->start();
		});
			
		$server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN, function($e) use($server) {
			echo "Server started at port " . $server->port. "\n";
		});
				
		$server->start();
	}
}