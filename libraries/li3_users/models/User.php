<?php
/**
 * Lithium Sphere: communized sphere of influence
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://www.opensource.org/licenses/MIT The MIT License
 */

namespace li3_users\models;

use lithium\util\String;
use lithium\util\Validator;

Validator::add('uniqueUserValue', function ($value, $format, $options) {
	$conditions = array();
	if (!empty($value)) {
		$conditions[$options['field']] = $value;
		if ($options['events']['update'] && !empty($options['values']['_id'])) {
			if ($options['field'] == '_id' && $value == $options['values']['_id']) {
				return true;
			}
			$conditions['_id'] = array('$ne' => $options['values']['_id']);
		}
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
		'source' => 'users',
		'connection' => 'li3_users',
		'initialized' => false
	);

	public $validates = array(
		'_id' => array(
			array('notEmpty', 'message' => 'a user id is required.'),
			array(
				'alphaNumeric',
				'message' => 'only numbers and letters are allowed for your username.'
			),
			array(
				'lengthBetween',
				'options' => array(
					'min' => 1,
					'max' => 250
				),
				'message' => 'please provide a user id.'
			),
			array('uniqueUserValue', 'message' =>  'that username is already taken.')
		),
		'password' => array(
			array('notEmpty', 'message' => 'a password is required.'),
		),
		'email' => array(
			array('email', 'message' => 'please provide a valid email address.'),
			array('uniqueUserValue', 'message' =>  'that email already has an account.')
		)
	);

	protected $_schema = array(
		'_id' => array('type' => 'string', 'length' => 250, 'primary' => true),
		'password' => array('type' => 'string', 'length' => 250),
		'email' => array('type' => 'string', 'length' => 250),
		'token' => array('type' => 'string', 'length' => 16),
		'expires' => array('type' => 'date'),
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
			}
			if (!empty($params['entity']->password)) {
				$params['entity']->password = String::hash($params['entity']->password);
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

	/**
	 * Validate a token against a user id
	 *
	 * @param array $data token and _id of user record
	 * @return boolean
	 */
	public static function reset($data = array()) {
		$defaults = array(
			'token' => null,
			'_id' => null,
		);
		extract($data + $defaults);
		if (!empty($token) && !empty($_id)) {
			$user = static::find('all', array(
				'conditions' => compact('_id','token'), 'limit' => 1
			));
			if ($user->count()) {
				return $user->first()->expires->sec > time();
			}
		}
		return false;
	}

	/**
	 * Initialize a token and expiry time for a user record
	 *
	 * @param array $record user record
	 * @param integer optional timestamp for expiration time to count from
	 * @return boolean|string token if valid else `false`
	 */
	public function token($record, $time = null) {
		$token = false;
		if (!empty($record->_id) && !empty($record->password)) {
			if (empty($time)) {
				$time = time();
			}
			$token = md5(
				md5($record->_id) . md5($record->password) . md5($time)
			);
			$expires = date('Y-m-d H:i:s', strtotime('+10 minutes', $time));
			$record->set(compact('token','expires'));
			$record->save();
		}
		return $token;
	}
}

?>