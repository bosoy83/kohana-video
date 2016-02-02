<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Video_Element extends Controller_Admin_Modules_Video {

	private $filter_type_options;

	public function before()
	{
		parent::before();

		$this->filter_type_options = array(
			'all' => __('all'),
			'own' => __('own'),
		);
	}

	public function action_index()
	{
		$orm = ORM::factory('video');
		
		$this->_apply_filter($orm);
			
		$paginator_orm = clone $orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(20)
			->count($paginator_orm->count_all());
		unset($paginator_orm);
		
		$list = $orm
			->paginator($paginator)
			->find_all();
		
		$this->template
			->set_filename('modules/video/element/list')
			->set('list', $list)
			->set('hided_list', $this->get_hided_list($orm->object_name()))
			->set('filter_type_options', $this->filter_type_options)
			->set('paginator', $paginator);
			
		$this->left_menu_element_add();
		$this->sub_title = __('List');;
	}

	private function _apply_filter($orm)
	{
		$filter_query = $this->request->query('filter');

		if ( ! empty($filter_query)) {
			$title = Arr::get($filter_query, 'title');
			if ( ! empty($title)) {
				$orm->where('title', 'like', '%'.$title.'%');
			}

			$type = Arr::get($filter_query, 'type');
			if ( ! empty($type) AND $type == 'own') {
				$orm->where('site_id', '=', SITE_ID);
			}
		}
	}

	public function action_edit()
	{
		$request = $this->request->current();
		$id = (int) $request->param('id');
		$helper_orm = ORM_Helper::factory('video');
		$orm = $helper_orm->orm();
		if ( (bool) $id) {
			$orm
				->where('id', '=', $id)
				->find();
		
			if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->title = __('Edit video');
		} else {
			$this->title = __('Add video');
		}
		
		if (empty($this->back_url)) {
			$query_array = array();
			$query_array = Paginator::query($request, $query_array);
			$this->back_url = Route::url('modules', array(
				'controller' => $this->controller_name['element'],
				'query' => Helper_Page::make_query_string($query_array),
			));
		}
		
		if ($this->is_cancel) {
			$request
				->redirect($this->back_url);
		}

		$errors = array();
		$submit = $request->post('submit');
		if ($submit) {
			try {
				if ( (bool) $id) {
					$orm->updater_id = $this->user->id;
					$orm->updated = date('Y-m-d H:i:s');
					$reload = FALSE;
				} else {
					$orm->site_id = SITE_ID;
					$orm->creator_id = $this->user->id;
					$reload = TRUE;
				}
				
				$values = $request->post();
				
				$values['public_date'] = $this->value_multiple_date($values, 'public_date');
				if (empty($values['uri']) OR row_exist($orm, 'uri', $values['uri'])) {
					$values['uri'] = transliterate_unique($values['title'], $orm, 'uri');
				}
				
				$helper_orm->save($values + $_FILES);
				
				if ($reload) {
					if ($submit != 'save_and_exit') {
						$this->back_url = Route::url('modules', array(
							'controller' => $request->controller(),
							'action' => $request->action(),
							'id' => $orm->id,
							'query' => Helper_Page::make_query_string($request->query()),
						));
					}
						
					$request
						->redirect($this->back_url);
				}
			} catch (ORM_Validation_Exception $e) {
				$errors = $this->errors_extract($e);
			}
		}

		// If add action then $submit = NULL
		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			$this->template
				->set_filename('modules/video/element/edit')
				->set('errors', $errors)
				->set('helper_orm', $helper_orm);
			
			$this->left_menu_element_add();
		} else {
			$request
				->redirect($this->back_url);
		}
	}
	
	public function action_delete()
	{
		$request = $this->request->current();
		$id = (int) $request->param('id');
		
		$helper_orm = ORM_Helper::factory('video');
		$orm = $helper_orm->orm();
		$orm
			->and_where('id', '=', $id)
			->find();
		
		if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
			throw new HTTP_Exception_404();
		}
		
		if ($this->element_delete($helper_orm)) {
			if (empty($this->back_url)) {
				$query_array = Paginator::query($request);
				$this->back_url = Route::url('modules', array(
					'controller' => $this->controller_name['element'],
					'query' => Helper_Page::make_query_string($query_array),
				));
			}
		
			$request
				->redirect($this->back_url);
		}
	}

	public function action_visibility()
	{
		$request = $this->request->current();
		$id = (int) $request->param('id');
		$mode = $request->query('mode');
		
		$orm = ORM::factory('video')
			->and_where('id', '=', $id)
			->find();
		
		if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'hide')) {
			throw new HTTP_Exception_404();
		}
		
		if ($mode == 'hide') {
			$this->element_hide($orm->object_name(), $orm->id);
		} elseif ($mode == 'show') {
			$this->element_show($orm->object_name(), $orm->id);
		}
		
		if (empty($this->back_url)) {
			$query_array = Paginator::query($request);
			$this->back_url = Route::url('modules', array(
				'controller' => $this->controller_name['element'],
				'query' => Helper_Page::make_query_string($query_array),
			));
		}
		
		$request
			->redirect($this->back_url);
	}
	
	public function action_view()
	{
		$request = $this->request->current();
		$id = (int) $request->param('id');
		$helper_orm = ORM_Helper::factory('video');
		$orm = $helper_orm->orm();
		$orm
			->where('id', '=', $id)
			->find();
			
		if ( ! $orm->loaded()) {
			throw new HTTP_Exception_404();
		}
	
		if (empty($this->back_url)) {
			$query_array = Paginator::query($request);
			$this->back_url = Route::url('modules', array(
				'controller' => $this->controller_name['element'],
				'query' => Helper_Page::make_query_string($query_array),
			));
		}
	
		$this->template
			->set_filename('modules/video/element/view')
			->set('helper_orm', $helper_orm);
	
		$this->title = __('Viewing');
		$this->left_menu_element_add();
	}
	
	protected function _get_breadcrumbs()
	{
		$breadcrumbs = parent::_get_breadcrumbs();
		
		$request = $this->request->current();
		$action = $request
			->action();
		if (in_array($action, array('edit', 'view'))) {
			$id = (int) $request->param('id');
			$element_orm = ORM::factory('video')
				->where('id', '=', $id)
				->find();
			if ($element_orm->loaded()) {
				switch ($action) {
					case 'edit':
						$_str = ' ['.__('edition').']';
						break;
					case 'view':
						$_str = ' ['.__('viewing').']';
						break;
					default:
						$_str = '';
				}
				
				$breadcrumbs[] = array(
					'title' => $element_orm->title.$_str,
				);
			} else {
				$breadcrumbs[] = array(
					'title' => ' ['.__('new video').']',
				);
			}
		}
		
		return $breadcrumbs;
	}
} 
