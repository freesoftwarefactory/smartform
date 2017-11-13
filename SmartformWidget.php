<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

require_once('DatetimepickerAsset.php');
require_once('TimepickerAsset.php');
require_once('FileinputAsset.php');

/**
 	SmartformWidget

	A form builder, you define field-types in a config file and then
	create a form specifying which of those field-types it has.

	@author Cristian A. Salazar H. christiansalazarh@gmail.com chileshift.cl
 */
class SmartformWidget extends Widget {
	public $config_entry = 'smartform'; // see \Yii::$app->params['smartform']
	public $form_id = ''; // the form to be rendered, see 'forms' _config
	public $active_form = null;	// required
	public $model = null;	// required
	public $callback = null; // function($_call, $_model, $_fieldname) { };
	
	private $_config;

	public function init() {
		parent::init();
		\Yii::setAlias("@smartform","@vendor/freesoftwarefactory/smartform");
		$this->_config = \Yii::$app->params[$this->config_entry];
		if(null === $this->callback)
			$this->callback = function($_call, $_model, $_field_name){
				// 
			};
	}

	public function run() {
		if(!$form_fields = $this->getFormConfig($this->form_id))
			return "<pre>Invalid field config entry id.</pre>";
		return $this->build(
			$form_fields, $this->active_form, $this->model, $this->callback);
	}

	private function getFormConfig($form_id){
		if(isset($this->_config['forms'][$form_id])){
			$config = $this->_config['forms'][$form_id];
			return $config;
		}else{
			return null;
		}
	}

	private function build($form_fields, $form, $model,$callback=null){
		$r = "";
		
		$field_types = $this->_config['field-types'];
		foreach($form_fields as $field=>&$data){
			if(is_numeric($field) && is_string($data)) { 
				continue; 
			}
			if((!is_string($data)) && is_array($data)){
				foreach($data as $_field=>&$_data)
					if(isset($field_types[$_data])) $_data = $field_types[$_data];
			}else
			if(is_string($data) && isset($field_types[$data])) {
				$data = $field_types[$data];
			}
		}
	
		foreach($form_fields as $field=>&$data){ 
			if(is_numeric($field) && is_string($data)) { 
				// a separator:
				$r .= $data;
			}
			if(is_numeric($field) && is_array($data)){
				// a group of fields:
				$r .= "<div class='col-md-4'>";
				foreach($data as $_field=>$_data){
					$r .= "<div class='form-group form-group-ex $field'>";
					$html = $this->renderField(
						$form,$model,$_field,$_data,$callback);
					$r .= $html;
					$r .= "</div>";
				}
				$r .= "</div>";
			}
			if(is_string($field) && is_array($data)){
				// individual fields
				$html = $this->renderField(
					$form,$model,$field,$data,$callback);
				$r .= $html;
			}
		}
		
		return $r;
	}

