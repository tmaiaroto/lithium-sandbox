<?php
/**
 * This will check access on all requests.
 * 
*/
use lithium\action\Dispatcher;
use lithium\net\http\Router;
use lithium\action\Response;
use lithium\security\Auth;
use lithium\core\Libraries;

use li3_access\security\Access;
use li3_flash_message\extensions\storage\FlashMessage;

// ...First, let's put a filter on the dispatcher to protect all admin actions
Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
    $request = $params['request'];
	$action = $request->action;
	$user = Auth::check('user');
	
	// Protect all admin methods except for login and logout.
	if($request->admin === true && $action != 'login' && $action != 'logout') {
		$action_access = Access::check('default', $user, $request, array('rules' => array('allowManagers')));
		if(!empty($action_access)) {
			FlashMessage::write($action_access['message'], array(), 'default');
			header('Location: ' . Router::match($action_access['redirect']));
			// None shall pass.
			exit();
		}
	}
	
	return $chain->next($self, $params, $chain);
});

Access::config(array(
	'default' => array(
			'adapter' => 'Rules',
			// optional filters applied for each configuration
			'filters' => array(
				/*function($self, $params, $chain) {
					// Any config can have filters that get applied
					exit();
					return $chain->next($self, $params, $chain);
				}*/
			)
	)
));

// Set some basic rules to be used from anywhere

// Allow access for users with a role of "administrator" or "content_editor"
Access::adapter('default')->add('allowManagers', function($user, $request, $options) {
	if(($user) && ($user['role'] == 'administrator' || $user['role'] == 'content_editor')) {
		return true;
	}
	return false;
});

// Restrict access to documents that have a published field marked as true 
// (except for users with a role of "administrator" or "content_editor")
Access::adapter('default')->add('allowIfPublished', function($user, $request, $options) {
	if(isset($options['document']['published']) && $options['document']['published'] === true) {
		return true;
	}
	if(($user) && ($user['role'] == 'administrator' || $user['role'] == 'content_editor')) {
		return true;
	}
	return false;
});
?>