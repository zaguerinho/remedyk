<?php

/* @var $this \yii\web\View */
use yii2fullcalendar\yii2fullcalendar;
use yii\bootstrap\Html;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Json;
$months = [
	'1'=> Yii::t('app', 'January'),
	'2'=> Yii::t('app', 'February'),
	'3'=> Yii::t('app', 'March'),
	'4'=> Yii::t('app', 'April'),
	'5'=> Yii::t('app', 'May'),
	'6'=> Yii::t('app', 'June'),
	'7'=> Yii::t('app', 'July'),
	'8'=> Yii::t('app', 'August'),
	'9'=> Yii::t('app', 'September'),
	'10'=> Yii::t('app', 'October'),
	'11'=> Yii::t('app', 'November'),
	'12'=> Yii::t('app', 'December'),		
];

$years = [];
$yearLast = date('Y', strtotime(date('Y').'+1 year'));
$yearInitial = 2016;




if (!isset($doctor_id)){
	$doctor_id = null;
}
if (!isset($patient_id)){
	$patient_id = null;
}
if (!isset($office_id)){
	$office_id = null;
}

$extra_params = ['doctor_id' =>  $doctor_id, 'patient_id' => $patient_id, 'office_id' => $office_id, 'month' => "$('#{$id}').fullCalendar('getDate').format('M')", 'year' => "$('#{$id}').fullCalendar('getDate').format('YYYY')"];
if (isset($ajaxParams)){
	$extra_params = ArrayHelper::merge($extra_params, $ajaxParams);
}

$extra_params = Json::encode($extra_params);
$extra_params = str_replace('"', '', $extra_params);

for ($year = $yearLast; $year >= $yearInitial; $year--){
	$years[$year] = $year; 	
}


