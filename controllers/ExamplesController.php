<?php
/**
 * Example controller.
 *
*/

namespace app\controllers;

use app\models\User;
use app\models\Example;
use app\models\System;
use app\extensions\util\Util;
use li3_access\security\Access;
use li3_flash_message\extensions\storage\FlashMessage;
use lithium\storage\Session;
use lithium\security\Auth;
use lithium\util\Set;
use lithium\util\Inflector;
use lithium\security\validation\RequestToken;
use lithium\data\entity\Document;
use MongoId;
use MongoDate;

class ExamplesController extends \lithium\action\Controller {
	
	/**
	 * Displays information about the content.
	 * 
	 * @param string $url The content URL
	*/
	public function admin_read($url=null) {
		$this->_render['layout'] = 'admin';
		
		// Get the document from the db to edit
		$conditions = array('url' => $url);
		$document = Example::find('first', array('conditions' => $conditions));
		$display_name = $document->name . ' Overview';
		
		// Set data for the view template
		$this->set(compact('document', 'display_name'));
	}
	
	/**
	 * Lists all the items stored.
	*/
	public function admin_index() {
		$this->_render['layout'] = 'admin';
		
		$conditions = array();
		// If a search query was provided, search all "searchable" fields (any field in the model's $search_schema property)
		// NOTE: the values within this array for "search" include things like "weight" etc. and are not yet fully implemented...But will become more robust and useful.
		// Possible integration with Solr/Lucene, etc.
		if((isset($this->request->query['q'])) && (!empty($this->request->query['q']))) {
			$search_schema = Example::searchSchema();
			$search_conditions = array();
			// For each searchable field, adjust the conditions to include a regex
			foreach($search_schema as $k => $v) {
				// TODO: possibly factor in the weighting later. also maybe note the "type" to ensure our regex is going to work or if it has to be adjusted (string data types, etc.)
				// var_dump($k);
				// The search schema could be provided as an array of fields without a weight
				// In this case, the key value will be the field name. Otherwise, the weight value
				// might be specified and the key would be the name of the field.
				$field = (is_string($k)) ? $k:$v;
				$search_regex = new \MongoRegex('/' . $this->request->query['q'] . '/i');
				$conditions['$or'][] = array($field => $search_regex);
			}
		}
		
		$limit = 25;
		$page = $this->request->page ?: 1;
		$order = array('created' => 'desc');
		$total = Example::count(compact('conditions'));
		$documents = Example::all(compact('conditions','order','limit','page'));
		
		$page_number = (int)$page;
		$total_pages = ((int)$limit > 0) ? ceil($total / $limit):0;
		
		$display_name = 'Examples';
		
		// Set data for the view template
		return compact('documents', 'total', 'page', 'limit', 'display_name', 'total_pages');
	}
	
	/**
	 * Generic create() action.
	*/
	public function admin_create() {
		$this->_render['layout'] = 'admin';
		
		// Get the fields so the view template can iterate through them and build the form
		$fields = Example::schema();
		
		// If data was passed, set some more data and save
		if ($this->request->data) {
			$document = Example::create();
			$now = new MongoDate();
			$this->request->data['created'] = $now;
			$this->request->data['modified'] = $now;
			
			// Ensure the tags don't save empty string value
			$this->request->data['tags'] = array_filter($this->request->data['tags']);
			
			// Set the pretty URL
			$this->generateUrl();
			
			// Set the owner
			$user = Auth::check('user');
			if($user) {
				$this->request->data['owner_id'] = new MongoId($user['_id']);
			} else {
				FlashMessage::write('You must be logged in to do this.', array(), 'default');
				return $this->redirect(array('controller' => 'users', 'action' => 'login'));
			}
			
			// Save
			if($document->save($this->request->data)) {
				FlashMessage::write('The content has been created successfully.', array(), 'default');
				$this->redirect(array('controller' => 'examples', 'action' => 'index'));
			} else {
				FlashMessage::write('The content could not be saved, please try again.', array(), 'default');
			}
		}
		
		if(empty($document)) {
			$document = Example::create();
		}
		
		$this->set(compact('document', 'fields'));
	}
	
