<?php

namespace app\models;

use \lithium\storage\Session;
use \lithium\data\collection\DocumentSet;
use \app\models\Post;

class Comment extends \lithium\data\Model {

	public $validates = array(
		'content' => array('notEmpty', 'message' => 'Please supply some content.'),
	);

	protected $_meta = array('source' => false);

	protected $_schema = array(
		'content' => array('type' => 'text'),
		'rating' => array('type' => 'numeric', 'default' => 0),
		'endorsements' => array('type' => 'array'),
		'comments' => array('type' => 'array'),
		'comment_count' => array('type' => 'numeric', 'default' => 0),
		'created' => array('type' => 'date'),
		'user' => array('type' => 'array')
	);

	public static function __init(array $options = array()) {
		parent::__init($options);

		static::applyFilter('save', function ($self, $params, $chain) {
			if (!$params['entity']->validates()) {
				return false;
			}
			if (empty($params['entity']->created)) {
				$params['entity']->created = time();

				if (empty($params['record']->user) && $user = Session::read('user', array(
					'name' => 'li3_user'
				))) {
					$params['entity']->user = array(
						'id' => $user['id'],
						'username' => $user['username'],
						'email' => $user['email'],
					);
				}
			}
			return true;
		});
	}

	public function comments($comment) {
		if ($comment->comments) {
			return new DocumentSet(array(
				'data' => $comment->comments,
				'model' => __CLASS__
			));
		}
		return null;
	}

}

?>