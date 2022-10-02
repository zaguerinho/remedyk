<?php
	$debugTools = '';
	if(YII_DEBUG){
		$debugTools = [
			'label' => 'Debug tools',
			'icon'  => 'share',
			'url'   => '#',
			'items' => [
				['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
				['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
				[
					'label' => 'Level One',
					'icon'  => 'circle-o',
					'url'   => '#',
					'items' => [
						['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
						[
							'label' => 'Level Two',
							'icon'  => 'circle-o',
							'url'   => '#',
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
		
		<?= dmstr\widgets\Menu::widget([
			'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
			'items'   => [
				//['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
				['label' => Yii::t('app', 'Moderation'), 'icon' => 'hand-stop-o', 'url' => ['/moderations/index']],
				['label' => Yii::t('app', 'Patients'), 'icon' => 'users', 'url' => ['/patients/index']],
				['label' => Yii::t('app', 'Doctors'), 'icon' => 'user-md', 'url' => ['/doctors/index']],
				['label' => Yii::t('app', 'Appointments'), 'icon' => 'calendar', 'url' => ['/appointments/index']],
				['label' => Yii::t('app', 'Collection'), 'icon' => 'usd', 'url' => ['/commissions/index']],
				['label' => Yii::t('app', 'Payments'), 'icon' => 'money', 'url' => ['/doctor-payments/index']],
				['label' => Yii::t('app', 'Users'), 'icon' => 'user-circle', 'url' => ['/users/index']],
				[
					'label' => Yii::t('app', 'Catalogs'),
					'icon'  => 'list',
					'url'   => '#',
					'items' => [
						['label' => Yii::t('app', 'Specialties'), 'icon' => 'star', 'url' => ['/specialties'],],
						['label' => Yii::t('app', 'Procedures'), 'icon' => 'wrench', 'url' => ['/procedure'],],
						['label' => Yii::t('app', 'Medicines'), 'icon' => 'medkit', 'url' => ['/medicine'],],
					],
				],
				['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
				$debugTools,
			],
		]) ?>

    </section>

</aside>