$css = <<<CSS
	.skin-remedyk .doctor-calendar-header-text .form-group {
		margin: 8px 0 0 0;
	}

	.skin-remedyk .doctor-calendar-header-text select.form-control {
		font-family: 'Amplesoft';
		font-size: 20px;	
		color: #2A377F;	
		background-image: linear-gradient(#2A377F,#2A377F),linear-gradient(#2A377F,#2A377F);
		box-shadow: none;
		background-size: 100% 2px, 100% 1px;
	}

	.skin-remedyk .doctor-calendar-header-buttons button.btn.btn-secondary {
		padding: 10px 4px;
	}
CSS;

$this->registerCss($css);


$js = <<<JS
	function setDate_{$id}(calendar){
		let year = calendar.fullCalendar('getDate').format('YYYY');
		let month = calendar.fullCalendar('getDate').format('M');
		let id = calendar.attr('id');
		$('#'+id+'_month').val(month);
		$('#'+id+'_year').val(year);

		$('#{$id}_prev_btn').attr('disabled', (calendar.fullCalendar('getDate').format('YYYY-M-D') == '{$yearInitial}-1-1'));
		
		let view = calendar.fullCalendar('getView');
		let lastDay = '1';
		switch (view.name){
			case 'agendaDay':
				$('#{$id}_day_tab').tab('show');
				lastDay = '31';
				break;
			case 'agendaWeek':
				$('#{$id}_week_tab').tab('show');
				lastDay = (parseInt($('#{$id}').fullCalendar('getDate').format('D'))+7 > 31) ? $('#{$id}').fullCalendar('getDate').format('D') : 31;
				break;
			case 'month':
				$('#{$id}_month_tab').tab('show');
				lastDay = '1';
				break;
		}
		$('#{$id}_next_btn').attr('disabled', (calendar.fullCalendar('getDate').format('YYYY-M-D') == '{$yearLast}-12-'+lastDay));
	}
	$('#{$id}_prev_btn').click(function() {
	    let calendar = $('#{$id}');
		calendar.fullCalendar('prev');
		if (calendar.fullCalendar('getDate').format('YYYY') < {$yearInitial}){
			calendar.fullCalendar('next');
			$('#{$id}_prev_btn').attr('disabled', true);
		}		
	});

	$('#{$id}_next_btn').click(function() {
		let calendar = $('#{$id}');
	    calendar.fullCalendar('next');
		if (calendar.fullCalendar('getDate').format('YYYY') > {$yearLast}){
			calendar.fullCalendar('prev');
			$('#{$id}_next_btn').attr('disabled', true);
		}	
	});

	$('#{$id}_month, #{$id}_year').change(function(){
		let calendar = $('#{$id}');
		let date = moment($('#{$id}_year').val()+'-'+$('#{$id}_month').val(), 'YYYY-M');
		calendar.fullCalendar('gotoDate', date);
	});

	$('#{$id}_view_month').on('click', function(e){
		e.preventDefault();
		let calendar = $('#{$id}');
		calendar.fullCalendar('changeView', 'month');
	});
	
	$('#{$id}_view_week').on('click', function(e){
		e.preventDefault();
		let calendar = $('#{$id}');
		calendar.fullCalendar('changeView', 'agendaWeek');
	});

	$('#{$id}_view_day').on('click', function(e){
		e.preventDefault();
		let calendar = $('#{$id}');
		calendar.fullCalendar('changeView', 'agendaDay');
		
	});
	
JS;
$this->registerJs($js, View::POS_READY, "js_calendar_{$id}");
?>
<div class="row">
	<div class="col-xs-2 doctor-calendar-header-buttons">
		<button id="<?= $id ?>_prev_btn" class="btn btn-secondary"><i class="fa fa-chevron-left"></i></button>
	</div>
	
	<div class="col-xs-5 text-right doctor-calendar-header-text">		
		<?= Html::dropDownList($id.'_month',null, $months, ['class' => 'form-control', 'id' => $id.'_month']) ?>
	</div>
	<div class="col-xs-3 text-left doctor-calendar-header-text">
		<?= Html::dropDownList($id.'_year',null, $years, ['class' => 'form-control', 'id' => $id.'_year']) ?>
	</div>
	
	<div class="col-xs-2 text-right doctor-calendar-header-buttons">
		<button id="<?= $id ?>_next_btn" class="btn btn-secondary"><i class="fa fa-chevron-right"></i></button>
	</div>
</div>


<ul id="<?= $id ?>_view_select" class="nav nav-tabs">
	<li id="<?= $id ?>_month_tab" class="active"><a id="<?= $id ?>_view_month" href="" data-toggle="tab"><?= Yii::t('app', 'Months') ?></a></li>
    <li id="<?= $id ?>_week_tab"><a id="<?= $id ?>_view_week" href="" data-toggle="tab"><?= Yii::t('app', 'Weeks') ?></a></li>
    <li id="<?= $id ?>_day_tab"><a id="<?= $id ?>_view_day" href="" data-toggle="tab"><?= Yii::t('app', 'Days') ?></a></li>
</ul>
<?php
$options = [
		'id' => $id,
		
	];

$clientOptions = ArrayHelper::merge($customOptions, [
		'views' => [
				'day' => ['columnFormat' => 'dddd, Do']
		]
]);
if (Yii::$app->language == 'en')
	$options['lang'] = 'en-us';

echo yii2fullcalendar::widget([
		'events' => [
						'url' => /*Yii::$app->params['doctorsDomain'].*/Url::to(['/appointments/ajax-calendar-events']),
						'data' => new JsExpression("function(){ return " . $extra_params . "; }"),
					],
		'eventRender' => new JsExpression("function(event, element){ if ($.inArray('secondary', event.className) == -1) {element.attr('data-toggle', 'tooltip').attr('role', 'modal-remote');}}"),
		'eventAfterAllRender' => new JsExpression("function(){setDate_{$id}($('#{$id}'));}"),
		'header' => [
				'left' => '',
				'center' => '',
				'right' => ''
				
		],
		'options' => $options,
		'clientOptions' => $clientOptions
]);

?>