	/**
	 
	 	@param ActiveForm $form
		@param FormModel $model
		@param $callback  function($_call,$_model,$field_name){}
	 */
	private function renderField($form,$model,$field_name,$data,$callback=null){
       	$html = ""; 
		$field = $form->field($model, $field_name);
		$_class = isset($data['class']) ? $data['class'] : '';
		$_class .= " form-field";
		$_class .= " form-field-".$field_name;
			
		$labels = $model->attributeLabels();
        $label = isset($labels[$field_name]) ? 
			$labels[$field_name] : $field_name;
        $_id = \yii\helpers\BaseHtml::getInputId($model,$field_name);
		$_mid = $model->getPrimaryKey();
		$after_label = $callback('after_label',$model,$field_name);

		$err=''; $err_class='has-error error';
		if(isset($model->errors[$field_name])){
			foreach($model->errors[$field_name] as $ee)
				$err .= $ee;
		}

		$view = \Yii::$app->controller->view;

		if('text'==$data['type'])
			$html = $field->textInput([
				'maxlength'=>$data['maxlength'],
				'size'=>$data['size'],
				'class'=>$_class,
			]);
		if('number'==$data['type'])
			$html = $field->textInput([
				'type'=>'number',
				'min'=>$data['min'],
				'max'=>$data['max'],
				'maxlength'=>$data['maxlength'],
				'size'=>$data['size'],
				'class'=>$_class,
			]);
		if('email'==$data['type'])
			$html = $field->textInput([
				'type'=>'email',
				'maxlength'=>$data['maxlength'],
				'size'=>$data['size'],
				'class'=>$_class,
			]);
		if('memo'==$data['type'])
			$html = $field->textArea([
				'rows'=>$data['rows'],
				'cols'=>$data['cols'],
				'maxlength'=>$data['maxlength'],
				'class'=>$_class,
			]);
		if('select'==$data['type']){
			$options = isset($data['options']) ? 
				$data['options'] : [''=>'No Options'];
			if($callback)
				if($ret = $callback('select-options',$model, $field_name))
					$options = $ret;
			$html = $field->dropDownList($options,[
				'class'=>$_class,
			]);
		}
		if('select_editable'==$data['type']){
			$options = isset($data['options']) ? 
				$data['options'] : [''=>'No Options'];
			$maxlen = isset($data['maxlength']) ? 
				$data['maxlength'] : '30';
			$new_item = isset($data['new_item']) ? $data['new_item'] : 'new:';
			if($callback)
				if($ret = $callback('select-options',$model, $field_name))
					$options = $ret;

			$html_options = ""; $cur_label = '';
			foreach($options as $value=>$text){
				$html_options .= "
					<li value='$value'><a href='#'>$text</a></li>";
				if($value == $model->$field_name)
					$cur_label = $text;
			}
        	
			$hidden_input = \yii\helpers\BaseHtml::activeInput(
        		'hidden',$model,$field_name, [
					'class'=>'form-control input_hidden']);
			$input_l = "<input type='text'"
				." value='".htmlentities($cur_label)."'"
				." maxlength=".$maxlen
				." class='form-control dropdown-toggle input_label'></input> ";

			$html = "
			<div class='form-group form-group-ex'>
				<label class='control-label'>{$label}</label>
				<div class='input-group editable-dropdown'>
					{$input_l}
					<ul class='dropdown-menu'>{$html_options}</ul>
					<span role='button' class='input-group-addon dropdown-toggle' 
					 data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
					<span class='caret'></span></span>
					{$hidden_input}
				</div>
			</div>
			";
			$view = \Yii::$app->controller->view;
			$view->registerJs("
				$('.editable-dropdown').each(function(i,w){
					var widget = $(w);
					widget.find('.dropdown-menu li a').click(function(e){
						e.preventDefault();
						var a = $(this);
						var li = a.parent();
						widget.find('.input_hidden').val(li.attr('value'));
						widget.find('.input_label').val(a.html());
					});//click li
					widget.find('.input_label').keyup(function(e){
						var hidden = widget.find('.input_hidden');
						var label = widget.find('.input_label').val();
						hidden.val('{$new_item}'+window.btoa(label));
					});
				});
			",\yii\web\View::POS_READY);
		}
		if('datepicker'==$data['type']){
			\app\assets\DatetimepickerAsset::register($view);
			$input = \yii\helpers\BaseHtml::activeInput(
				'text',$model,$field_name,['class'=>'form-control date']);
			$html = "
				<div class='form-group form-group-ex'>
				<label class='control-label'>{$label}</label>
				<div class='input-group date' id='datepicker-{$_id}'>
					{$input}
					<span class='input-group-addon'>
						<span class='glyphicon glyphicon-calendar'></span>
					</span>
				</div>
				<div class='help-block $err_class'>$err</div>
				</div>
			";
			$view->registerJs("

                $.fn.datepicker.dates['es'] = {
                    days: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves','Viernes', 'Sabado'],
                    daysShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                    daysMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Deciembre'],
                    monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    today: 'Hoy',
                    clear: 'Limpia',
                    format: 'dd/mm/yyyy',
                    titleFormat: 'MM yyyy',
                    weekStart: 0
                };
                
                $('#datepicker-{$_id}').datepicker({ 
					  format: '{$data['format']}'
                    , language: 'es'
                    , autoclose: true
                    , orientation: 'auto'
                    , zIndexOffset: 10000
				});
			");
		}
		if('timepicker'==$data['type']){
			\app\assets\TimepickerAsset::register($view);
			$input = \yii\helpers\BaseHtml::activeInput(
				'text',$model,$field_name,[
					'id'=>$_id,'class'=>'form-control input-small']);
			$html = "
				<div class='form-group form-group-ex'>
				<label class='control-label'>{$label}</label>
				<div class='input-group bootstrap-timepicker timepicker'>
					{$input}
				</div>
				<div class='help-block $err_class'>$err</div>
				</div>
			";
			$options = isset($data['options']) ? 
				json_encode($data['options']) : "";
			$view->registerJs("$('#{$_id}').timepicker($options);");
		}
		if('upload_file'==$data['type']){
			\app\assets\FileinputAsset::register($view);
			$is_multiple = ($data['min_files'] != $data['max_files']) &&
				($data['max_files'] != 1);
			$opts = ['class'=>'form-control'];
			$opts['multiple'] = $is_multiple;
        	$input = \yii\helpers\BaseHtml::activeInput(
        		'file',$model,$field_name, $opts);
        	$html = "
				<div class='form-group form-group-ex'>
        		<label class='control-label'>{$label}</label>
				{$after_label}
       			{$input}</input>
                
				<div class='help-block $err_class'>$err</div>
				</div>
        	";
			$allowedFileTypes =  json_encode($data['file_types']);
			$allowedFileExtensions =  json_encode($data['file_ext']);
			$upload_url = $callback('get_file_upload_url',$model,$field_name);
			// preview of current files:
			$initialPreview = [];
			$initialPreviewConfig = [];
			if($instance_files = $callback('instance_files',$model,$field_name)){
				foreach($instance_files as $file_data){
					$initialPreview[] = "<img src='"
						.$file_data["preview_url"]
							."' class='file-preview-image' />"; // <- site.css
					$initialPreviewConfig[] = [
						"caption"=>$file_data["file_name"],
						"url"=>$file_data["delete_url"],
						"key"=>$file_data['id'],
					];
				}
			}
			// ok
			$initialPreview = json_encode($initialPreview);
			$initialPreviewConfig = json_encode($initialPreviewConfig);
			// end preview
			$view->registerJs("

                setTimeout(function(){

				$('#{$_id}').fileinput({
					 showUpload: true
					,showPreview: true 
					,showBrowse: true
					,showRemove: false
					,showClose: false
					,uploadUrl: '$upload_url'
					,uploadAsync: true
					,uploadExtraData: { model_id : '{$_mid}' , field_name: '{$field_name}'}
					,initialCaption: '{$data['placeholder']}'
					,allowedFileTypes: $allowedFileTypes
					,allowedFileExtensions: $allowedFileExtensions
					,captionClass: 'form-control'
					,browseLabel: 'Examinar...'
					,removeLabel: 'Eliminar'
					,uploadLabel: 'Subirlo'
					,minFileCount: {$data['min_files']}
					,maxFileCount: {$data['max_files']}
					,msgInvalidFileType: 'Tipo de archivo invalido.'
					,msgInvalidFileExtension: 'La extension del archivo no es aceptada'
					,msgValidationError: 'No se pueden subir los archivos'
					,initialPreview: $initialPreview
					,initialPreviewConfig: $initialPreviewConfig
				});
				
                $('#{$_id}').on('filepredelete', function(jqXHR) {
					var abort = true;
					var resp = prompt('tipee SI para confirmar la eliminacion.');
					if ('si'==resp || 'SI'==resp) {
						abort = false;
					}
					return abort;
				});

                }, 1000);

			");
		}
  return $html;
	}
}

