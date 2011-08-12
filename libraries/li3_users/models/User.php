<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_users\models;

use lithium\util\String;
use lithium\util\Validator;

Validator::add('uniqueUserValue', function ($value, $format, $options) {
	$conditions = array();
	if (!empty($value)) {
		$conditions[$options['field']] = $value;
		return !(boolean) User::find('count', compact('conditions'));
	}
	return false;
});

class User extends \lithium\data\Model {

	protected $_meta = array(
		'key' => '_id',
		'name' => null,
		'title' => null,
		'class' => null,
		'locked' => true,
		'source' => null,
		'connection' => 'li3_users',
		'initialized' => false
	);

	public $validates = array(
		'_id' => array(
			array(
				'alphaNumeric',
				'message' => 'Only numbers and letters are allowed for your username'
			),
			array('notEmpty', 'message' => 'Please provide a user id.'),
			array(
				'lengthBetween',
				'options' => array(
					'min' => 4,
					'max' => 250
				),
				'message' => 'Password must be be at least.'
			),
			array('uniqueUserValue', 'message' =>  'That username is already taken.')
		),
		'password' => array(
			array('notEmpty', 'message' => 'You must provide a password.'),
			array(
				'lengthBetween',
				'options' => array(
					'min' => 6,
					'max' => 250
				),
				'message' => 'Password must be be at least.'
			)
		),
		'email' => array(
			array('email', 'message' => 'Please provide a valid email address.'),
			array('uniqueUserValue', 'message' =>  'That email already has an account.')
		)
	);

	protected $_schema = array(
		'_id' => array('type' => 'string', 'length' => 250, 'primary' => true),
		'password' => array('type' => 'string', 'length' => 250),
		'email' => array('type' => 'string', 'length' => 250),
		'created' => array('type' => 'date'),
		'settings' => array('type' => 'array'),
		'type' => array('type' => 'string', 'default' => 'user')
	);

	/**
	 * Save Filter
	 *
	 * The save filter currently works as follows:
	 * 	- if no `created` parameter present in entity, new user is assumed
	 * 	- if `new_password` parameter passed in entity, `password` becomes this new value
	 *
	 * @param array $options
	 * @return void
	 */
	public static function __init(array $options = array()) {
		static::config($options);
		static::applyFilter('save', function ($self, $params, $chain) {
			if (empty($params['entity']->created)) {
				$params['entity']->created = date('Y-m-d H:i:s');
				$params['entity']->password = String::hash($params['entity']->password);
			}
			if (!empty($params['entity']->new_password)) {
				$params['entity']->password = String::hash($params['entity']->new_password);
			}
			return $chain->next($self, $params, $chain);
		});
	}

	/**
	 * Login
	 *
	 * @param array $data user data
	 * @return mixed false if invalid data or incorrect password, otherwise `user` entity retruned
	 * @todo set cooldown period for (10?) failed login attempts
	 */
	public static function login($data) {
		if (empty($data['conditions']['_id']) || empty($data['conditions']['password'])) {
			return false;
		}
		$result = static::first($data['conditions']['_id']);
		if (!empty($result) && $data['conditions']['password'] === $result->password) {
			return $result;
		}
		return false;
	}
}

?>