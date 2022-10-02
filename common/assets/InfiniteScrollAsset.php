<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InfiniteScrollAsset extends AssetBundle
{
	public $sourcePath = '@common/jslibs/infinite-scroll';
	public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
	
	public $js = [
			'js/jquery.infinitescroll.js',
	];
	public $css = [
			'css/jquery.infinitescroll.css',
	];
	
	public $depends = [
			'yii\web\YiiAsset',
	];
}
