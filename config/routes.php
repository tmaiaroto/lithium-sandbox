<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * The routes file is where you define your URL structure, which is an important part of the
 * [information architecture](http://en.wikipedia.org/wiki/Information_architecture) of your
 * application. Here, you can use _routes_ to match up URL pattern strings to a set of parameters,
 * usually including a controller and action to dispatch matching requests to. For more information,
 * see the `Router` and `Route` classes.
 *
 * @see lithium\net\http\Router
 * @see lithium\net\http\Route
 */
use lithium\net\http\Router;
use lithium\core\Environment;
use lithium\action\Dispatcher;

// Set the evironment
if($_SERVER['HTTP_HOST'] == 'game.dev.local' || $_SERVER['HTTP_HOST'] == 'localhost') {
	Environment::set('development');
}

/**
 * Dispatcher rules to rewrite admin actions.
 */
Dispatcher::config(array(
	'rules' => array(
		'admin' => array('action' => 'admin_{:action}')
	)
));

/**
 * Pass-through routes for admin requests.
 * Both /admin and /admin/page Will take the user to the admin dashboard.
 */
Router::connect("/admin", array('admin' => true, 'controller' => 'pages', 'action' => 'view'), array('continue' => true, 'persist' => array(
	'controller', 'admin'
)));
Router::connect("/admin/{:args}", array('admin' => true), array('continue' => true, 'persist' => array(
	'controller', 'admin'
)));

/**
 * Routes for reporting JSON, CSV, XML, etc.
 */
Router::connect('/{:args}.{:type:json|csv|xml}', array(), array('continue' => true));

/**
 * Connect the static pages.
 */
Router::connect("/", array('controller' => 'pages', 'action' => 'view', 'args' => array('home'), 'persist' => false, 'continue' => false));
Router::connect("/page/{:args}", array('controller' => 'pages', 'action' => 'view', 'args' => array('home'), 'persist' => false, 'continue' => false));

/**
 * Special short routes.
*/
Router::connect("/login", array('controller' => 'users', 'action' => 'login'));
Router::connect("/logout", array('controller' => 'users', 'action' => 'logout'));

/**
 * Add the testing routes. These routes are only connected in non-production environments, and allow
 * browser-based access to the test suite for running unit and integration tests for the Lithium
 * core, as well as your own application and any other loaded plugins or frameworks. Browse to
 * [http://path/to/app/test](/test) to run tests.
 */
if (!Environment::is('production')) {
	Router::connect('/test/{:args}', array('controller' => 'lithium\test\Controller'));
	Router::connect('/test', array('controller' => 'lithium\test\Controller'));
}

/**
 * ### Database object routes
 *
 * The routes below are used primarily for accessing database objects, where `{:id}` corresponds to
 * the primary key of the database object, and can be accessed in the controller as
 * `$this->request->id`.
 *
 * If you're using a relational database, such as MySQL, SQLite or Postgres, where the primary key
 * is an integer, uncomment the routes below to enable URLs like `/posts/edit/1138`,
 * `/posts/view/1138.json`, etc.
 */
// Router::connect('/{:controller}/{:action}/{:id:\d+}.{:type}', array('id' => null));
// Router::connect('/{:controller}/{:action}/{:id:\d+}');

/**
 * If you're using a document-oriented database, such as CouchDB or MongoDB, or another type of
 * database which uses 24-character hexidecimal values as primary keys, uncomment the routes below.
 */
// Router::connect('/{:controller}/{:action}/{:id:[0-9a-f]{24}}.{:type}', array('id' => null));
// Router::connect('/{:controller}/{:action}/{:id:[0-9a-f]{24}}');

/**
 * Routes for pagination
 */
Router::connect("/{:controller}/{:action}/page-{:page:[0-9]+}");
Router::connect("/{:controller}/{:action}/page-{:page:[0-9]+}/{:args}");

/**
 * Finally, connect the default route. This route acts as a catch-all, intercepting requests in the
 * following forms:
 *
 * - `/foo/bar`: Routes to `FooController::bar()` with no parameters passed.
 * - `/foo/bar/param1/param2`: Routes to `FooController::bar('param1, 'param2')`.
 * - `/foo`: Routes to `FooController::index()`, since `'index'` is assumed to be the action if none
 *   is otherwise specified.
 *
 * In almost all cases, custom routes should be added above this one, since route-matching works in
 * a top-down fashion.
 */
Router::connect("/{:controller}/{:action}/{:args}");
?>