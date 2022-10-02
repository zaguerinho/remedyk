<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini"><img src="/images/logo_bw.png" /></span><span class="logo-lg"><img src="/images/logo_bw.png" /> Remedyk</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
             
                <li>
                    <a href="/site/login" class="btn btn-outline white-text btn-sm">Log in</a>
                </li>
               
            </ul>
        </div>
    </nav>
</header>
