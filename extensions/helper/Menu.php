<?php
/**
 * Menu Helper
 * 
*/
namespace app\extensions\helper;

use app\models\Menu as MenuModel;
use lithium\template\View;
use lithium\util\Inflector;
use lithium\storage\Cache;
use lithium\net\http\Router;

class Menu extends \lithium\template\Helper {
  	
	/**
	 * Renders a static menu that gets built using Lithium's filter system.
	 * For now, this only goes two deep. In the future it should allow for more levels...Though there should be a limit.
	 *
	 * @param string $name The menu name
	 * @param array $options 
	 * @return string HTML code for the menu
	*/
	public function static_menu($name=null, $options=array()) {
		$defaults = array(
            //'cache' => '+1 day'
			'cache' => false,
			'menu_id' => '',
			'menu_class' => '',
        );
		$options += $defaults;
		
		if(empty($name) || !is_string($name)) {
			return '';
		}
		
		// Get the current URL (false excludes the domain)
		$here = $this->_context->html->here(false);
		
		// set the cache key for the menu
		$cache_key = (empty($name)) ? 'static_menus.all':'static_menus.' . $name;
		$menu = false;
		
		// if told to use the menu from cache (note: filters will not be applied for this call because Menu::static_menu() should not be called provided there's a copy in cache)
        if(!empty($options['cache'])) {
            $menu = Cache::read('default', $cache_key);
        }
		
		// if the menu hasn't been set in cache or it was empty for some reason, get a fresh copy of its data
		if(empty($menu)) {
			$menu = MenuModel::static_menu($name);	
		}
		
		// if using cache, write the menu data to the cache key
		if(!empty($options['cache'])) {
			Cache::write('default', $cache_key, $menu, $options['cache']);
		}
		
		// Format the HTML for the menu
		// option for additional custom menu class
		$menu_class = ' ' . $options['menu_class'];
		
		$string = "\n";
		$string .= '<ul class="menu ' . $name . '_menu' . $menu_class . '" id="' . $options['menu_id'] . '">';
		$string .= "\n";
		
		if(is_array($menu)) {
			$i = 1;
			$total = count($menu);
			foreach($menu as $parent) {
				$title = (isset($parent['title']) && !empty($parent['title'])) ? $parent['title']:false;
				$url = (isset($parent['url']) && !empty($parent['url'])) ? $parent['url']:false;
				$options = (isset($parent['options']) && is_array($parent['options'])) ? $parent['options']:array();
				$sub_items = (isset($parent['sub_items']) && is_array($parent['sub_items'])) ? $parent['sub_items']:array();
				if($title && $url) {
					$position_class = ($i == 1) ? ' menu_first':'';
					$position_class = ($i == $total) ? ' menu_last':$position_class;
					$string .= "\t";
					$matched_route = Router::match($url);
					// /dashboard is the customer_ prefixed actions and /admin is of course admin_ prefix actions
					$current_class = ($matched_route == $here || (strstr($here, '/admin' . $matched_route)) || (strstr($here, '/dashboard' . $matched_route))) ? ' current':'';
					$string .= '<li class="menu_item' . $position_class . $current_class . '">' . $this->_context->html->link($title, $url, $options);
					// sub menu items
					if(count($sub_items) > 0) {
						$string .= "\n\t";
						$string .= '<ul class="sub_menu">';
						$string .= "\n";
						foreach($sub_items as $child) {
							$title = (isset($child['title']) && !empty($child['title'])) ? $child['title']:false;
							$url = (isset($child['url']) && !empty($child['url'])) ? $child['url']:false;
							$options = (isset($child['options']) && is_array($child['options'])) ? $child['options']:array();
							if($title && $url) {
								$string .= "\t\t";
								$string .= '<li class="sub_menu_item">' . $this->_context->html->link($title, $url, $options) . '</li>';
								$string .= "\n";
							}
						}
						$string .= "\t";
						$string .= '</ul>';
						$string .= "\n";
					}
					$string .= '</li>';
					$string .= "\n";
				}
				$i++;
			}
		}
		
		$string .= '</ul>';
		$string .= "\n";
		
		return $string;
	}
    
}
?>