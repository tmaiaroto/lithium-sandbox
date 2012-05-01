<?php
use app\models\Menu;

// Apply filters to Menu::static_menu() in order to alter and create menus
Menu::applyFilter('static_menu',  function($self, $params, $chain) {
	if($params['name'] == 'admin') {
		$self::$static_menus['admin']['m8_app'] = array(
			'title' => 'Example',
			'url' => array('admin' => true, 'controller' => 'example', 'action' => 'index'),
			'sub_items' => array(
				array(
					'title' => 'List All',
					'url' => array('admin' => true, 'controller' => 'example', 'action' => 'index')
				)
			)
		);
	}
	
	return $chain->next($self, $params, $chain);
});
?>