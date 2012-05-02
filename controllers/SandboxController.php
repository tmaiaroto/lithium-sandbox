<?php
/**
 * A single controller for all sandbox information.
 * This will provide a way for you to read about all of the screen casts, blog posts, etc. that
 * go along with this sandbox.
 * 
*/
namespace app\controllers;

use app\models\SandboxArticle;
use app\models\SandboxLink;
use app\models\SandboxScreencast;
use app\models\SandboxPresentation;
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
		$conditions = array('published' => true);
		
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
		$conditions = array('published' => true);
		
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
	
	/**
	 * This action returns a JSON response with a listing of links based on the
	 * parameters posted to it.
	 * 
	 * @return string JSON
	*/
	public function links_list() {
		$start = microtime(true);
		
		// Only allow this action to be viewed as JSON
		$response = array('success' => false, 'result' => null, 'created' => null);
		if(!$this->request->is('json')) {
			return json_encode($response);
		}
		
		// Providing a comma separate list of tags will return only certain documents.
		$tags = isset($this->request->data['tags']) ? explode(',', $this->request->data['tags']):false;
		
		$conditions = array('published' => true);
		foreach($tags as $tag) {
			$conditions['$or'][] = array('tags' => trim($tag));
		}
		
		// Limit just in case. So things don't get out of hand one day.
		$limit = isset($this->request->data['limit']) ?$this->request->data['limit']:25;
		$page = $this->request->page ?: 1;
		$order = array('title' => 'asc');
		
		$total = SandboxLink::count(compact('conditions'));
		$documents = SandboxLink::all(compact('conditions','order','limit','page'));
		
		$page_number = (int)$page;
		$total_pages = ((int)$limit > 0) ? ceil($total / $limit):0;
		
		// Pagination info
		$response['page'] = $page;
		$response['total'] = $total;
		$response['limit'] = $limit;
		$response['total_pages'] = $total_pages;
		
		// The links
		if($total > 0) {
			$response['success'] = true;
			
			foreach($documents as $document) {
				$response['result'][] = array('title' => $document->title, 'description' => $document->description, 'link' => $document->link);
			}
		}
		
		// Stats
		$response['created'] = time();
		$response['took'] = microtime(true) - $start;
		
		return json_encode($response);
	}
	
	/**
	 * This action returns a JSON response with a listing of blog posts and articles based on the
	 * parameters posted to it.
	 * 
	 * @return string JSON
	*/
	public function articles_list() {
		$start = microtime(true);
		
		// Only allow this action to be viewed as JSON
		$response = array('success' => false, 'result' => null, 'created' => null);
		if(!$this->request->is('json')) {
			return json_encode($response);
		}
		
		// Providing a comma separate list of tags will return only certain documents.
		$tags = isset($this->request->data['tags']) ? explode(',', $this->request->data['tags']):false;
		
		$conditions = array('published' => true);
		foreach($tags as $tag) {
			$conditions['$or'][] = array('tags' => trim($tag));
		}
		
		// Limit just in case. So things don't get out of hand one day.
		$limit = isset($this->request->data['limit']) ?$this->request->data['limit']:25;
		$page = $this->request->page ?: 1;
		$order = array('created' => 'desc');
		
		$total = SandboxArticle::count(compact('conditions'));
		$documents = SandboxArticle::all(compact('conditions','order','limit','page'));
		
		$page_number = (int)$page;
		$total_pages = ((int)$limit > 0) ? ceil($total / $limit):0;
		
		// Pagination info
		$response['page'] = $page;
		$response['total'] = $total;
		$response['limit'] = $limit;
		$response['total_pages'] = $total_pages;
		
		// The links
		if($total > 0) {
			$response['success'] = true;
			
			foreach($documents as $document) {
				$response['result'][] = array('title' => $document->title, 'description' => $document->description, 'link' => $document->link);
			}
		}
		
		// Stats
		$response['created'] = time();
		$response['took'] = microtime(true) - $start;
		
		return json_encode($response);
	}
	
	/**
	 * This action returns a JSON response with a listing of screencasts based on the parameters 
	 * posted to it.
	 * 
	 * @return string JSON
	*/
	public function screencasts_list() {
		$start = microtime(true);
		
		// Only allow this action to be viewed as JSON
		$response = array('success' => false, 'result' => null, 'created' => null);
		if(!$this->request->is('json')) {
			return json_encode($response);
		}
		
		// Providing a comma separate list of tags will return only certain documents.
		$tags = isset($this->request->data['tags']) ? explode(',', $this->request->data['tags']):false;
		
		$conditions = array('published' => true);
		foreach($tags as $tag) {
			$conditions['$or'][] = array('tags' => trim($tag));
		}
		
		// Limit just in case. So things don't get out of hand one day.
		$limit = isset($this->request->data['limit']) ?$this->request->data['limit']:25;
		$page = $this->request->page ?: 1;
		$order = array('created' => 'desc');
		
		$total = SandboxScreencast::count(compact('conditions'));
		$documents = SandboxScreencast::all(compact('conditions','order','limit','page'));
		
		$page_number = (int)$page;
		$total_pages = ((int)$limit > 0) ? ceil($total / $limit):0;
		
		// Pagination info
		$response['page'] = $page;
		$response['total'] = $total;
		$response['limit'] = $limit;
		$response['total_pages'] = $total_pages;
		
		// The links
		if($total > 0) {
			$response['success'] = true;
			
			foreach($documents as $document) {
				$response['result'][] = array('title' => $document->title, 'description' => $document->description, 'link' => $document->link);
			}
		}
		
		// Stats
		$response['created'] = time();
		$response['took'] = microtime(true) - $start;
		
		return json_encode($response);
	}
	
	/**
	 * This action returns a JSON response with a listing of presentations based on the parameters 
	 * posted to it.
	 * 
	 * @return string JSON
	*/
	public function presentations_list() {
		$start = microtime(true);
		
		// Only allow this action to be viewed as JSON
		$response = array('success' => false, 'result' => null, 'created' => null);
		if(!$this->request->is('json')) {
			return json_encode($response);
		}
		
		// Providing a comma separate list of tags will return only certain documents.
		$tags = isset($this->request->data['tags']) ? explode(',', $this->request->data['tags']):false;
		
		$conditions = array('published' => true);
		foreach($tags as $tag) {
			$conditions['$or'][] = array('tags' => trim($tag));
		}
		
		// Limit just in case. So things don't get out of hand one day.
		$limit = isset($this->request->data['limit']) ?$this->request->data['limit']:25;
		$page = $this->request->page ?: 1;
		$order = array('created' => 'desc');
		
		$total = SandboxPresentation::count(compact('conditions'));
		$documents = SandboxPresentation::all(compact('conditions','order','limit','page'));
		
		$page_number = (int)$page;
		$total_pages = ((int)$limit > 0) ? ceil($total / $limit):0;
		
		// Pagination info
		$response['page'] = $page;
		$response['total'] = $total;
		$response['limit'] = $limit;
		$response['total_pages'] = $total_pages;
		
		// The links
		if($total > 0) {
			$response['success'] = true;
			
			foreach($documents as $document) {
				$response['result'][] = array('title' => $document->title, 'description' => $document->description, 'link' => $document->link);
			}
		}
		
		// Stats
		$response['created'] = time();
		$response['took'] = microtime(true) - $start;
		
		return json_encode($response);
	}
	
}
?>