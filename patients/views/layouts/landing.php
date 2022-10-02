<?php
use yii\helpers\Html;
use common\assets\MaterialAdminLteAsset;
use common\helpers\MaterialAdminLteHelper;
use common\assets\AdminLtePluginsAsset;

/* @var $this \yii\web\View */
/* @var $content string */
	
	
	MaterialAdminLteAsset::register($this);
	AdminLtePluginsAsset::register($this);
	if (class_exists('patients\assets\AppAsset')) {
		patients\assets\AppAsset::register($this);
	} else {
		app\assets\AppAsset::register($this);
	}
	
	$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@common/jslibs/material-admin-lte');
	?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition <?= MaterialAdminLteHelper::skinClass() ?> sidebar-mini wysihtml5-supported fixed landing-body" style="height: auto;">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'landing-header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>
    

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
