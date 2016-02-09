<?php defined('SYSPATH') or die('No direct script access.');

class Model_Video extends ORM_Base {

	protected $_table_name = 'video';
	protected $_sorting = array('public_date' => 'desc');
	protected $_deleted_column = 'delete_bit';
	protected $_active_column = 'active';

	public function labels()
	{
		return array(
			'title' => 'Title',
			'uri' => 'URI',
			'image' => 'Image',
			'iframe_code' => 'iframe code',
			'description' => 'Description',
			'active' => 'Active',
			'for_all' => 'For all sites',
			'public_date' => 'Public date',
		);
	}

	public function rules()
	{
		return array(
			'id' => array(
				array('digit'),
			),
			'site_id' => array(
				array('not_empty'),
				array('digit'),
			),
			'title' => array(
				array('not_empty'),
				array('max_length', array(':value', 255)),
			),
			'uri' => array(
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 100 )),
				array('alpha_dash'),
				array(array($this, 'check_uri')),
			),
			'image' => array(
				array('max_length', array(':value', 255)),
			),
			'iframe_code' => array(
				array('not_empty'),
			),
			'public_date' => array(
				array('date'),
			),
		);
	}

	public function filters()
	{
		return array(
			TRUE => array(
				array('trim'),
			),
			'title' => array(
				array('strip_tags'),
			),
			'active' => array(
				array(array($this, 'checkbox'))
			),
			'for_all' => array(
				array(array($this, 'checkbox'))
			),
		);
	}
	
	public function apply_mode_filter()
	{
		parent::apply_mode_filter();
	
		if($this->_filter_mode == ORM_Base::FILTER_FRONTEND) {
			$this
				->where($this->_object_name.'.public_date', '<=', date('Y-m-d H:i:00'));
		}
	}
	
	public function check_uri($value)
	{
		if ( ! $this->active) {
			return TRUE;
		}
	
		$orm = clone $this;
		$orm->clear();
	
		if ($this->loaded()) {
			$orm
				->where('id', '!=', $this->id);
		}
	
		if ($this->for_all) {
			$orm
				->site_id(NULL);
		}
	
		$orm
			->where('uri', '=', $this->uri)
			->where('active', '>', 0)
			->find();
	
		return ! $orm->loaded();
	}
}
