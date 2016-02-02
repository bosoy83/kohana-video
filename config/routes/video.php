<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	'video' => array(
		'uri_callback' => '(/<uri>-<id>.html)(?<query>)',
		'regex' => array(
			'uri' => '[^/.,;?\n]+',
			'id' => '[0-9]+',
		),
		'defaults' => array(
			'directory' => 'modules',
			'controller' => 'video',
			'action' => 'index',
		)
	),
);

