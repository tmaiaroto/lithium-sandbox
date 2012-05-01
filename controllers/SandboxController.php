<?php
/**
 * A single controller for all sandbox information.
 * This will provide a way for you to read about all of the screen casts, blog posts, etc. that
 * go along with this sandbox.
 * 
*/
namespace app\controllers;

use app\models\SandboxArticle;
use app\models\SandboxScreencast;
use MongoId;
use MongoDate;
use MongoRegex;

class SandboxController extends \lithium\action\Controller {
	
	protected function _init() {
		parent::_init();
		
		$this->_render['layout'] = 'sandbox';
	}
	
	/**
	 * Basic static pages.
	 * 
	 * @return
	*/
	public function page() {
		$path = func_get_args() ?: array('home');
		// Look for templates in a "static" directory under the "sandbox" views directory.
		array_unshift($path, 'static');
		
		return $this->render(array('template' => join('/', $path)));
	}
	
	/**
	 * A listing of all remote screencasts along with the ability to search.
	 * 
	*/
	public function screencasts() {
		$conditions = array();
		
		// NOTE: the values within this array for "search" include things like "weight" etc. and are not yet fully implemented...But will become more robust and useful.
		if((isset($this->request->query['q'])) && (!empty($this->request->query['q']))) {
			$search_schema = SandboxScreencast::searchSchema();
			$search_conditions = array();
			// For each searchable field, adjust the conditions to include a regex
			foreach($search_schema as $k => $v) {
				$field = (is_string($k)) ? $k:$v;
				$search_regex = new MongoRegex('/' . $this->request->query['q'] . '/i');
				$conditions['$or'][] = array($field => $search_regex);
			}
		}
		
		$limit = 25;
		$page = $this->request->page ?: 1;
		$order = array('created' => 'desc');
		$total = SandboxScreencast::count(compact('conditions'));
		$documents = SandboxScreencast::all(compact('conditions','order','limit','page'));
		
		$page_number = (int)$page;
		$total_pages = ((int)$limit > 0) ? ceil($total / $limit):0;
		
		// Set data for the view template
		return compact('documents', 'total', 'page', 'limit', 'total_pages');
	}
	
	/**
	 * A listing of all remote articles and blog posts along with the ability to search.
	 * 
	*/
	public function articles() {
		$conditions = array();
		
		// NOTE: the values within this array for "search" include things like "weight" etc. and are not yet fully implemented...But will become more robust and useful.
		if((isset($this->request->query['q'])) && (!empty($this->request->query['q']))) {
			$search_schema = SandboxArticle::searchSchema();
			$search_conditions = array();
			// For each searchable field, adjust the conditions to include a regex
			foreach($search_schema as $k => $v) {
				$field = (is_string($k)) ? $k:$v;
				$search_regex = new MongoRegex('/' . $this->request->query['q'] . '/i');
				$conditions['$or'][] = array($field => $search_regex);
			}
		}
		
		$limit = 25;
		$page = $this->request->page ?: 1;
		$order = array('created' => 'desc');
		$total = SandboxArticle::count(compact('conditions'));
		$documents = SandboxArticle::all(compact('conditions','order','limit','page'));
		
		$page_number = (int)$page;
		$total_pages = ((int)$limit > 0) ? ceil($total / $limit):0;
		
		// Set data for the view template
		return compact('documents', 'total', 'page', 'limit', 'total_pages');
	}
	
}
?>