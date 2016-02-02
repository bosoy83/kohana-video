<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Video extends Controller_Admin_Front {

	protected $module_config = 'video';
	protected $menu_active_item = 'modules';
	protected $title = 'Video';
	protected $sub_title = 'Video';
	
	protected $controller_name = array(
		'element' => 'video_element',
	);
	
	public function before()
	{
		parent::before();
	
		$request = $this->request;
		$query_controller = $request->query('controller');
		if ( ! empty($query_controller) AND is_array($query_controller)) {
			$this->controller_name = $request->query('controller');
		}
		$this->template
			->bind_global('CONTROLLER_NAME', $this->controller_name);
		
		$this->title = __($this->title);
		$this->sub_title = __($this->sub_title);
	}
	
	protected function layout_aside()
	{
		$menu_items = array_merge_recursive(
			Kohana::$config->load('admin/aside/video')->as_array(),
			$this->menu_left_ext
		);
		
		return parent::layout_aside()
			->set('menu_items', $menu_items);
	}

	protected function left_menu_element_add()
	{
		$this->menu_left_add(array(
			'video' => array(
				'sub' => array(
					'add' => array(
						'title' => __('Add video'),
						'link' => Route::url('modules', array(
							'controller' => $this->controller_name['element'],
							'action' => 'edit',
						)),
					),
				),
			),
		));
	}
	
	protected function _get_breadcrumbs()
	{
		return array(
			array(
				'title' => __('Video'),
				'link' => Route::url('modules', array(
					'controller' => $this->controller_name['element'],
				)),
			)
		);
	}
	
}

