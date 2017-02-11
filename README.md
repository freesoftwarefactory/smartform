
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

# How to Upload a File

1. in your config file define a field named "product_image" in this way:		

```
"product_image"=>"upload_one_picture", 
```

2. in the widget definition, add a inline callback. this is used to tell
the widget where to put the $_POST and $_FILES.

```
		echo \app\components\SmartformWidget::widget([
			'config_entry'=>'field-groups',
			'form_id'=>'some-form-id',
			'active_form'=>$form,
			'model'=>$model,
			'callback' => function($_call,$_model,$_fieldname){
				
				if('get_file_upload_url'==$_call){
					return \yii\helpers\Url::toRoute(['ajax-upload-product-image']);
				}

				if('instance_files' == $_call){
					$list = [];
					if('product_image'==$_fieldname) $list[] = [
						'id'=>1, 'file_name'=>'', 'file_path'=>'',
						'preview_url'=>'/media/landingpage/product/thumb/'.$_model->id,
						'delete_url'=>'',
					];
					return $list;
				}
			}
		]);
```

3. the controller which will receive the $_POST and $_FILES should declare
a action and some exceptions to CSRF:

```
	// methods required in the controller:

	public function beforeAction($action) {            
		if($action->id == 'ajax-upload-product-image') 
			$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}
	
	public function actionAjaxUploadProductImage(){
		if(!Yii::$app->request->isAjax) die('invalid ajax request');
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$model_id=filter_input(INPUT_POST,"model_id",FILTER_SANITIZE_STRING);
		
		// this will help you to get more information:
		Yii::info("UPLOAD_INFO\n", print_r(["POST"=>$_POST,"FILES"=>$_FILES],true));
		
		$model = $this->findModel($model_id);
		$tmp_file = $_FILES['Landingpage']['tmp_name']['product_image'];	
		$binary_data = file_get_contents($tmp_file);
		
		return [];
	}
```

4. only for your information, the LOG injected into the action will tell you something
like the following, so use this information in order to get your file:

```
$_POST = [
    'file_id' => '0'
    'model_id' => '1'
    'field_name' => 'product_image'
]
$_FILES = [
    'Landingpage' => [
        'name' => [
            'product_image' => 'foto_1_verde.jpg'
        ]
        'type' => [
            'product_image' => 'image/jpeg'
        ]
        'tmp_name' => [
            'product_image' => '/tmp/phppQhABh'
        ]
        'error' => [
            'product_image' => 0
        ]
        'size' => [
            'product_image' => 2597
        ]
    ]
]
```



