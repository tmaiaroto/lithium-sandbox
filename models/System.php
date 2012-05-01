<?php
/**
 * A System model to work with MongoDB from a higher level.
 * This model will executes various commands as well as store new commands.
 * The system.js collection is used.
 * 
 * Warning: There be dragons in here.
 * 
*/
namespace app\models;

use lithium\data\Connections;
use lithium\core\Environment;
use MongoCode;

class System extends \lithium\data\Model {
	
	// Use the system.js in MongoDB
	protected $_meta = array(
		'source' => 'system.js', 
		'locked' => true
	);
	
	// We only should be saving _id and value fields
	protected $_schema = array(
		'_id' => array('type' => 'string'),
		'value' => array('type' => 'string')
	);
	
	/**
	 * Generate MongoCode from an external JavaScript file.
	 * These files are saved in the library or app root path under 
	 * a `mongo_code` directory (should sit next to models, controllers, etc.).
	 *
	 * @param string $file
	 * @return MongoCode 
	*/
	public static function getMongoCode($file=null) {
		$command_path = dirname(__DIR__) . '/mongo_code/';
		
		// ensure the file has .js on the end, all files should be saved as .js
		// but you can also pass just the base name of the file to this method
		if(substr($file, -3) != '.js') {
			$file = $file . '.js';
		}
		
		$full_path = $command_path . $file;
		
		if(file_exists($full_path)) {
			$handle = fopen($full_path, "rb");
			$contents = fread($handle, filesize($full_path));
			fclose($handle);
			
			// remove new lines (they can mess up the code in some cases)
			$contents = str_replace(array("\r\n", "\n", "\r"), '', $contents);
			// tabs with single spaces
			$contents = str_replace("\t", ' ', $contents);
			
			return new MongoCode($contents);
		} else {
			return false;
		}
	}
	
	/**
	 * Save a command in MongoDB's stored JavaScript.
	 * This will allow the use of the function later in Mongo queries and operations.
	 * 
	 * WARNING: It would not be advisable to allow front-end visitors to submit code
	 * to be stored in MongoDB for possible injection risks.
	 * 
	 * @param string $name The name of the command that will be stored in MongoDB
	 * @param array $options Several options
	 *		- file: The file name for the JS, stored at: {library or app}/mongo_code/...
	 *		- code: Or, actual JS code
	 * @return boolean
	*/
	public static function saveCommand($name=null, $options = array()) {
		$defaults = array(
			'file' => null,
			'code' => null
		);
		$options += $defaults;
		
		$code = null;
		
		if(!empty($name) && is_string($name)) {
			$db = self::connection();
			
			// Determine if code was passed or a file reference
			if(!empty($options['file'])) {
				$code = static::getMongoCode($options['file']);
			}
			// Any passed `code` is going to take prescedence
			if(!empty($options['code'])) {
				$code = (is_string($options['code'])) ? new MongoCode($options['code']):$options['code'];
			}
			
			// one more check here...
			if($code instanceof MongoCode) {
				$command = static::create();
				$command->set(array('_id' => $name, 'value' => $code));
				return $command->save();
			}
		} 
		
		return false;
	}
	
	/**
	 * Removes user stored JavaScript from MongoDB.
	 * 
	 * @param string $name The name of the stored JavaScript method
	 * @return boolean Only if the command was executed properly 
	 *		(note: MongoDB returns 'ok' => 1 even if there was no document found to remove)
	*/
	public static function removeCommand($name=null) {
		if(!empty($name)) {
			$db = self::connection();
			$result = $db->connection->execute('return db.system.js.remove({ _id: "' . $name . '" });');
			return ($result['ok'] == 1) ? true:false;
		}
		return false;
	}
	
	/**
	 * Runs an eval() in MongoDB for a previousy stored method.
	 * 
	 * @param string $name The stored JavaScript method name
	 * @param array $args Any arguments for the method
	 * @return mixed The result from MongoDB or false if not enough info was provided
	*/
	public static function runCommand($name=null, $args=array(), $options=array()) {
		$defaults = array('nolock' => true);
		$options += $defaults;
		if(!empty($name) && is_string($name)) {
			$db = self::connection();
			return $db->connection->command(array(
				'$eval' => 'return '. $name .'(' . join(',', $args) . ');',
				'nolock' => $options['nolock']
			));
		}
		return false;
	}
	
	
	/**
	 * Executes a piece of MongoCode (can also be a string).
	 * 
	 * @param mixed $code Either a string or MongoCode
	 * @param array $args The args for the function that's being executed
	 * @return mixed The result or false if no code was passed
	 */
	public static function runCode($code=null, $args=array()) {
		if(empty($code)) {
			return false;
		}
		$db = self::connection();
		return $db->connection->execute($code, $args);
	}
	
