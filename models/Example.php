<?php
namespace app\models;

class Example extends BaseModel {
	
	protected $_schema = array(
		'_id' => array('type' => 'id'),
		'name' => array('type' => 'string', 'form' => array('label' => 'Document Name')),
		'body' => array('type' => 'string', 'form' => array('label' => 'Body', 'type' => 'textarea')),
		'tags' => array('type' => 'array'),
		'owner_id' => array('type' => 'object'),
		'url' => array('type' => 'string', 'form' => array('label' => 'Pretty URL', 'position' => 'options', 'help_text' => 'Optional, manual pretty URL. One will be created based on the territory name otherwise.')),
		'modified' => array('type' => 'date'),
		'created' => array('type' => 'date')
	);
	
	protected $_meta = array(
		'locked' => true
	);
	
	public $validates = array(
		'name' => array(
			array('notEmpty', 'message' => 'You must provide a name for this document.')
		)
	);
	
	public $url_field = 'name';
	public $url_separator = '-';
	
	public $search_schema = array(
		'name' => array(
			'weight' => 1  
		)
	);
	
}
?>