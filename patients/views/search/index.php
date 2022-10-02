<?php
/* @var $this yii\web\View */
use common\models\SearchForm;
use common\models\Doctor;
use common\assets\InfiniteScrollAsset;
use yii\bootstrap\Modal;
use yii\web\View;
use johnitvn\ajaxcrud\CrudAsset;

InfiniteScrollAsset::register($this);
CrudAsset::register($this);

$this->title = Yii::t('app', 'Search results');
?>
<div class="panel search-results-panel">
	<div class="panel-head">
		<div class="row">
			<div class="col-xs-12">
				<h2 class="panel-header-text"><?= Yii::t('app', 'Find the best doctor') ?></h2>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-8">
				<?php 
					echo $this->render('/search/_search_box',['search' => $search]);
				?>
			</div>
		</div>
	</div>
</div>
<br>
<div class="panel">
	<div class="panel panel-head">
		<div class="row">
			<div class="col-xs-12">
			<div class="panel-header-text text-primary-2"><?= $results['count'] ?> <?= Yii::t('app','Doctors found') ?></div>
			</div>
		</div>
	</div>
	<div class="panel-body">
		
		<?php //if (isset($results)) : ?>
			<?php $pjax = \yii\widgets\Pjax::begin(); ?>
			<div class="search-results">
				<div class="row hidden-xs hidden-sm">
					<div class="col-md-2">
						<div class="col-md-2"></div>
						<div class="col-md-10 text-bold text-primary-1"><?= Yii::t('app', 'Doctor') ?></div>
					</div>
					<div class="col-md-2 text-bold text-primary-1"><?= Yii::t('app', 'Specialties') ?></div>
					<div class="col-md-2 text-bold text-primary-1"><?= Yii::t('app', 'Procedures') ?></div>
					<div class="col-md-2 text-bold text-primary-1"><?= Yii::t('app', 'Rating') ?></div>
					<div class="col-md-2"></div>
					<div class="col-md-2"></div>
				</div>
				<div class="separator no-padding hidden-xs hidden-sm"></div>
				<?php 
					
					foreach ($results['data'] as $office){
						echo '<div class="search-result">';
						echo $this->render('_search_result', ['office' => $office]);
						echo '</div>';
					} 
					
				?>
				
				<div class="pagination-wrap">
					<div class="pagination">
						<div class="next">
							<?php if (!empty($results['data'])): ?>
							<a href="<?= '/search/index?SearchForm[searchString]='.$search->searchString.'&SearchForm[searchAddress]='.$search->searchAddress.'&SearchForm[page]='.($results['page']+1) ?>">
								Next
							</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php \yii\widgets\Pjax::end(); ?> <!-- #pjax_wrap -->
			</div>
		<?php //else: ?>
		<?php //endif; ?>
	</div>
</div>

<?php 
$initInfiniteScroll = <<< JS
function initInfiniteScroll(){

	var options = {
		pagination: '.pagination-wrap',
		next: '.pagination .next a:first',
		item: '.search-result',
		state: {
				isPaused: true,
		},
		pjax: {
				container: '#$pjax->id',
		},
		bufferPx: 40,
		wrapper: '.search-results',
		alwaysHidePagination: true,
		container: 'window',
	};
	// init

	$('.search-results').on('infinitescroll:afterRetrieve', function(){
	    $('[data-krajee-rating]').rating(eval($('[data-krajee-rating]').attr('data-krajee-rating')));
	});
	$('.search-results').infinitescroll(options);
	// enable, paused by default
	$('.search-results').infinitescroll('start');
	
	
}


$(function(){
	initInfiniteScroll();
});
JS;


$this->registerJs($initInfiniteScroll, View::POS_END); ?>

<?php Modal::begin([
    "id"=>"ajaxCrudModal",
	//'size' => 'large',
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>