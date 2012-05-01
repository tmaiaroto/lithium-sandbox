<?php

namespace app\extensions\command;

use app\models\User;

use lithium\util\Set;
use lithium\util\Inflector;
use lithium\data\Connections;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use DirectoryIterator;

use MongoDate;
use MongoId;

class Users extends \lithium\console\Command {

	
	/**
	 * Generates the appropriates indexes for the words collection.
	 *
	 * @return void
	*/
	public static function indexes() {
		$meta = User::meta();
		$database = Connections::get($meta['connection']);
		
		// Add arrays with fields to this array... example: array('firstName'), array('lastName')
		$indexes = array(
		);

		echo "Ensuring indexes..." . PHP_EOL;
		foreach ($indexes as $index) {
			$database->connection->{$meta['source']}->ensureIndex($index);
		}
		echo "Indexes built!" . PHP_EOL;
	}
}
?>