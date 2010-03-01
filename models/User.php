<?php

namespace app\models;

use \lithium\util\String;

class User extends \lithium\data\Model {

	public $validates = array();

	protected $_meta = array('source' => 'lithosphere');

	protected $_schema = array(
		'username' => array('type' => 'string', 'length' => 250),
		'password' => array('type' => 'string', 'length' => 250),
		'email' => array('type' => 'string', 'length' => 250),
		'created' => array('type' => 'date'),
	);

	public static function __init(array $options = array()) {
		parent::__init($options);
		static::applyFilter('save', function ($self, $params, $chain) {
			$params['record']->type = 'user';
			if (empty($params['record']->created)) {
				$params['record']->id = $params['record']->email;
				$params['record']->created = date('Y-m-d H:i:s');
				$params['record']->password = String::hash($params['record']->password);
			}
			return $chain->next($self, $params, $chain);
		});
	}

	public static function login($data) {
		if (empty($data['conditions']['username']) || empty($data['conditions']['password'])) {
			return false;
		}
		$result = static::first(array(
			'conditions' => array(
				'design' => 'user', 'view' => 'by_username',
				'key' => json_encode($data['conditions']['username'])
			)
		));
		if (!empty($result) && $data['conditions']['password'] === $result->password) {
			return $result;
		}
		return false;
	}
}

?>