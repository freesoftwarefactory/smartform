<?php
namespace app\assets;

use yii\web\AssetBundle;

// credits: https://eonasdan.github.io/bootstrap-datetimepicker/
class DatetimepickerAsset extends AssetBundle
{
	public $sourcePath = '@smartform/assets/datepicker';
	//public $basePath = '@webroot';
	//public $baseUrl = '@web';
	public $css = [
		'bootstrap-datetimepicker.css',
	];
	public $js = [
		'moment.js',
		'bootstrap-datetimepicker.js',
	];
	public $depends = [ '\yii\web\JqueryAsset' ];
}
