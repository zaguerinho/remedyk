<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Rafa <jrcoelloalba@gmail.com>
 * @since 2.0
 */
class GlisseAsset extends AssetBundle
{
	public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
	public $sourcePath = '@common/jslibs/glisse';
	public $css = [
			'css/glisse.css',
	];
	public $js = [
			'js/glisse.js',
	];
	public $depends = [
			'yii\web\YiiAsset',
	];
}
