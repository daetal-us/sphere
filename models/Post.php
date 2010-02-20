<?php

namespace app\models;

use \lithium\util\String;
use \lithium\storage\Session;
use \app\models\User;

class Post extends \lithium\data\Model {

	public $validates = array();

	protected $_meta = array('source' => 'lithosphere');

	protected $_schema = array(
		'title' => array('type' => 'string', 'length' => 250),
		'content' => array('type' => 'text'),
		'created' => array('type' => 'date'),
	);

	public static function __init(array $options = array()) {
		parent::__init($options);
		static::applyFilter('save', function ($self, $params, $chain) {
			$params['record']->type = 'post';
			if (empty($params['record']->created)) {
				$params['record']->created = date('Y-m-d H:i:s');
				if ($user = Session::read('user')) {
					$params['record']->user_id = $user['id'];
				}
			}
			return $chain->next($self, $params, $chain);
		});
		static::applyFilter('find', function ($self, $params, $chain) {
			$result = $chain->next($self, $params, $chain);
			if (!empty($params['options']['conditions']['id'])) {
				$result->set(array('user' => User::find($result->user_id)));
			} else {
				while ($row = $result->next()) {
					$row->set(array('user' => User::find($row->user_id)));
				}
			}
			return $result;
		});
	}

	public static function comment($params) {
		extract($params);
		if (empty($record->comments)) {
			$record->comments = array();
		}
		if (!empty($data['comment'])) {
			$comment_id = (!isset($data['comment_id']))
				? count($record->comments)
				: $data['comment_id'] . (count($record->comments) - 1);
			$comments = array(
				'id' => $comment_id,
				'content' => $data['comment']
			);
		}

		if (!empty($comments)) {
			$comments = (!empty($record->comments))
				? $record->comments->data() + $comments : $comments;
		}
		return $comments;
	}
}

?>