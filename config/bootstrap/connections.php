<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \lithium\data\Connections;

Connections::add('default', array(
	'type' => 'http',
	'adapter' => 'CouchDb',
	'host' => 'localhost',
	'database' => 'lithosphere',
	'version' => '1.0'
));

Connections::add('test', array(
	'type' => 'http',
	'adapter' => 'CouchDb',
	'host' => 'localhost',
	'database' => 'lithosphere_test',
	'version' => '1.0'
));

?>