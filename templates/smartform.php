<?php 
	$panel_ends = "
		</div>
	</div>
	";
	function panel_begins($text){
		return "
		<div class='panel panel-default'>
			<div class='panel-heading'>$text</div>
			<div class='panel-body'>
		";
	}
	return [
	'field-types'=>[
		'default_text'=>[
			'maxlength'=>50,
			'size'=>50,
			'type'=>'text',
			'class'=>'form-control',
		],
		'email'=>[
			'maxlength'=>50,
			'size'=>50,
			'type'=>'email',
			'class'=>'form-control',
		],
		'numeric'=>[
			'maxlength'=>6,
			'size'=>10,
			'type'=>'number',
			'class'=>'form-control',
			'min'=>0,
			'max'=>9999,
		],
		'memo'=>[
			'maxlength'=>4096,
			'cols'=>50,
			'rows'=>5,
			'type'=>'memo',
			'class'=>'form-control',
		],
		'select'=>[
			'type'=>'select',
			'class'=>'form-control',
		],
		'boolean'=>[
			'type'=>'select',
			'class'=>'form-control',
			'options'=>['0'=>'NO','1'=>'SI'],
		],
		'select_editable_1'=>[
			'type'=>'select_editable',
			'class'=>'form-control',
			'new_item'=>'sysoption:some_category:',  
					// this value is prepended to the new value
					// can be used later in a controller to
					// detect new items to be inserted in DB
		],
		'datepicker'=>[
			'type'=>'datepicker',
			'format'=>'DD-MM-YYYY',
		],
		'timepicker'=>[
			'type'=>'timepicker',
		],
		'upload_one_picture'=>[
			'type'=>'upload_file',
			'placeholder'=>'Suba un JPG, PNG o GIF',
			'file_types'=>['image'],//['image', 'html', 'text', 'video', 'audio', 'flash', 'object']
			'file_ext'=>['jpg','jpeg','png','gif'],
			'min_files'=>1,
			'max_files'=>1,
		],
		'upload_pictures'=>[
			'type'=>'upload_file',
			'placeholder'=>'Suba archivos JPG, PNG o GIF',
			'file_types'=>['image'],//['image', 'html', 'text', 'video', 'audio', 'flash', 'object']
			'file_ext'=>['jpg','jpeg','png','gif'],
			'min_files'=>1,
			'max_files'=>3,
		],
	],
	'forms'=>[
		'form1'=>[
			panel_begins("Panel 1"),
			'field_1'=>'default_text',
			'field_2'=>'boolean',
			'field_3'=>'memo',
			'field_3'=>'datepicker',
			$panel_ends,
			
			panel_begins("Panel 2"),
			'field_4'=>'upload_pictures',
			$panel_ends,
		],
	],
];
