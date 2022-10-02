<?php 

use common\models\SearchForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Welcome to Remedyk');
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="panel">
			<div class="panel-head">
				<div class="row">
					<div class="col-xs-12">
						<h2 class="panel-header-text"><?= Yii::t('app', 'Find the best doctor') ?></h2>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<?php 
					$search = new SearchForm() ;
					echo $this->render('/search/_search_box',['search' => $search]);
				?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 text-center">
		<h2 class="white-text"><?= Yii::t('app', 'Where doctors and patients connect') ?></h2>
	</div>
</div>
<br>
<div class="row">
	<div class="xol-xs-12 text-center">
		<iframe allowFullScreen="allowFullScreen" class="embeded-video" src="https://www.youtube.com/embed/saWTd91G-n8"></iframe>
	</div>
</div>
<br>
<div class="row">
	<div class="col-xs-12 text-center">
		<span class="white-text"><?= Yii::t('app', 'Learn how Remedyk helps doctors and patients connect easier across borders.') ?></span>
	</div>
</div>