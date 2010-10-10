<?php

namespace app\models;

use \lithium\storage\Session;
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

	public function replies($comment) {}

	public function rating($record) {
		return 0;
		$rating = (integer) count($record->endorsements);
		$rating += ((integer) $record->comment_count * .5);
		if (!empty($record->comments)) {
			$record->comments->first();
			while($comment = $record->comments->current()) {
				$rating += (integer) $comment->rating();
				$record->comments->next();
			}
		}
		$record->set(compact('rating'));
		return $rating;
	}
}

?>