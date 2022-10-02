<?php

use common\widgets\AddressFiller;
use yii\base\Widget;
use yii\bootstrap\ActiveForm;
use common\models\Address;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><button class="btn btn-tertiary" disabled onclick="http://www.yiiframework.com">Get started with Yii</button></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>
        <div class="well">
        <div class="row">
        <div class="col-xs-12">
        <?php
        	$form = ActiveForm::begin();
        	$address = new Address();
        	echo AddressFiller::widget(['options' => ['form' => $form, 'address' => $address]]);
        	ActiveForm::end();
        ?>
        </div>
        </div>
        </div>
		<button class="btn btn-raised btn-danger" onclick="sendServerMessage()">Send Message</button>
    </div>
</div>
<script>
	var socket;
	$(function(){
		
		socket = new WebSocket('ws://localhost:8080');
		socket.onopen = function(e){
			console.log("Connected");
		};

		socket.onmessage = function(e){
			console.log(e);
		};

		/*$('button').click(function(){
			socket.send(JSON.stringify({'action' : 'chat', 'message' : 'Hello from the other side'}));
		});*/
		
	});

	function sendServerMessage(){
		$.post('/site/socket-test', {}, function(data){
			console.log("Success");
			console.log(data);
		}).fail(function(error){
			console.log("Error");
			console.log(error);
		});
		
	}
</script>
