
# Setup

1. Install this class to make it visible

		cd /your/app/components;
		ln -s ../vendor/freesoftwarefactory/smartform/SmartformWidget.php .

2. Setup a config file by installing it in your 'params' section,

		// copy the template from ./template/smartform.php to:
		'field-groups'=>require(__DIR__.'/my-smart-form-settings.php'),

# Usage

```
[php]

	use yii\widgets\ActiveForm;
	
    $form = ActiveForm::begin();
	echo \app\components\SmartformWidget::widget([
		'config_entry'=>'field-groups',
		'form_id'=>'form-1',
		'active_form'=>$form,
		'model'=>$model,
	]);
	ActiveForm::end();
```


# Callback Examples

Sometimes a callback is required to get values from it.

## Callback Example: Read select options from database.

```
[php]

	<?php
	function _somecallback($_call,$_model,$_field_name){
		if('select-options' == $_call){
			if('some_custom_field_name'==$_field_name){
				$options = [''=>'--Choose--'];
				foreach(\app\models\Options::all() as $item)
					$options[$item->id] = $item->text;
				return $options;
			}
		}
	}
	?>

	...html...
	<?=\app\components\SmartformWidget::widget([
		...
		'callback'=>'_somecallback',
		...
	]);?>

```

## Callback Example: File Uploader

Smartform contain a special field type called: 'upload_file', used to implement
a smart file uploader, it requires some help via callback to get it work:

```
[php]

function file_uploader_callback($_call, $_model, $_field_name){
	if('after_label'==$_call){
		if('some_field_name'==$_field_name){
			return "<p>Please upload a PNG picture</p>";
		}
	}
	if('get_file_upload_url'==$_call){
		// the URL that will receive the $_FILES post.
		//
		return Url::toRoute(['some_ajax_based_action']);
	}
	if('instance_files'==$_call){
		if('some_field_name'==$_field_name){
			// see the next code snippet (below this lines) 
			//
			if($objects = \Yii::$app->mymediamanager->enumGraphics(
				$_model->some_instance, $_field_name)){
				foreach($objects as &$item)	
					$item['delete_url'] = 
						Url::toRoute(['ajaxremove','d'=>$item['id']]);
			}
			return $media;
		}
	}
   
 }

 // the enumGraphics exposed by mymediamanager:

	public function enumGraphics($parent,$usage){
		$list = [];
		if($all = \app\models\Media::all($parent)){
			foreach($all as $media){
				if($media->usage != $usage) continue;
				$hash = base64_encode(json_encode(
					[$parent,$usage,$media->codigo]));
				$list[] = [
					"id"=>$media->id,
					"file_name"=>$media->file_name_only,
					"file_path"=>$media->long_file_local_path,
					"preview_url"=>
						Url::toRoute(['/media/preview','f'=>$hash]),
					"delete_url"=>[], // should be defined by the caller
				];
			}
		}
		return $list;
	}

// The POSTED $_FILES and $_POST will look like this:

		[FILES] => Array(
			[SomeModelName] => Array (
				[name] => Array(
					[some_field_name] => somepicture.jpg
				)
				[type] => Array(
					[some_field_name] => image/jpeg
				)
				[tmp_name] => Array(
					[some_field_name] => /tmp/php1298s1ff1
				)
				[error] => Array(
					[some_field_name] => 0
				)
				[size] => Array(
					[some_field_name] => 23654
				)
			)
		)
		[POST] => Array(
				[file_id] => 0
				[model_id] => 'SOME UNIQUE ID'
				[field_name] => some_field_name
			)

 ```


