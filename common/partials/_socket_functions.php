<?php
use common\models\User;
use yii\web\View;

if (!Yii::$app->user->isGuest){
	
	$websocketServer = Yii::$app->params['websocketServer'];
	$websocketPort = Yii::$app->params['websocketPort'];
	$user_id = User::getUserIdentity()->id;
	
	$js = <<<JS
	var socket;
	function sendRegistration(user_id){
		socket.send(JSON.stringify({'action': 'register', 'user_id': '{$user_id}'}));
	}
JS;
	
	$this->registerJs($js, View::POS_HEAD, 'socket-global');
	
	$js2 = <<<JS
	socket = new WebSocket('ws://{$websocketServer}:{$websocketPort}');
		socket.onopen = function(e){
			console.log("Connected");
			sendRegistration({$user_id});
		};
		
		socket.onmessage = function(e){
			var response = JSON.parse(e.data);
			
			switch (response.type){
				case 'register':
					console.log(response.message);
					break;
				case 'unreaded':
					markUnreadCount(response.count);
					break;
				case 'unseen':
					markUnseenCount(response.count);
					break;
				case 'chat':
					receiveMessage(response.message);
					break;
				case 'notification':
					updateNotifications(response.notification);
					break;
				case 'visited':
					setVisitedNotification(response.id);
			}
		};
JS;
	$this->registerJs($js2, View::POS_READY, 'socket-init');
}