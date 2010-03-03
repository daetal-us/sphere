<?php

namespace app\models;

use \lithium\data\model\Document;
use \lithium\util\Collection;
use \lithium\util\Inflector;
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
				$params['record']->id = Inflector::slug($params['record']->title);
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
				$result = Post::commentCount($result);
				$result = Post::endorsements($result);
			} else {
				$result->first();
				while ($row = $result->current()) {
					$row->set(array('user' => User::find($row->user_id)));
					$row = Post::commentCount($row);
					$row = Post::endorsements($row);
					$result->next();
				}
			}
			return $result;
		});
	}

	public static function comment($params) {
		$default = array(
			'author' => Session::read('user'),
			'args' => array(),
			'post' => null
		);
		$params += $default;
		extract($params);

		if (!empty($data['comment'])) {
			$data['content'] = $data['comment'];
			unset($data['comment']);
		}

		if (empty($post) || empty($data['content'])) {
			return null;
		}

		$data = static::commentMeta($data, $author);

		if (empty($post->comments)) {
			$post->comments = new Document();
		}
		$comments = $post->comments->data();

		if (!empty($args)) {
			$path = '/' . implode('/comments/', array_values($args));
			$current = Set::extract($comments, $path);
			$index = 0;
			if (isset($current[0]['comments']) && count($current[0]['comments']) > 0) {
				$keys = array_keys($current[0]['comments']);
				$index = array_pop($keys) + 1;
			}
			$args[] = $index;

			$comments = Set::insert($comments, implode('.comments.', $args), $data);

		} else {
			$comments[] = $data;
		}
		$data = compact('comments');
		return $post->save($data);
	}

	/**
	 * This method appends author (user) data, and created date to comment data.
	 *
	 * @param array $data
	 * @param array $author data from user authenticated session.
	 */
	public static function commentMeta($data = array(), $author) {
		if (!empty($data) && !empty($author)) {
			$data['user'] = array(
				'id' => $author['id'],
				'username' => $author['username'],
				'email' => md5($author['email'])
			);
			$data['created'] = date('Y-m-d H:i:s');
		}
		return $data;
	}

	public static function commentCount($data = array()) {
		$count = 0;
		if (!empty($data) && !empty($data->comments)) {
			$count = $data->comments->count();
			$data->comments->first();
			while ($comment = $data->comments->current()) {
				if (!empty($comment->comments)) {
					$comment = static::commentCount($comment);
					$count += $comment->comment_count;
				}
				$data->comments->next();
			}
		}
		$data->set(array('comment_count' => $count));
		return $data;
	}

	public static function endorse($id, $options = array()) {
		$defaults = array(
			'author' => Session::read('user'),
			'args' => array(),
			'post' => null
		);
		$options += $defaults;
		extract($options);

		if ((empty($id) && empty($post)) && (!$post = Post::find($id)) && empty($author)) {
			return false;
		}

		$result = false;

		if (empty($post->endorsements)) {
			$post->endorsements = new Document();
		}

		$data = $post->data();

		array_shift($args);
		if (!empty($args)) {
			$path = '/comments/' . implode('/comments/', array_values($args));
			$comment = Set::extract($data, $path);
			$comment = array_shift($comment);
			if (
				!isset($comment['endorsements']) ||
				(array_search($author['id'], $comment['endorsements']) === false)
			) {
				$comment['endorsements'][] = $author['id'];
			}
			$data = Set::insert($data, 'comments.' . implode('.comments.', $args), $comment);
		} else {
			if (array_search($author['id'], $data['endorsements']) === false) {
				$data['endorsements'][] = $author['id'];
			}
		}
		$result = $post->save($data);
		///

		return $result;
	}

	public static function endorsements($data = array()) {
		$rating = 0;
		if (!empty($data)) {
			if (!empty($data->endorsements)) {
				$rating = count($data->endorsements);
			}
			if (!empty($data->comment_count)) {
				$rating += ($data->comment_count * .5);
			}
			if (!empty($data->comments)) {
				$data->comments->first();
				while ($comment = $data->comments->current()) {
					$comment = static::endorsements($comment);
					$data->comments->next();
				}
			}
		}
		$data->set(array('rating' => round($rating)));
		return $data;
	}
}

?>