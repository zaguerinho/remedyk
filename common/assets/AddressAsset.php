<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AddressAsset extends AssetBundle
{
	public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
	public $sourcePath = '@common/jslibs/address';
	public $css = [
			'css/address.css',
	];
	public $js = [
			'js/address.js',
	];
	public $depends = [
			'yii\web\YiiAsset',
	];
}
