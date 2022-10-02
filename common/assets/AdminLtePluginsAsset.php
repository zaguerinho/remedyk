<?php
namespace common\assets;

use yii\web\AssetBundle;
class AdminLtePluginsAsset extends AssetBundle
{
	public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
	public $js = [
			//'slimscroll/jquery.slimscroll.min.js',
			// more plugin Js here
	];
	public $css = [
			// more plugin CSS here
	];
	public $depends = [
			'dmstr\web\AdminLteAsset',
	];
}