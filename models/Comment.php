<?php

namespace app\models;

use \lithium\storage\Session;

class Comment extends \lithium\data\Model {

	public $validates = array(
		'content' => array('notEmpty', 'message' => 'Please supply some content.'),
	);

	protected $_meta = array('source' => false);

	protected $_schema = array(
		'content' => array('type' => 'text'),
		'rating' => array('type' => 'numeric', 'default' => 0),
		'comment_count' => array('type' => 'numeric', 'default' => 0),
		'created' => array('type' => 'date'),
	);

	public static function __init(array $options = array()) {
		parent::__init($options);

		static::applyFilter('save', function ($self, $params, $chain) {
			if (!$params['record']->validates()) {
				return false;
			}
			if (empty($params['record']->created)) {
				$params['record']->created = date('Y-m-d H:i:s');

				if (empty($params['record']->user) && $user = Session::read('user')) {
					$params['record']->user = array(
						'id' => $user['id'],
						'username' => $user['username'],
						'email' => $user['email'],
					);
				}
			}
			return true;
		});
	}
}

?>