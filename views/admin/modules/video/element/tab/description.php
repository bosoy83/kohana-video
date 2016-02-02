<?php defined('SYSPATH') or die('No direct access allowed.');

	$orm = $helper_orm->orm();
	$labels = $orm->labels();
	$required = $orm->required_fields();
	
/**** description ****/
	
	echo View_Admin::factory('form/control', array(
		'field' => 'description',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::textarea('description', $orm->description, array(
			'id' => 'description_field',
			'class' => 'text_editor',
		)),
	));