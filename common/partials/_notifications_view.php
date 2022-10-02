<?php
use common\models\User;
use yii\web\View;

if (!Yii::$app->user->isGuest){
	$user = User::getUserIdentity();
	$notifications = $user->getLastNotifications();
	$countNotifications = $user->getUnseenNotificationsCount();
	
$js = <<<JS
function updateNotifications(notification){

	/*
	 * notification.id
	 * notification.target_id
	 * notification.text
 	 * notification.datetime
	 * notification.unseen_count
	 * notification.fa_icon_class
	 * notification.target_url
	 *
	 */
	
	insertFirstNotification(notification, 'not-visited')
	removeLastNotification();
	markUnseenCount(notification.unseen_count);
}

function markUnseenCount(count){
	$('#notifications_count').html(count);
}

function setVisitedNotification(notification_id){
	if ($('#notification-'+notification_id).length){
		if ($('#notification-'+notification_id).hasClass('not-visited')){
			$('#notification-'+notification_id).removeClass('not-visited');
		}
	}
}

function clearUnseenNotifications(){
	markUnseenCount(''); 
	$.get('/notifications/ajax-clear-unseen-notifications', {}, function(data){ 
		markUnseenCount(data.count); 
	}).fail(function(error){ 
		console.log(error); 
	});
}

function insertFirstNotification(notification, cssClass){
	var html = 
		'<li class="'+cssClass+'" id="notification-'+notification.id+'">'+
			'<a title="'+notification.text+'" href="'+notification.target_url+'" onclick="visitLink('+notification.id+'); return false;">'+
				'<i class="'+notification.fa_icon_class+'"></i> '+notification.text+
			'</a>'+
		'</li>';
	$('#notifications_container').prepend(html);
	playNotifySound();
}
function removeLastNotification(){
	if ($('#notifications_container').children().length > 10){
		$('#notifications_container>li:last').remove();
	}
}

function visitLink(id){
	$.get('/notifications/ajax-visit-link?id='+id, {}, function(data){
		location.href = data.href;	
	})
}

function playNotifySound(){
	var audio = new Audio('/sounds/notify.mp3');
	audio.play();
}

JS;

$this->registerJs($js, View::POS_END, 'notifications-global');
?>

<li class="dropdown notifications-menu">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" onclick="clearUnseenNotifications()">
    	<i class="fa fa-bell"></i>
        <span id="notifications_count" class="label label-danger"><?= $countNotifications ?></span>
	</a>
	<ul class="dropdown-menu">
		<li class="header"><?=  Yii::t('app', 'Notifications') ?></li>
		<?php if (count($notifications) > 0): ?>
		<li>

			<!-- inner menu: contains the actual data -->
			<ul class="menu" id="notifications_container">
			<?php foreach ($notifications as $notification): ?>
				<?php /* @var $notification \common\models\Notification */ ?>
				<?php $class = $notification->visited_at? '': 'not-visited'; ?>
				<li class="<?= $class ?>" id="notification-<?= $notification->id ?>">
					<a title="<?= $notification->localized_text ?>" href="<?= $notification->target_url ?>" onclick="visitLink(<?= $notification->id ?>); return false;">
						<i class="<?= $notification->fa_icon_class ?>"></i> <?= $notification->localized_text ?>
					</a>
				</li>
			<?php endforeach; ?>
			</ul>
                            
		</li>
		<?php endif; ?>
		<li class="footer"><a href="/notifications/index"><?= Yii::t('app', 'View All') ?></a></li>
	</ul>
</li>

<?php 
}