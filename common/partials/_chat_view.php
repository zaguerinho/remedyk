<?php

use common\models\User;
use yii\web\View;

$user = User::getUserIdentity();
$userPicture = $user->profilePicture;
$sendingText = Yii::t('app', 'Sending...');
$js = <<<JS

	function adjustChatSize(){
		
		var w = Math.min(document.documentElement.clientWidth, window.innerWidth, 400 || 0);
		var h = Math.min(document.documentElement.clientHeight, window.innerHeight || 0);
		var mt = $('.main-header').css('height');
		$('#active_conversations').css({height: h-280});
		$('.control-sidebar').css({width: w, 'padding-top': mt});
		$('#messages_container').css({height: h-300});
	}
	adjustChatSize();
	$(window).resize(function(){
    	adjustChatSize();
	});

	$("#message_to_send").on('keyup', function (e) {
    if (e.keyCode == 13) {
        $('#send_message_button').click();
    }
});
	
JS;

$this->registerJs($js, View::POS_READY, 'chat-init-function');
$js2 = <<<JS
	function receiveMessage(message){
		/*
		 *	message.fromPicture
		 *	message.from_id
		 *	message.sent_at
		 *	message.message
		 *	message.mine
		 *	message.unread -> if not mine
		 *	message.id 
		 *	message.temp_id
		 */
		loadActiveChatUsers();
		var html = '';
		if (message.mine == true){
			if (messageFound('unsent_'+message.temp_id)){
					updateMessageIdAndSentAt('unsent_'+message.temp_id, message.id, message.sent_at)
				}
			if (!messageFound(message.id)){
				html = pushMessageMine(message.message, message.fromPicture, message.sent_at, message.id);
				$('#messages_container').append(html);	
				$('#messages_container').scrollTop($('#messages_container').prop("scrollHeight"));
			}
		}
		if (message.mine == false){
			//if I am in the chat enters directly and mark it as readed
			if (isActiveChat(message.from_id)){
				if (!messageFound(message.id)){
					html = pushMessageHis(message.message, message.fromPicture, message.sent_at, message.id);
					$('#messages_container').append(html);	
					$('#messages_container').scrollTop($('#messages_container').prop("scrollHeight"));
				}
			} 

			if (isOpen(message.from_id)){
				markAsRead(message.from_id);
			}
			else {//if I am not in the chat if notifies me in the message icon, reorganizes the chat sort, plays sound
				$('#message_count').html(message.unread);
				playChatSound();
			}
			
			
		}
		
	}	
	
	function playChatSound(){
		var audio = new Audio('/sounds/chat_notify.mp3');
		audio.play();
	}
	
	function loadActiveChatUsers(){
		$.get('/chats/ajax-load-active-chat-users', {}, function(data){
			$('#active_conversations').html('<li></li>');
			$.each(data, function(index, item){
				var html = pushActiveConversation(item.userPicture, item.userName, item.unreadCount, item.chat_user_id);
				$('#active_conversations').find('li').append(html);
			});
		}).fail(function(error){console.log(error);});
	}

	function markAsRead(user_id){
		$.get('/chats/ajax-mark-as-read?user_id='+user_id, {}, function(data){ 
			$('#message_count').html(data.unreaded); 
			$('#message_count_'+data.user_id).html(data.unreaded_from_user); 
		}).fail(function(error){console.log(error);});
	}
	
	function isActiveChat(person_id){	
		return ($('#chat_person_id').val() == person_id);
	}
	function isOpen(person_id){
		person_id = person_id || false;
		var result = $('.control-sidebar').hasClass('control-sidebar-open') && $('#control-sidebar-conversation-tab').hasClass('active');
		if (person_id)
			return isActiveChat(person_id) && result;
		return result;
	}

	function markUnreadCount(count){	
		$('#message_count').html(count);
	}
	
	
	
	function pushActiveConversation(fromPicture, name, unread, from_id){
		var html =
			'<div class="row"><div class="col-xs-12"><div class="separator" style="margin-bottom: 0"></div></div></div>'+
                '<a href="#" onclick="return gotoChat('+from_id+');">'+
                	'<div class="col-xs-4 text-center">'+ 
                		'<img src="'+fromPicture+'" class="img img-circle img-small" style="margin-top: 0; margin-bottom: 5px;">'+
                	'</div>'+
                	'<div class="col-xs-8" style="margin-top: 10px;">'+
                		name+' <span class="label label-danger" id="message_count_'+from_id+'">'+unread+'</span>'+
                	'</div>'+
                '</a>'+
        	'<div class="row"><div class="col-xs-12"><div class="separator" style="margin-bottom: 0"></div></div></div>';
		return html;
	}
	function pushMessageMine(message, fromPicture, sent_at, message_id){
		var html = 
			'<div class="row">'+
            	'<div class="col-xs-3 text-left">'+
					'<input type="hidden" class="message-id" value="'+message_id+'">'+
            		'<img src="'+fromPicture+'" class="img img-circle img-small" style="margin-top: 0; margin-bottom: 5px;">'+
            	'</div>'+
            	'<div class="col-xs-9 chat-ballon-mine">'+
            		'<p>'+message+'</p>'+
            		'<div class="col-xs-12 text-right"><small>'+sent_at+'</small></div>'+
            	'</div>'+
            '</div>';
		return html;
	}
	
	function pushMessageHis(message, fromPicture, sent_at, message_id){
		var html = 
			'<div class="row">'+
            	'<div class="col-xs-9 chat-ballon-his">'+
            		'<p>'+message+'</p>'+
            		'<div class="col-xs-12 text-right"><small>'+sent_at+'</small></div>'+
            	'</div>'+
            	'<div class="col-xs-3 text-right">'+
            		'<img src="'+fromPicture+'" class="img img-circle img-small" style="margin-top: 0; margin-bottom: 5px;">'+
					'<input type="hidden" class="message-id" value="'+message_id+'">'+
            	'</div>'+
            '</div>';
		return html;
	}
	
	function sendMessage(){
		$('#unsent_messages').val(parseInt($('#unsent_messages').val())+1);
		var temp_id = $('#unsent_messages').val();
		var id = $('#chat_person_id').val();
		var text = $('#message_to_send').val();
		if (!messageFound('unsent_'+$('#unsent_messages').val())){
			html = pushMessageMine(text, '{$userPicture}', '{$sendingText}', 'unsent_'+temp_id);
			$('#messages_container').append(html);
			$('#messages_container').scrollTop($('#messages_container').prop("scrollHeight"));
		}
	
		if (text != ""){
			$('#message_to_send').val('');
			$.post('/chats/ajax-send-message', {user_id: id, text: text, temp_id: temp_id}, function(data){
				if (messageFound('unsent_'+data.temp_id)){
					updateMessageIdAndSentAt('unsent_'+data.temp_id, data.message_id, data.sent_at);
				}
				if (!messageFound(data.id)){
					html = pushMessageMine(data.message, data.fromPicture, data.sent_at, data.message_id);
					$('#messages_container').append(html);
					$('#messages_container').scrollTop($('#messages_container').prop("scrollHeight"));
				}
			}).fail(function(error){
				console.log(error)
			});
		}
	}
	
	function messageFound(message_id){
		if ($('input.message-id[value="'+message_id+'"]').length)
			return true;
		return false;
	}
	
	function removeMessage(message_id){
		$('input.message-id[value="'+message_id+'"]').parent().parent().remove();
	}	
	
	function updateMessageIdAndSentAt(temp_id, id, sent_at){
		var element = $('input.message-id[value="'+temp_id+'"]');
		element.parent().parent().find('small').html(sent_at);
		element.val(id)
	}
	
	function gotoChat(user_id){
		$.get('/chats/ajax-load-messages?user_id='+user_id, {}, function(data){		
			$('#chat_name_container').html(data.person);
			$('#chat_person_id').val(data.person_id);
			$('#messages_container').html('');
			$.each(data.messages, function(index, message){
				let html = '';
				if (message.mine){
					html = pushMessageMine(message.message, message.fromPicture, message.sent_at, message.id);
				}
				else {
					html = pushMessageHis(message.message, message.fromPicture, message.sent_at, message.id);
				}
				if (!messageFound(message.id)){
					$('#messages_container').append(html);
				}
				
			});
			if (!$('.control-sidebar').hasClass('control-sidebar-open')){
				$('.control-sidebar').addClass('control-sidebar-open');
			}
			$('a[href="#control-sidebar-conversation-tab"]').click();
			markAsRead(data.person_id);
			$('#messages_container').scrollTop($('#messages_container').prop("scrollHeight"));
		});
		return false;
	}