	/**
	 * Generic update() action.
	*/
	public function admin_update($url=null) {
		$this->_render['layout'] = 'admin';
		
		// Get the document from the db to edit
		$conditions = array('url' => $url);
		$document = Example::find('first', array('conditions' => $conditions));
		
		$current_meta = array(
			'tags' => $document->tags
		);
		
		// Get the fields so the view template can iterate through them and build the form
		$fields = Example::schema();
		
		// If data was passed, set some more data and save
		if ($this->request->data) {
			$now = new MongoDate();
			$this->request->data['modified'] = $now;
			
			// there's bound to be empty values in here, clean them up
			if(isset($this->request->data['tags'])) {
				$this->request->data['tags'] = array_filter($this->request->data['tags']);
			}
			
			// see if the meta changed...
			$tags_diff = 0;
			if(is_array($current_meta['tags'])) {
				$tags_diff = array_diff($current_meta['tags'], $this->request->data['tags']);
			}
			if(!empty($tags_diff)) {
				$this->request->data['meta_changed'] = true;
			}
			
			// Set the pretty URL
			$this->generateUrl($document->_id);
			
			// Save
			if($document->save($this->request->data)) {
				FlashMessage::write('The content has been updated successfully.', array(), 'default');
				$this->redirect(array('controller' => 'examples', 'admin' => true, 'action' => 'index'));
			} else {
				FlashMessage::write('The content could not be updated, please try again.', array(), 'default');
			}
		}
		
		$this->set(compact('document', 'fields'));
	}
	
	/**
	 * Generic delete() action.
	*/
	public function admin_delete($url=null) {
		if(empty($url)) {
			return $this->redirect(array('controller' => 'examples', 'action' => 'index', 'admin' => true));
		}
		
		// Get the document to delete
		$conditions = array('url' => $url);
		$document = Example::find('first', array('conditions' => $conditions));
		
		// TODO: check document access
		
		if($document->delete()) {
			FlashMessage::write('The content has been deleted.', array(), 'default');
		} else {
			FlashMessage::write('The content could not be deleted, please try again.', array(), 'default');
		}
		$this->redirect(array('controller' => 'examples', 'admin' => true, 'action' => 'index'));
	}
	
	/**
	 * Generates the pretty URL for the content with the help of some model properties
	 * and a utility class that does the heavy lifting. This method is merely deciding
	 * which field to use and ensuring that there's a value to send to the utility that
	 * generates the unique URL.
	 *
	*/
	public function generateUrl($id=null) {
		$url = '';
		$url_field = Example::urlField();
		$url_separator = Example::urlSeparator();
		
		// the 'url' key will be set if a manual URL has been provided (or on an edit when the user does not clear out the field) so URLs do not change if the title changes once created. After initial creation, they must be manually altered or the field must be emptied out in the form.
		if(isset($this->request->data['url']) && !empty($this->request->data['url'])) {
			$url = $this->request->data['url'];
		} else {
			if($url_field != '_id' && !empty($url_field)) {
				if(is_array($url_field)) {
					foreach($url_field as $field) {
						if(isset($this->request->data[$field]) && $field != '_id') {
							$url .= $this->request->data[$field] . ' ';
						}
					}
					$url = Inflector::slug(trim($url), $url_separator);
				} else {
					$url = Inflector::slug($this->request->data[$url_field], $url_separator);
				}
			}
		}
		
		// Last check for the URL...if it's empty for some reason set it to a generic "document" which will ultimately get a number appended to the end to avoid dupes
		if(empty($url)) {
			$url = 'document';
		}
		
		// Then get a unique URL from the desired URL (numbers will be appended if URL is duplicate) this also ensures the URLs are lowercase
		$this->request->data['url'] = Util::uniqueUrl(array(
			'url' => $url,
			'model' => 'app\models\Example',
			'id' => $id
		));
	}
	
}
?>