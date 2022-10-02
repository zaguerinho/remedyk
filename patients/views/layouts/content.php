<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;

?>
<div class="content-wrapper">
    <section class="content-header">
        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1><?= $this->blocks['content-header'] ?></h1>
        <?php } ?>

        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content">    	 
        <?= Alert::widget() ?>
        <div class="container-fluid">
        	<?= $content ?>
        </div>
    </section>
</div>

<?php if (Yii::$app->user->isGuest) : ?>
<footer class="main-footer">
   <div class="container-fluid">
    	<div class="row">
    		<div class="col-xs-12 text-center">
    			&copy; <?= date('Y') ?> Remedyk | 
    			<a class="white-text" href="/site/terms-of-use"><?= Yii::t('app', 'Terms of use') ?></a> | 
    			<a class="white-text" href="/site/privacy-policy"><?= Yii::t('app', 'Privacy policy') ?></a>
    		</div>
    	</div>
    	
    </div>
</footer>
<?php endif; ?>
<?php if (!Yii::$app->user->isGuest) :?>
	<?= $this->render('@common/partials/_chat_view'); ?>
<?php endif; ?>