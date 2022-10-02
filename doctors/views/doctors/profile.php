<?php
/* @var $this yii\web\View */
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Profile');
$officesTabClass = 
$officesViewClass =
$operatingRoomsTabClass =
$operatingRoomsViewClass =
$hoursTabClass = 
$hoursViewClass = 
$profileTabClass = 
$profileViewClass = '';
switch ($activePage){
	case 'offices':		
		$officesTabClass = ' class="active"';
		$officesViewClass = ' in active';
		break;
	case 'operating_rooms':
		$operatingRoomsTabClass = ' class="active"';
		$operatingRoomsViewClass = ' in active';
		break;
	case 'hours':
		$hoursTabClass = ' class="active"';
		$hoursViewClass = ' in active';
		break;
	default:
		$profileTabClass = ' class="active"';		
		$profileViewClass = ' in active';
		break;
}
?>

<div class="doctors-profile">
	<div class="panel">
		<div class="panel panel-header">
			<div class="row">
				<div class="col-xs-12 panel-header-text">
					<span class="text-primary-2 text-bold"><?= Html::a( Yii::t('app', 'Start'), Url::home(), ['class'=> "text-primary-2 text-bold"] ) ?></span><span class="text-primary-1 text-bold"> / <?= Yii::t('app', 'Profile') ?></span>
				</div>				
			</div>		
		</div>
		
		<div class="panel-body">
			<ul id="profile_view_select" class="nav nav-tabs">
				<li id="profile_tab"<?= $profileTabClass ?>><a id="profile_tab_link" href="#profile_view" data-toggle="tab"><?= Yii::t('app', 'Profile') ?></a></li>
			    <li id="offices_tab"<?= $officesTabClass ?>><a id="offices_tab_link" href="#offices_view" data-toggle="tab"><?= Yii::t('app', 'Offices') ?></a></li>
			    <li id="operating_rooms_tab"<?= $operatingRoomsTabClass ?>><a id="operating_rooms_tab_link" href="#operating_rooms_view" data-toggle="tab"><?= Yii::t('app', 'Operating Rooms') ?></a></li>
			    <li id="hours_tab"<?= $hoursTabClass ?>><a id="hours_tab_link" href="#hours_view" data-toggle="tab"><?= Yii::t('app', 'Hours') ?></a></li>
			</ul>
			
			<div class="tab-content">
			  	<div id="profile_view" class="tab-pane fade<?= $profileViewClass ?>">
			   		<?= $this->render('_profile_view') ?>
			  	</div>
			  	<div id="offices_view" class="tab-pane fade<?= $officesViewClass ?>">
			   	 	<?= $this->render('_offices_view',  ['officesSearchModel' => $officesSearchModel, 'officesDataProvider' => $officesDataProvider]) ?>
			  	</div>
			  	<div id="operating_rooms_view" class="tab-pane fade<?= $operatingRoomsViewClass ?>">
			   	 	<?= $this->render('_operating_rooms_view',  ['operatingRoomsSearchModel' => $operatingRoomsSearchModel, 'operatingRoomsDataProvider' => $operatingRoomsDataProvider]) ?>
			  	</div>
			  	<div id="hours_view" class="tab-pane fade<?= $hoursViewClass ?>">
			   	 	<?= $this->render('_hours_view', ['model' => $doctorHoursForm]) ?>
			  	</div>
			</div>
		</div>
	</div>
</div>
		
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
		