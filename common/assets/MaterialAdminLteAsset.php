<?php
namespace common\assets;

use yii\base\Exception;
use yii\web\AssetBundle;
/**
 * MaterialAdminLte AssetBundle
 * @since 0.1
 */
class MaterialAdminLteAsset extends AssetBundle
{
	public $sourcePath = '@common/jslibs/material-admin-lte';
	public $css = [
			'css/MaterialAdminLTE.min.css',
	];
	public $js = [
			//'js/app.min.js'
	];
	public $depends = [
			'dmstr\web\AdminLteAsset', // include adminLTE
			'exocet\BootstrapMD\MaterialAsset', // include css and js
			'exocet\BootstrapMD\MaterialIconsAsset', // include icons (optional)
			'exocet\BootstrapMD\MaterialInitAsset', // add $.material.init(); js (optional)
	];
	
	/**
	 * @var string|bool Choose skin color, eg. `'skin-blue'` or set `false` to disable skin loading
	 * @see https://almsaeedstudio.com/themes/AdminLTE/documentation/index.html#layout
	 */
	public $skin = 'all-md-skins';
	
	/**
	 * @inheritdoc
	 */
	public function init()
	{
		// Append skin color file if specified
		if ($this->skin) {
			if (('all-md-skins' !== $this->skin) && (strpos($this->skin, 'skin-') !== 0)) {
				throw new Exception('Invalid skin specified');
			}
			
			$this->css[] = sprintf('css/skins/%s.min.css', $this->skin);
		}
		
		parent::init();
	}
}
