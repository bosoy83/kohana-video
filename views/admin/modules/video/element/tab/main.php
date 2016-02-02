<?php defined('SYSPATH') or die('No direct access allowed.');

	$orm = $helper_orm->orm();
	$labels = $orm->labels();
	$required = $orm->required_fields();

/**** for_all ****/

	if (IS_MASTER_SITE) {
		echo View_Admin::factory('form/checkbox', array(
			'field' => 'for_all',
			'errors' => $errors,
			'labels' => $labels,
			'required' => $required,
			'orm_helper' => $helper_orm,
		));
	}	
	
/**** active ****/
	
	echo View_Admin::factory('form/checkbox', array(
		'field' => 'active',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'orm_helper' => $helper_orm,
	));
	
/**** title ****/
	
	echo View_Admin::factory('form/control', array(
		'field' => 'title',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::input('title', $orm->title, array(
			'id' => 'title_field',
			'class' => 'input-xxlarge',
		)).$preview_link,
	));
	
/**** uri ****/
	
	echo View_Admin::factory('form/control', array(
		'field' => 'uri',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::input('uri', $orm->uri, array(
			'id' => 'uri_field',
			'class' => 'input-xxlarge',
		)),
	));

/**** public_date ****/
	
	echo View_Admin::factory('form/date', array(
		'field' => 'public_date',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'orm' => $orm,
	));
	
/**** image ****/
	
	echo View_Admin::factory('form/image', array(
		'field' => 'image',
		'value' => $orm->image,
		'orm_helper' => $helper_orm,
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
	));
	

/**** iframe_code ****/
	
	echo View_Admin::factory('form/control', array(
		'field' => 'iframe_code',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::textarea('iframe_code', $orm->iframe_code, array(
			'id' => 'iframe_code_field',
			'class' => 'text-area-clear',
		)),
	));