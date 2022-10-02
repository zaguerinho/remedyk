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
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items' => [
                    //['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                    ['label' => Yii::t('app', 'Agenda'), 'icon' => 'calendar', 'url' => ['/site/index']],
                	['label' => Yii::t('app', 'Patients'), 'icon' => 'users', 'url' => ['/patients/index']],
                	['label' => Yii::t('app', 'Prescriptions'), 'icon' => 'file-o', 'url' => ['/prescriptions/index']],
                	['label' => Yii::t('app', 'Profile'), 'icon' => 'user-circle', 'url' => ['/doctors/profile']],
                	['label' => Yii::t('app', 'Appointments'), 'icon' => 'th-list', 'url' => ['/appointments/index']],
                	['label' => Yii::t('app', 'Commissions'), 'icon' => 'usd', 'url' => ['/commissions/index']],
                	['label' => Yii::t('app', 'Payments'), 'icon' => 'money', 'url' => ['/doctor-payments/index']],
                	['label' => Yii::t('app', 'Support'), 'icon' => 'question-circle', 'url' => ['#'], 'options' => ['onclick' => 'return gotoChat(1);']],
                    $debugTools
                ],
            ]
        ) ?>

    </section>

</aside>
