<?php
namespace app\assets;

use yii\web\AssetBundle;

// http://plugins.krajee.com/file-basic-usage-demo
class FileinputAsset extends AssetBundle
{
	public $sourcePath = '@smartform/assets/fileinput';
	//public $basePath = '@webroot';
	//public $baseUrl = '@web';
	public $css = [
		'fileinput.min.css',
	];
	public $js = [
		'fileinput.min.js',
		'locales/es.js',
	];
	public $depends = [ '\yii\web\JqueryAsset' ];
}
