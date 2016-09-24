<?php
namespace app\assets;

use yii\web\AssetBundle;

// https://jdewit.github.io/bootstrap-timepicker/
class TimepickerAsset extends AssetBundle
{
	public $sourcePath = '@smartform/assets/timepicker';
	//public $basePath = '@webroot';
	//public $baseUrl = '@web';
	public $css = [
		'bootstrap-timepicker.css', // a symlink to the .less provided file
	];
	public $js = [
		'bootstrap-timepicker.js',
	];
	public $depends = [ '\yii\web\JqueryAsset' ];
}