JS;
$this->registerJs($js2, View::POS_END, 'chat-global-function');
$activeChats = $user->getActiveChatUsers();

?>

<!-- Chat Sidebar -->
<aside class="control-sidebar control-sidebar-light">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-user"></i></a></li>
        <li><a href="#control-sidebar-conversation-tab" data-toggle="tab"><i class="fa fa-comment"></i></a></li>
        <li><a data-toggle="control-sidebar" class="dropdown-toggle remedyk-link"><i class="fa fa-times"></i></a> 
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane active" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading"><?= Yii::t('app', 'Conversations') ?></h3>

            <ul class='control-sidebar-menu' id="active_conversations">
                <li>
                	<?php foreach ($activeChats as $chatPreview): ?>
                		<div class="row"><div class="col-xs-12"><div class="separator" style="margin-bottom: 0"></div></div></div>
                			<a href="#" onclick="return gotoChat(<?= $chatPreview->id ?>);">
                			<div class="col-xs-4 text-center"> 
                				<img src="<?= $chatPreview->profilePicture ?>" class="img img-circle img-small" style="margin-top: 0; margin-bottom: 5px;">
                			</div>
                			<div class="col-xs-8" style="margin-top: 10px;">
                				<?= $chatPreview->name ?> <span class="label label-danger" id="message_count_<?= $chatPreview->id ?>"><?= $user->getUnreadMessageCount($chatPreview->id) ?></span>
                			</div>
                			</a>
                		<div class="row"><div class="col-xs-12"><div class="separator" style="margin-bottom: 0"></div></div></div>
                	<?php endforeach; ?>
                    <!-- <a href='javascript::;'>
                        <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading"></h4>

                            <p></p>
                        </div>
                    </a> -->
                </li>
                
            </ul>
            <!-- /.control-sidebar-menu -->
        </div>
        <!-- /.tab-pane -->

        <!-- Settings tab content -->
        <div class="tab-pane" id="control-sidebar-conversation-tab">
        	<h4 id="chat_name_container"></h4>
        	<input type="hidden" id="chat_person_id">
        	<input type="hidden" id="unsent_messages" value="0">
        	<div class="separator"></div>
        	<div id="messages_container" class="messages-container">
	            
            </div>
            <div class="separator"></div>
            <div class="row">
            	<div class="col-xs-9">
            		<div class="form-group no-margin">
            			<input type="text" id="message_to_send" class="form-control" placeholder="<?= Yii::t('app', 'Write your message...') ?>">
            		</div>
            	</div>
            	<div class="col-xs-3" style="margin-top: 5px;">
            		<span class="input-group-btn">
						<button id="send_message_button" class="btn btn-primary btn-xs" type="button" onclick="sendMessage()"><?= Yii::t('app', 'Send') ?></button>
					</span>
            	</div>
            </div>
        </div>
        <!-- /.tab-pane -->
    </div>
</aside><!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class='control-sidebar-bg'></div>