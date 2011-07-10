<?php

namespace app\models;

class Comment extends \lithium\data\Model {

	public $validates = array(
		'content' => array('notEmpty', 'message' => 'Please supply some content.'),
	);

	protected $_meta = array(
		'connection' => false,
		'source' => false,
		'locked' => true
	);

	protected $_schema = array(
		'content' => array('type' => 'text'),
		'rating' => array('type' => 'numeric', 'default' => 0),
		'endorsements' => array('type' => 'array', 'default' => array()),
		'comments' => array('type' => 'array', 'default' => array()),
		'comment_count' => array('type' => 'numeric', 'default' => 0),
		'created' => array('type' => 'date'),
		'user' => array('type' => 'array', 'default' => array())
	);

	protected static $_classes = array(
		'session' => 'lithium\storage\Session',
		'set'     => 'lithium\data\collection\DocumentSet',
	);

	public static function __init(array $options = array()) {
		parent::__init($options);
		$classes = static::$_classes;
		static::applyFilter('save', function ($self, $params, $chain) use ($classes) {
			if (!$params['entity']->validates()) {
				return false;
			}
			if (empty($params['entity']->created)) {
				$params['entity']->created = time();
				if (empty($params['entity']->user) && $user = $classes['session']::read('user', array(
					'name' => 'li3_user'
				))) {
					$params['entity']->user = array(
						'_id' => $user['_id'],
						'email' => $user['email'],
					);
				}
			}
			return true;
		});
	}

	public function comments($comment) {
		$set = static::$_classes['set'];
		if ($comment->comments) {
			return new $set(array(
				'data' => $comment->comments,
				'model' => __CLASS__
			));
		}
		return null;
	}

}

?>