<?php
namespace app\models;

class BaseModel extends \lithium\data\Model {

	protected $_meta = array(
		'locked' => true
	);
	
	public $url_field;
	
	public $url_separator = '-';
	
	public $search_schema = array();
	
	public static function __init() {
		 $class =  __CLASS__;
		 
		 // Note: If any of the following properties are not set in any extended model, 
		 // they will be picked up from this base model.
		 $class::_object()->search_schema = static::_object()->search_schema += $class::_object()->search_schema;
		 $class::_object()->url_field = static::_object()->url_field;
		 $class::_object()->url_separator = static::_object()->url_separator;
		 
		 parent::__init();
	}
	
	/**
	 * Returns the URL field(s) for the current model.
	 * If it's not set, it will return null. 
	 * The controller would need to make a decision about the URL at that point.
	 * This method can also be used to set the URL field(s).
	 *
	 * @param mixed $field An array or string that chooses the field(s) from which to generate a friendly URL
	 * @return mixed Either an array of multiple fields to use for a URL (presumably to be concatenated), a single field as a string, or null for no field
	*/
	public static function urlField($field=null) {
		$class =  __CLASS__;
		if(!empty($field)) {
			$class::_object()->url_field = $field;
		}
		return (isset($class::_object()->url_field) && !empty($class::_object()->url_field)) ? $class::_object()->url_field:null;
	}
	
	/**
	 * Gets or sets the URL separator, which replaces spaces.
	 * Default is always a '-' symbol.
	 *
	 * @param string $separator The separator character to use for spaces
	 * @return String
	*/
	public static function urlSeparator($separator=null) {
		$class =  __CLASS__;
		if(!empty($separator)) {
			$class::_object()->url_separator = $separator;
		}
		return (isset($class::_object()->url_separator) && !empty($class::_object()->url_separator)) ? $class::_object()->url_separator:'-';
	}
	
	/**
	 * Gets or sets the search schema for the model.
	 * 
	 * @param array Optional new search schema values
	 * @return array
	*/
	public static function searchSchema($schema=array()) {
		$class =  __CLASS__;
		if(!empty($schema)) {
			$class::_object()->search_schema = $schema;
		}
		return (isset($class::_object()->search_schema) && !empty($class::_object()->search_schema)) ? $class::_object()->search_schema:array();
	}
	
}
?>