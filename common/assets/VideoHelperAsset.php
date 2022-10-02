<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Rafa <jrcoelloalba@gmail.com>
 * @since 2.0
 */
class VideoHelperAsset extends AssetBundle
{
	public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
	public $sourcePath = '@common/jslibs/video-helper';
	public $css = [
	];
	public $js = [
			'js/videohelper.js',
	];
	public $depends = [
			'yii\web\YiiAsset',
	];
}
