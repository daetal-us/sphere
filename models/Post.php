<?php

namespace app\models;

use \lithium\data\collection\DocumentSet;
use \lithium\util\Inflector;
use \lithium\storage\Session;
use \li3_users\models\User;
use \app\models\Comment;

class Post extends \lithium\data\Model {

	public $validates = array(
		'title' => array('notEmpty', 'message' => 'Please supply a title.'),
		'content' => array('notEmpty', 'message' => 'Please supply some content.'),
	);

	protected $_meta = array(
		'source' => 'lithosphere',
		'locked' => false
	);

	protected $_schema = array(
		'id' => array('type' => 'string'),
		'rev' => array('type' => 'string'),
		'title' => array('type' => 'string', 'length' => 250),
		'content' => array('type' => 'text'),
		'rating' => array('type' => 'numeric', 'default' => 0),
		'endorsements' => array('type' => 'array'),
		'comments' => array('type' => 'array'),
		'comment_count' => array('type' => 'numeric', 'default' => 0),
		'created' => array('type' => 'date'),
		'type' => array('type' => 'string', 'default' => 'post'),
		'user_username' => array('type' => 'string'),
		'user_id' => array('type' => 'string')
	);

	public static $tags = array(
		'apps','questions','press','tutorials','code','videos','podcasts','slides','events','docs'
	);

	public static function __init(array $options = array()) {
		parent::__init($options);
		static::applyFilter('save', function ($self, $params, $chain) {
			if (empty($params['entity']->created)) {
				$params['entity']->id = Inflector::slug($params['entity']->title);
				$params['entity']->created = time();
				if (!empty($params['entity']->tags) && is_string($params['entity']->tags)) {
					$params['entity']->tags = array_unique(array_filter(explode(
						",", str_replace(' ', '', $params['entity']->tags)
					)));
				}
				if ($user = Session::read('user', array('name' => 'li3_user'))) {
					$params['entity']->user_username = $user['username'];
					$params['entity']->user_id = $user['id'];
				}
			}
			$params['entity']->rating = $params['entity']->rating();
			return $chain->next($self, $params, $chain);
		});

		static::applyFilter('find', function ($self, $params, $chain) {
			$result = $chain->next($self, $params, $chain);

			if (empty($result)) {
				return $result;
			}

			if (!empty($result->comments)) {
				$comments = new DocumentSet(array(
					'data' => $result->comments->data(),
					'model' => 'app\models\Comment'
				));
				$result->set(compact('comments'));
			}

			return $result;
		});
	}

	public function user($record) {
		if (!empty($record->_user)) {
			return $record->_user;
		}
		return $record->_user = User::find($record->user_id);
	}

	public function comment($record, $params = array()) {
		$record = static::first($record->id);
		$default = array('args' => array(), 'data' => array());
		$params += $default;
		extract($params);

		$comment = Comment::create($data);

		if (empty($record->id) || !$comment->save()) {
			return null;
		}
		$data = $comment->data();
		$comments = !empty($record->comments) ? $record->comments->data() : array();

		$insert = function($comments, $args) use (&$insert, $data) {
			while($args) {
				$key = array_shift($args);
				if (isset($comments[$key])) {
					$result = (array) $comments[$key] + array('comments' => array());
					$comments[$key]['comments'] = $insert($result['comments'], $args);
					$comments[$key]['comment_count']++;
				}
				return $comments;
			}
			return array_merge((array) $comments, array($data));
		};
		$record->comment_count++;

		$comments  = $insert($comments, $args);
		$record->set(compact('comments'));

		return $record->save();
	}

	public function endorse($record, $options = array()) {
		$defaults = array(
			'author' => Session::read('user', array('name' => 'li3_user')), 'args' => array(),
		);
		$options += $defaults;
		extract($options);

		if (empty($record->id) || empty($author['id'])) {
			return false;
		}
		$data = $record->data() + array('endorsements' => array());

		$endorse = function ($data, $args) use ($author) {
			if (array_search($author['id'], $data['endorsements']) !== false) {
				return $data['endorsements'];
			}
			$data['endorsements'][] = $author['id'];
			return $data['endorsements'];
		};
		$endorsements = $endorse($data, $args);
		return $record->save(compact('comments', 'endorsements'));
	}

	public static function rating($record) {
		$rating = 0;
		if (is_object($record)) {
			if (get_class($record) == 'lithium\data\entity\Document') {
				$record = $record->data();
			}
			$record = (array) $record;
		}
		if (!empty($record['endorsements'])) {
			$rating += count($record['endorsements']);
		}
		if (!empty($record['comments'])) {
			$rating += count($record['comments'])  * .5;
			foreach ($record['comments'] as $comment) {
				$rating += (static::rating($comment) * .5);
			}
		}
		return ceil($rating);
	}

}

?>