<?php
/**
 * This model is basically responsible for reading about all the videos and slides for the sandbox
 * and Lithium in general. These videos often include talks at events and meetups that may reference
 * the sandbox and a particlar branch.
 * 
 * You won't be able to write to this database of course, because you will be connecting with
 * read-only access...So this model is not really important for you to work with. In fact, if you
 * edit anything in here, you could run into trouble later on.
 * 
*/
namespace app\models;

class SandboxPresentation extends BaseModel {
	
	protected $_meta = array(
		'locked' => true,
		'connection' => 'sandbox_master',
		'source' => 'presentations',
	);
	
	protected $_schema = array(
		'_id' => array('type' => 'id'),
		'title' => array('type' => 'string'),
		'description' => array('type' => 'string'),
		// pretty url
		'url' => array('type' => 'url'),
		// link out to video
		'link' => array('type' => 'string'),
		'embed' => array('type' => 'string'),
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