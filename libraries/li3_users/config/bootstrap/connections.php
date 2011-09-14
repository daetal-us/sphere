<?php
/**
 * Lithium Sphere: communized sphere of influence
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://www.opensource.org/licenses/MIT The MIT License
 */

use lithium\data\Connections;

Connections::add('li3_users', array(
	'adapter' => 'MongoDb',
	'host' => 'localhost',
	'database' => 'li3_users'
));

?>