	/**
	 * Runs a mongoimport command.
	 * Also keep in mind that with a function like this, you could build a command to import
	 * from the Twitter firehose...Or use this command to import any starter data for your app
	 * from a JSON file (or any other type Mongo can import from).
	 * 
	 * See http://www.mongodb.org/display/DOCS/Import+Export+Tools#ImportExportTools-DataImportandExport
	 * For a list of various flags that can be passed. There are only a few used here.
	 * If any additional flags are desired, simply add them to $options['flags'] array.
	 * Even if it's more of an argument than a flag, it will work (it'll take spaces). Just don't 
	 * put the -- in there or anything, those get added automatically. By default, 'upsert' is
	 * flagged, so objects that already exist will be updated. You would need to pass an empty
	 * 'flags' => array() or any other set of flags to change that. I find more cases where I want
	 * upsert flagged on than off, so it's on by default.
	 * 
	 * ex.
	 * 'flags' => array(
	 *     'upsert',
	 *     'dbpath /db/path',
	 *     'drop'
	 * )
	 * 
	 * Another important note: The 'connection' key in options. It's 'default' by default, but
	 * you will want to ensure that you are providing a connection config name that is indeed a
	 * MongoDb connection. However, if you pass 'host' and 'database' in the $options array, then
	 * the connection will be ignored.
	 * 
	 * @param string $source The source file or URL
	 * @param string $collection The collection to import to
	 * @param array $options Various options including the host, db, collection, etc.
	 * @return boolean
	*/
	public static function mongoImport($source=null, $collection=null, $options=array()) {
		if(empty($source) || empty($collection)) {
			return false;
		}
		
		// set some defaults
		$options += array(
			// name of the connection that you created with Connections::add() in the bootstrap
			'connection' => 'default',
			'environment' => Environment::get(),
			'type' => 'json',
			// any other flags
			'flags' => array(
				'upsert'
			),
			'remove_file_on_success' => true
		);
		
		$flags = (!empty($options['flags'])) ? ' --' . join(' --', $options['flags']):null;
		$host = (isset($options['host'])) ? $options['host']:null;
		$database = (isset($options['database'])) ? $options['database']:null;
		$connection = Connections::get($options['connection'], array('config' => true));
		// in case connection wasn't found... invalid config name or no config...
		if(!empty($connection)) {
			$host = (empty($host)) ? $connection['host']:$host;
			$database = (empty($database)) ? $connection['database']:$database;
			// Don't try to run this for a connection that is not to a MongoDB server
			// unless we were passed specific 'host' and 'database' options.
			// There could be a case where the 'default' connection is another database...
			// But there is another MongoDB running elsewhere not configured or something.
			if($connection['type'] != 'MongoDb' && empty($host) && empty($database)) {
				return false;
			}
		}
		
		if(empty($host) || empty($database)) {
			return false;
		}
		
		// Username is a weird one... the MongoDb adapter configs with a "login" key
		// but MongoDb's import command line uses '-u' or '--username' so we want to also
		// accept a 'username' key from our $options array, but then also a 'login' key since
		// that's what Lithium uses. So break it down...
		// First, take a login key from options.
		$user = (isset($options['login'])) ? $options['login']:null;
		// Second, if that wasn't set, take a username key from options.
		$user = (isset($options['username']) && empty($user)) ? $options['username']:$user;
		// Last, if the username wasn't passed in the options, take it from the connection if set.
		$user = (isset($connection['login']) && empty($user)) ? $connection['login']:$user;
		// Otherwise, it will be null which is fine.
		$user = (!empty($user)) ? ' --username ' . $user:null;
		
		// Password is 'password' everywhere.
		$password = (isset($options['password'])) ? $options['password']:null;
		$password = (isset($connection['password']) && empty($password)) ? $connection['password']:$password;
		$password = (!empty($password)) ? ' --password ' . $password:null;
		
		// It doesn't really matter much, we may want to remove --upsert if performance is better.
		// Changing data isn't a huge concern in this situation.
		$command = 'mongoimport --host ' . $host . ' --db ' . $database . $user . $password . ' --collection ' . $collection . ' --type ' . $options['type'] . ' --file ' . $source . ' --upsert';
		
		exec($command, $result);
		if(isset($result[1]) && stristr($result[1], 'imported')) {
			if($options['remove_file_on_success'] === true && file_exists($source)) {
				@unlink($source);
			}
			return true;
		}
		return false;
	}
}
?>