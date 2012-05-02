<?php
/**
 * This model is basically responsible for reading about all the blog posts for the sandbox and 
 * Lithium in general. These blog posts often include tutorials that you can follow along with using
 * the sandbox code and perhaps a specific branch.
 * 
 * You won't be able to write to this database of course, because you will be connecting with
 * read-only access...So this model is not really important for you to work with. In fact, if you
 * edit anything in here, you could run into trouble later on.
 * 
*/
namespace app\models;

class SandboxArticle extends BaseModel {
	
	protected $_meta = array(
		'locked' => true,
		'connection' => 'sandbox_master',
		'source' => 'articles',
	);
	
	protected $_schema = array(
		'_id' => array('type' => 'id'),
		'title' => array('type' => 'string'),
		'description' => array('type' => 'string'),
		// pretty url
		'url' => array('type' => 'url'),
		// link out to blog post/article
		'link' => array('type' => 'string'),
		'tags' => array('type' => 'array'),
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