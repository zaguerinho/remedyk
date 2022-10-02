<?php
	
	use yii\helpers\Html;
	
	/* @var $this \yii\web\View */
	/* @var $content string */
	$notifications = [];
	$messagesCount = '';

?>

<header class="main-header">
	
	<?= Html::a('<span class="logo-mini"><img src="/images/logo.png" /></span><span class="logo-lg"><img src="/images/logo.png" /> Remedyk</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">
		<?php if(!Yii::$app->user->isGuest) : ?>
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
		<?php endif; ?>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
				<?php if(!Yii::$app->user->isGuest) : ?>
					<?php
					$user = Yii::$app->user->identity;
					/* @var common\models\User $user */
					$messagesCount = $user->getUnreadMessageCount();
					
					?>
                    <!-- Messages: style can be found in dropdown.less-->
                 	<?= $this->render('@common/partials/_notifications_view') ?>
                	
                    <!-- Tasks: style can be found in dropdown.less -->
	                <li class="dropdown tasks-menu">
	                    <a href="#" class="dropdown-toggle" data-toggle="control-sidebar">
	                        <i class="fa fa-comment"></i>
	                        <span class="label label-danger" id="message_count"><?= $messagesCount ?></span>
	                    </a>
	                </li>
                    <!-- User Account: style can be found in dropdown.less -->

                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?= $user->profilePicture ?>" class="user-image fast-change-picture" alt="User Image"/>
                            <span class="hidden-xs">
	                        	<span class="user-name">
	                        		<?= $user->first_name . ' ' . $user->last_name ?>
	                        	</span>
	                        	<br/>
	                        	<span class="user-role">
	                        		<?= Yii::t('app', 'Patient') ?>
	                        	</span>
                        	</span>
                        </a>
                        <ul class="dropdown-menu">
	                        <!-- User image -->
	                        <li class="user-header">
	                            <img src="<?= $user->profilePicture ?>" class="img-circle fast-change-picture"
	                                 alt="User Image"/>
	
	                            <p>
	                                <?= $user->first_name . ' ' . $user->last_name ?>
	                                <small><?= $user->email ?></small>
	                            </p>
	                        </li>
	                        <!-- Menu Body -->
	                        <li class="user-body">
	                           <!-- <div class="col-xs-4 text-center">
	                                <a href="#">Followers</a>
	                            </div> --> 
	                            <!-- <div class="col-xs-4 text-center">
	                                <a href="#">Friends</a>
	                            </div>  -->
	                        </li>
	                        <!-- Menu Footer-->
	                        <li class="user-footer">
	                            <div class="pull-left">
	                                <a href="/profile/password" class="btn btn-tertiary"><?= Yii::t('app', 'New password') ?></a>
	                            </div>
	                            <div class="pull-right">
	                                <?= Html::a(
	                                    Yii::t('app', 'Sign out'),
	                                    ['/site/logout'],
	                                    ['data-method' => 'post', 'class' => 'btn btn-tertiary']
	                                ) ?>
	                            </div>
	                        </li>
	                    </ul>
                    </li>
				<?php else : ?>
                    <li>
                        <a href="/site/login" class="btn btn-outline btn-sm">Log in</a>
                    </li>
				<?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
