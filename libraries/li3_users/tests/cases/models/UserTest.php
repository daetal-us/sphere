<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_users\tests\cases\models;

use \lithium\data\Connections;
use \lithium\data\model\Query;

define('LI3_USERS_TEST', 'li3_users_test');

class User extends \li3_users\models\User {

	protected $_meta = array(
		'key' => '_id',
		'name' => null,
		'title' => null,
		'class' => null,
		'locked' => true,
		'source' => null,
		'connection' => LI3_USERS_TEST,
		'initialized' => false
	);

}

class UserTest extends \lithium\test\Unit {

	public function setUp() {
		Connections::config(array(
			LI3_USERS_TEST => array(
				'type' => 'Http',
				'adapter' => 'CouchDb',
				'version' => '1.0',
				'port' => '5984',
				'database' => LI3_USERS_TEST,
				'params' => array()
			)
		));

		Connections::get(LI3_USERS_TEST)->describe(LI3_USERS_TEST);
	}

	public function tearDown() {
		if (User::find('all')->count()) {
			Connections::get(LI3_USERS_TEST)->delete(
				new Query(
					array(
						'model' => 'li3_users\tests\cases\models\User'
					)
				)
			);
		}

	}

	public function testClassInstanceAndConnection() {
		$user = new User();
	}

}

?>