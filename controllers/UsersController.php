<?php
namespace app\controllers;

use app\models\User;
use app\extensions\util\Util;
use li3_flash_message\extensions\storage\FlashMessage;
use li3_access\security\Access;
use lithium\security\validation\RequestToken;
use lithium\security\Auth;
use lithium\storage\Session;
use lithium\security\Password;
use lithium\util\Set;
use lithium\util\String;
use lithium\util\Inflector;
use MongoDate;
use MongoId;

class UsersController extends \lithium\action\Controller {

	public function admin_index() {
		$this->_render['layout'] = 'admin';
		
		$conditions = array();
		// If a search query was provided, search all "searchable" fields (any field in the model's $search_schema property)
		// NOTE: the values within this array for "search" include things like "weight" etc. and are not yet fully implemented...But will become more robust and useful.
		// Possible integration with Solr/Lucene, etc.
		if((isset($this->request->query['q'])) && (!empty($this->request->query['q']))) {
			$search_schema = User::searchSchema();
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
		$total = User::count(compact('conditions'));
		$documents = User::all(compact('conditions','order','limit','page'));
		
		$page_number = (int)$page;
		$total_pages = ((int)$limit > 0) ? ceil($total / $limit):0;
		
		// Set data for the view template
		return compact('documents', 'total', 'page', 'limit', 'total_pages');
	}
	
	/**
	 * Allows admins to update users.
	 * 
	 * @param string $id The user id
	*/
	public function admin_update($id=null) {
		$this->_render['layout'] = 'admin';
		
		// Get the fields so the view template can iterate through them and build the form
		$fields = User::schema();
		
		// Special rules for user creation (includes unique e-mail)
		$rules = array(
			'email' => array(
				array('notEmpty', 'message' => 'E-mail cannot be empty.'),
				array('email', 'message' => 'E-mail is not valid.'),
				array('uniqueEmail', 'message' => 'Sorry, this e-mail address is already registered.'),
			)
		);
		
		$roles = User::userRoles();
		
		// Get the document from the db to edit
		$conditions = array('_id' => $id);
		$document = User::find('first', array('conditions' => $conditions));
		// Redirect if invalid user
		if(empty($document)) {
			FlashMessage::write('That user was not found.', array(), 'default');
			return $this->redirect('/admin');
		}
		
		// If data was passed, set some more data and save
		if ($this->request->data) {
			// CSRF
			if(!RequestToken::check($this->request)) {
				RequestToken::get(array('regenerate' => true));
			} else {
				$now = new MongoDate();
				$this->request->data['modified'] = $now;
				
				// Hard code these to prevent any possible change
				$this->request->data['role'] = $document->role;
				$this->request->data['owner_id'] = $document->parent_id;
				
				// Add validation rules for the password IF the password and password_confirm field were passed
				if((isset($this->request->data['password']) && isset($this->request->data['password_confirm'])) &&
				   (!empty($this->request->data['password']) && !empty($this->request->data['password_confirm']))) {
					$rules['password'] = array(
						array('notEmpty', 'message' => 'Password cannot be empty.'),
						array('notEmptyHash', 'message' => 'Password cannot be empty.'),
						array('moreThanFive', 'message' => 'Password must be at least 6 characters long.')
					);
					
					// ...and of course hash the password
					$this->request->data['password'] = Password::hash($this->request->data['password']);
				} else {
					// Otherwise, set the password to the current password.
					$this->request->data['password'] = $document->password;
				}
				
				// Ensure the unique e-mail validation rule doesn't get in the way when editing users
				// So if the user being edited has the same e-mail address as the POST data...
				// Change the e-mail validation rules
				if(isset($this->request->data['email']) && $this->request->data['email'] == $document->email) {
					$rules['email'] = array(
						array('notEmpty', 'message' => 'E-mail cannot be empty.'),
						array('email', 'message' => 'E-mail is not valid.')
					);
				}
				
				// Save
				if($document->save($this->request->data, array('validate' => $rules))) {
					FlashMessage::write('The user has been updated successfully.', array(), 'default');
					$this->redirect(array('controller' => 'users', 'admin' => true, 'action' => 'index'));
				} else {
					$this->request->data['password'] = '';
					FlashMessage::write('The user could not be updated, please try again.', array(), 'default');
				}
			}
		}
		
		$this->set(compact('document', 'fields', 'roles'));
	}
	
	/**
	 * Registers a user.
	*/
	public function register() {
		
		// Special rules for registration
		$rules = array(
			'email' => array(
				array('notEmpty', 'message' => 'E-mail cannot be empty.'),
				array('email', 'message' => 'E-mail is not valid.'),
				array('uniqueEmail', 'message' => 'Sorry, this e-mail address is already registered.'),
			),
			'password' => array(
				array('notEmpty', 'message' => 'Password cannot be empty.'),
				array('notEmptyHash', 'message' => 'Password cannot be empty.'),
				array('moreThanFive', 'message' => 'Password must be at least 6 characters long.')
			)
		);
		
		// Save
		if ($this->request->data) {
			// CSRF
			if(!RequestToken::check($this->request)) {
				RequestToken::get(array('regenerate' => true));
			} else {
				$document = User::create();
				
				$now = new MongoDate();
				$this->request->data['created'] = $now;
				$this->request->data['modified'] = $now;
				
				$this->request->data['active'] = true;
				
				// Generate the URL
				$url = '';
				$url_field = User::urlField();
				$url_separator = User::urlSeparator();
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
				
				// Last check for the URL...if it's empty for some reason set it to "user"
				if(empty($url)) {
					$url = 'user';
				}
				
				// Then get a unique URL from the desired URL (numbers will be appended if URL is duplicate) this also ensures the URLs are lowercase
				$this->request->data['url'] = Util::uniqueUrl(array(
					'url' => $url,
					'model' => 'app\models\User'
				));
				
				// Set the user's role...always hard coded and set.
				$this->request->data['role'] = 'registered_user';
				
				// However, IF this is the first user ever created, then they will be an administrator.
				$users = User::find('count');
				if(empty($users)) {
					$this->request->data['active'] = true;
					$this->request->data['role'] = 'administrator';
				}
				
				// Set the password, it has to be hashed
				if((isset($this->request->data['password'])) && (!empty($this->request->data['password']))) {
					$this->request->data['password'] = Password::hash($this->request->data['password']);
				}

				if($document->save($this->request->data, array('validate' => $rules))) {
					FlashMessage::write('User registration successful.', array(), 'default');
					$this->redirect('/');
				} else {
					$this->request->data['password'] = '';
				}
			}
		}
		
		if(empty($document)) {
			// Create an empty user object
			$document = User::create();
		}
		
		$this->set(compact('document'));
	}
	
	/*
	 * Also make the login method available to admin routing.
	 * It can have a different template and layout if need be.
	 * I'm not sure it will need one yet...
	*/
	public function admin_login() {
		// $this->_render['layout'] = 'admin';
		$this->_render['template'] = 'login';
		return $this->login();
	}
	
	/**
	 * Provides a login page for users to login.
	 * 
	 * @return type 
	*/
	public function login() {
		$user = Auth::check('user', $this->request);
		// 'triedAuthRedirect' so we don't end up in a redirect loop
		if (!Session::check('triedAuthRedirect', array('name' => 'cookie'))) {
			Session::write('triedAuthRedirect', 'false', array('name' => 'cookie', 'expires' => '+1 hour'));
		}
		
		// Facebook returns a session querystring... We don't want to show this to the user.
		// Just redirect back so it ditches the querystring. If the user is logged in, then
		// it will redirect like expected using the $url variable that has been set below.
		// Not sure why we need to do this, I'd figured $user would be set...And I think there's
		// a session just fine if there was no redirect and the user navigated away...
		// But for some reason it doesn't see $user and get to the redirect() part...
		if(isset($_GET['session'])) {
			$this->redirect(array('controller' => 'users', 'action' => 'login'));
		}
		
		if ($user) {
			// Users will be redirected after logging in, but where to?
			$url = '/';
			
			// Default redirects for certain user roles
			switch($user['role']) {
				case 'administrator':
				case 'content_editor':
					$url = '/admin';
					break;
				default:
					$url = '/';
					break;
			}
			
			// Second, look to see if a cookie was set. The could have ended up at the login page
			// because he/she tried to go to a restricted area. That URL was noted in a cookie.
			if (Session::check('beforeAuthURL', array('name' => 'cookie'))) {
				$url = Session::read('beforeAuthURL', array('name' => 'cookie'));
				
				// 'triedAuthRedirect' so we don't end up in a redirect loop
				$triedAuthRedirect = Session::read('triedAuthRedirect', array('name' => 'cookie'));
				if($triedAuthRedirect == 'true') {
					$url = '/';
					Session::delete('triedAuthRedirect', array('name' => 'cookie'));
				} else {
					Session::write('triedAuthRedirect', 'true', array('name' => 'cookie', 'expires' => '+1 hour'));
				}
				
				Session::delete('beforeAuthURL', array('name' => 'cookie'));
			}
			
			// Save last login IP and time
			$user_document = User::find('first', array('conditions' => array('_id' => $user['_id'])));
			
			if($user_document) {
				$user_document->save(array('last_login_ip' => $_SERVER['REMOTE_ADDR'], 'last_login_time' => new MongoDate()));
			}
			
			// only set a flash message if this is a login. it could be a redirect from somewhere else that has restricted access
			// $flash_message = FlashMessage::read('default');
			// if(!isset($flash_message['message']) || empty($flash_message['message'])) {
				FlashMessage::write('You\'ve successfully logged in.', array(), 'default');
			// }
			$this->redirect($url);
		} else {
			if($this->request->data) {
				FlashMessage::write('You entered an incorrect username and/or password.', array(), 'default');
			}
		}
		$data = $this->request->data;
		
		return compact('data');
	}
	
	/**
	 * Also make the login available to admin routing.
	*/
	public function admin_logout() {
		return $this->logout();
	}

	/**
	 * Logs a user out.
	*/
	public function logout() {
		Auth::clear('user');
		FlashMessage::write('You\'ve successfully logged out.', array(), 'default');
		$this->redirect('/');
	}
	
	/**
	 * Change a user password.
	 * This is a method that you request via AJAX.
	 *
	*/
	public function update_password($url=null) {
		// First, get the record
		$record = User::find('first', array('conditions' => array('url' => $url)));
		if(!$record) {
			return array('error' => true, 'response' => 'User record not found.');
		}
		
		$user = Auth::check('user');
		if(!$user) {
			//$this->redirect('/');
			return array('error' => true, 'response' => 'You must be logged in to change your password.');
			//exit();
		}
		
		$record_data = $record->data();
		if($user['_id'] != $record_data['_id']) {
			//$this->redirect('/');
			return array('error' => true, 'response' => 'You can only change your own password.');
			//exit();
		}
		
		// Update the record
		if ($this->request->data) {
			// Make sure the password matches the confirmation
			if($this->request->data['password'] != $this->request->data['password_confirm']) {
			return array('error' => true, 'response' => 'You must confirm your password change by typing it again in the confirm box.');
			}
			
			// Call save from the main app's User model
			if($record->save($this->request->data)) {
			//$this->redirect(array('controller' => 'users', 'action' => 'manage', $url));
			return array('error' => false, 'response' => 'Password has been updated successfully.');
			} else {
			return array('error' => true, 'response' => 'Failed to update password, try again.');
			}
		} else {
			return array('error' => true, 'response' => 'You must pass the proper data to change your password and you can\'t call this URL directly.');
		}
    }
	
	/**
	 * Enables/disables the user.
	 * This method should be called via AJAX.
	 * 
	 * @param string $id The user's MongoId
	 * @param mixed $active What to set the active field to. 1 = true and 0 = false, 'false' = false too
	 * @return boolean Success
	*/
	public function admin_set_status($id=null, $active=true) {
		$this->_render['layout'] = 'admin';
		
		// Do our best here
		if($active == 'false') {
			$active = false;
		} else {
			$active = (bool) $active;
		}
		
		// Only allow this method to be called via JSON
		if(!$this->request->is('json')) {
			return array('success' => false);
		}
		
		$requested_user = User::find('first', array('conditions' => array('_id' => $id)));
		
		$current_user = Auth::check('user');
		
		// Don't allow a user to make themself active or inactive.
		if((string)$request_user->_id == $current_user['_id']) {
			return array('success' => false);
		}
		
		if(User::update(
			// query
			array(
				'$set' => array(
					'active' => $active
				)
			), 
			// conditions
			array(
				'_id' => $requested_user->_id
			), 
			array('atomic' => false)
		)) {
			return array('success' => true);
		}
		
		// Otherwise, return false. Who knows why, but don't do anything.
		return array('success' => false);
	}
	
}
?>