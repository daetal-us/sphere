<?php

namespace app\models;

use lithium\util\String;

class Post extends \lithium\data\Model {

	public $validates = array();

	protected $_meta = array('source' => 'lithosphere');

	protected $_schema = array(
		'title' => array('type' => 'string', 'length' => 250),
		'content' => array('type' => 'text'),
		'created' => array('type' => 'date'),
	);

	public static function __init($options = array()) {
		parent::__init($options);
		static::applyFilter('save', function ($self, $params, $chain) {
			$params['record']->type = 'post';
			if (empty($params['record']->created)) {
				$params['record']->created = date('Y-m-d H:i:s');
			}
			return $chain->next($self, $params, $chain);
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