<?php

namespace app\models;

use \lithium\util\Set;
use \lithium\util\String;
use \lithium\storage\Session;
use \app\models\User;

class Post extends \lithium\data\Model {

	public $validates = array();

	protected $_meta = array('source' => 'lithosphere');

	protected $_schema = array(
		'title' => array('type' => 'string', 'length' => 250),
		'content' => array('type' => 'text'),
		'comment_count' => array('type' => 'numeric', 'default' => 0),
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
		$default = array(
			'author' => Session::read('user')
		);
		$params += $default;
		extract($params);
		if (empty($post->comments)) {
			$post->comments = array();
		}
		if(!empty($data['comments'])) {
			if (!empty($author)) {
				$data = static::commentMeta($data, $author);
			}
		}
		if (!empty($data['comments']) && $post->comments) {
			$data['comments'] = Set::merge(
				Set::to('array', $post->comments->data()),
				$data['comments']
			);
		}
		return $post->save($data);
	}

	/**
	 * This method walks through a multidimensional array of data seeking relevant content key and
	 * appending author (user) data, and created date to new (assumed posted) comment.
	 *
	 * @param array $data
	 * @param array $author data from user authenticated session.
	 */
	public static function commentMeta($data = array(), $author) {
		if (!empty($data)) {
			$key = key($data);
			if (array_key_exists('content', $data)) {
				$data['user'] = array(
					'id' => $author['id'],
					'username' => $author['username'],
					'email' => md5($author['email'])
				);
				$data['created'] = date('Y-m-d H:i:s');
			} else {
				$data[$key] = static::commentMeta($data[$key], $author);
			}
		}
		return $data;
	}
}

?>