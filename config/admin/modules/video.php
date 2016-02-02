<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'a2' => array(
		'resources' => array(
			'video_element_controller' => 'module_controller',
			'video' => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access_1' => array(
					'role' => 'base',
					'resource' => 'video_element_controller',
					'privilege' => 'access',
				),
			
				'video_edit_1' => array(
					'role' => 'super',
					'resource' => 'video',
					'privilege' => 'edit',
				),
				'video_edit_2' => array(
					'role' => 'base',
					'resource' => 'video',
					'privilege' => 'edit',
					'assertion' => array('Acl_Assert_Argument', array(
						'site_id' => 'site_id'
					)),
				),
				'video_hide' => array(
					'role' => 'full',
					'resource' => 'video',
					'privilege' => 'hide',
					'assertion' => array('Acl_Assert_Site', array(
						'site_id' => SITE_ID,
						'site_id_master' => SITE_ID_MASTER
					)),
				),
			),
			'deny' => array(
			)
		)
	),
);