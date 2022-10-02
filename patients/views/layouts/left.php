<?php 
$debugTools = '';
if (YII_DEBUG){
	$debugTools = [
			'label' => 'Debug tools',
			'icon' => 'share',
			'url' => '#',
			'items' => [
					['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
					['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
					[
							'label' => 'Level One',
							'icon' => 'circle-o',
							'url' => '#',
							'items' => [
									['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
									[
											'label' => 'Level Two',
											'icon' => 'circle-o',
											'url' => '#',
											'items' => [
													['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
													['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
											],
									],
							],
					],
			],
	];
}
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
            	'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    //['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                    ['label' => Yii::t('app', 'Find Doctors'), 'icon' => 'search', 'url' => ['/site/index']],
                    ['label' => Yii::t('app', 'My Doctors'), 'icon' => 'user-md', 'url' => ['/doctors/index']],
                	['label' => Yii::t('app', 'My Appointments'), 'icon' => 'calendar', 'url' => ['/appointments/index']],
                	['label' => Yii::t('app', 'Payments'), 'icon' => 'usd', 'url' => ['/payments/index']],
                	['label' => Yii::t('app', 'Profile'), 'icon' => 'user-circle', 'url' => ['/profile/index']],
                	['label' => Yii::t('app', 'Support'), 'icon' => 'question-circle', 'url' => ['#'], 'options' => ['onclick' => 'return gotoChat(1);']],
                		
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    $debugTools
                ]
            ]
        ) ?>

    </section>

</aside>
