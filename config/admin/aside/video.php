<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'video' => array(
		'title' => __('Video list'),
		'link' => Route::url('modules', array(
			'controller' => 'video_element',
		)),
		'sub' => array(),
	),
);