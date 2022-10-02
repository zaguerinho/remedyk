<?php
use yii\helpers\Html;
use common\assets\MaterialAdminLteAsset;
use common\helpers\MaterialAdminLteHelper;
use common\assets\AdminLtePluginsAsset;

/* @var $this \yii\web\View */
/* @var $content string */


if (Yii::$app->controller->action->id === 'login') {
	/**
	 * Do not use this code in your template. Remove it.
	 * Instead, use the code  $this->layout = '//main-login'; in your controller.
	 */
	echo $this->render(
			'main-login',
			['content' => $content]
			);
} else {
	
	echo $this->render('@common/partials/_socket_functions');
	MaterialAdminLteAsset::register($this);
	AdminLtePluginsAsset::register($this);

	doctors\assets\AppAsset::register($this);
	
	$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@common/jslibs/material-admin-lte');
	?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition <?= MaterialAdminLteHelper::skinClass() ?> sidebar-mini wysihtml5-supported fixed" style="height: auto;">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>
		
		
        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>
        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>