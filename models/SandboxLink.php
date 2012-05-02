<?php
/**
 * This model is responsible for a listing of useful links. There are a lot of useful links out
 * there about Lithium.
 * 
 * You won't be able to write to this database of course, because you will be connecting with
 * read-only access...So this model is not really important for you to work with. In fact, if you
 * edit anything in here, you could run into trouble later on.
 * 
*/
namespace app\models;

class SandboxLink extends BaseModel {
	
	protected $_meta = array(
		'locked' => true,
		'connection' => 'sandbox_master',
		'source' => 'links',
	);
	
	protected $_schema = array(
		'_id' => array('type' => 'id'),
		'title' => array('type' => 'string'),
		'description' => array('type' => 'string'),
		'link' => array('type' => 'string'),
		'tags' => array('type' => 'array'),
		'published' => array('type' => 'boolean'),
		'created' => array('type' => 'date')
	);
	
	public $url_field = array('title');
	
	public $search_schema = array(
		'title' => array(
			'weight' => 1  
		),
		'description' => array(
			'weight' => 1
		)
	);
	
	public $validates = array(
		'title' => array(
			array('notEmpty', 'message' => 'Title cannot be empty.')
		),
		'link' => array(
			array('notEmpty', 'message' => 'Link cannot be empty.')
		)
	);
	
}
?>