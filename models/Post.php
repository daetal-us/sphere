<?php

namespace app\models;

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
		'_id' => array('type' => 'string', array('primary' => true)),
		'created' => array('type' => 'date'),
		'title' => array('type' => 'string', 'length' => 250),
		'_title' => array('type' => 'array', 'default' => array()),
		'content' => array('type' => 'text'),
		'rating' => array('type' => 'numeric', 'default' => 0),
		'endorsements' => array('type' => 'array', 'default' => array()),
		'comments' => array('type' => 'array', 'default' => array()),
		'comment_count' => array('type' => 'numeric', 'default' => 0),
		'type' => array('type' => 'string', 'default' => 'post'),
		'user_id' => array('type' => 'string', 'default' => null),
	);

	/**
	 * Container for Post Author
	 */
	protected $_user = null;

	/**
	 * Top-level tags
	 */
	public static $tags = array(
		'apps','questions','press','tutorials','code','videos','podcasts','slides','events','docs',
		'jobs','misc'
	);

	protected static $_classes = array(
		'inflector'   => 'lithium\util\Inflector',
		'set'         => 'lithium\data\collection\DocumentSet',
		'session'     => 'lithium\storage\Session',
		'user'        => 'li3_users\models\User',
		'comment'     => 'app\models\Comment',
	);

	public static function __init(array $options = array()) {
		parent::__init($options);
		$classes = static::$_classes;

		static::applyFilter('save', function ($self, $params, $chain) use ($classes) {
			if (empty($params['entity']->created)) {

				$params['entity']->title = trim(preg_replace(
					'/\s{2,}/', ' ', $params['entity']->title
				));

				$_id = $classes['inflector']::slug($params['entity']->title);
				if (strlen($_id) < 5) {
					$_id = $_id . '-' . substr(md5($_id . time()), 0, 5);
				}
				$slug = $_id;
				while ($existing = $self::first($_id)) {
					$_id = "$slug-" .  substr(md5($slug . time()), 0, 4);
				}
				$params['entity']->_id = $_id;

				$params['entity']->created = time();

				$params['entity']->_title = array_filter(array_unique(explode(
					' ', $params['entity']->title
				)));

				if (!empty($params['entity']->tags) && is_string($params['entity']->tags)) {
					$params['entity']->tags = array_unique(array_filter(explode(
						",", str_replace(' ', '', $params['entity']->tags)
					)));
					if (!empty($params['entity']->tags)) {
						$params['entity']->tags->each(function($v) use ($classes) {
							return $classes['inflector']::slug($v);
						});
					}
				}
				if (empty($params['entity']->user_id)
					&& $user = $classes['session']::read('user', array('name' => 'li3_user'))
				) {
					$params['entity']->user_id = $user['_id'];
					$params['entity']->user_id = $user['_id'];
				}
			}

			if (!empty($params['entity']->comments) && is_object($params['entity']->comments)) {
				$params['entity']->comments = $params['entity']->comments->data();
			}

			$params['entity']->rating = $params['entity']->rating();
			return $chain->next($self, $params, $chain);
		});

		static::applyFilter('find', function ($self, $params, $chain) use ($classes) {
			$result = $chain->next($self, $params, $chain);
			if (!empty($result->comments)) {
				$comments = new $classes['set'](array(
					'data' => $result->comments->data(),
					'model' => $classes['comment']
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
		$user = static::$_classes['user'];
		return $record->_user = $user::find($record->user_id);
	}

	public function comment($record, $params = array()) {
		$default = array('args' => array(), 'data' => array());
		$params += $default;
		extract($params);

		$comment = static::$_classes['comment'];
		$comment = $comment::create($data);

		if (empty($record->_id) || !$comment->save()) {
			return null;
		}
		$data = $comment->data();
		$comments = !empty($record->comments) ? $record->comments->data() : array();

		$insert = function($comments, $args) use (&$insert, $data) {
			while($args) {
				$key = array_shift($args);
				if (isset($comments[$key])) {
					$comments[$key] = (array) $comments[$key];
					$result = (array) $comments[$key] + array('comments' => array());
					$comments[$key]['comments'] = $insert((array) $result['comments'], $args);
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

	public function endorse($record, $params = array()) {
		$session = static::$_classes['session'];
		$default = array(
			'author' => $session::read('user', array('name' => 'li3_user')),
			'args' => array(),
		);
		$params += $default;
		extract($params);

		if (empty($record->_id) || empty($author['_id'])) {
			return false;
		}

		$data = $record->data();

		$endorse = function ($comments, $args) use (&$endorse, $author, $data) {
			while($args) {
				$key = array_shift($args);
				if (isset($comments['comments'][$key])) {
					$comments['comments'][$key] = $endorse($comments['comments'][$key], $args);
				}
				if ($key != $data['_id']) {
					return $comments;
				}
			}
			$comments += array('endorsements' => array());
			if ((isset($comments['user_id']) && $author['_id'] == $comments['user_id'])
				|| (isset($comments['user']) && $author['_id'] == $comments['user']['_id'])
				|| (array_search($author['_id'], $comments['endorsements']) !== false)
			){
				return $comments;
			}
			$comments['endorsements'][] = $author['_id'];
			$comments['rating']++;
			return $comments;
		};

		$data = $endorse($data, $args);
		return $record->save($data);
	}

	public static function rating($record) {
		$rating = 0;
		if (is_object($record)) {
			$record = $record->data();
		}
		if (!empty($record['rating'])) {
			$rating = $record['rating'];
		}
		if (!empty($record['comments'])) {
			$rating += count($record['comments'])  * .1;
			foreach ($record['comments'] as $comment) {
				$rating += (static::rating($comment) * .1);
			}
		}
		return ceil($rating);
	}

	public function comments($record) {
		$set = static::$_classes['set'];
		$comments = null;
		if (!empty($record->comments)) {
			$comments = new $set(array(
				'data' => $record->comments->data(),
				'model' => 'app\models\Comment',
			));
		}
		return $comments;
	}


}

?>