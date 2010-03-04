<?php

namespace app\models;

use \lithium\data\model\Document;
use \lithium\util\Inflector;
use \lithium\storage\Session;
use \app\models\User;
use \app\models\Comment;

class Post extends \lithium\data\Model {

	public $validates = array(
		'title' => array('notEmpty', 'message' => 'Please supply a title.'),
		'content' => array('notEmpty', 'message' => 'Please supply some content.'),
	);

	protected $_meta = array('source' => 'lithosphere');

	protected $_schema = array(
		'title' => array('type' => 'string', 'length' => 250),
		'content' => array('type' => 'text'),
		'comment_count' => array('type' => 'numeric', 'default' => 0),
		'created' => array('type' => 'date'),
	);

	/**
	 * The \app\models\User related to the post
	 *
	 * @var \app\models\User
	 */
	protected $_user = null;

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
	}

	public function user($self) {
		if (!empty($self->_user)) {
			return $self->_user;
		}
		return $self->_user = User::find($self->user_id);
	}

	public function comment($self, $params = array()) {
		$default = array('args' => array(), 'data' => array());
		$params += $default;
		extract($params);

		$comment = Comment::create($data);

		if (empty($self->id) || !$comment->save()) {
			return null;
		}
		$data = $comment->data();
		$comments = !empty($self->comments) ? $self->comments->data() : array();

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
		$comments  = $insert($comments, $args);
		$comment_count = $self->comment_count + 1;
		return $self->save(compact('comments', 'comment_count'));
	}

	public function endorse($self, $options = array()) {
		$defaults = array(
			'author' => Session::read('user'), 'args' => array(),
		);
		$options += $defaults;
		extract($options);

		if (empty($self->id) || empty($author['id'])) {
			return false;
		}
		$data = $self->data() + array('endorsements' => array());

		$endorse = function ($data) use ($author) {
			if (array_search($author['id'], $data['endorsements']) !== false) {
				return $data['endorsements'];
			}
			$data['endorsements'][] = $author['id'];
			return $data['endorsements'];
		};
		if (!empty($args)) {
			$insert = function($comments, $args) use (&$insert, &$endorse) {
				while($args) {
					$key = array_shift($args);

					if (isset($comments[$key])) {
						$result = (array) $comments[$key] + array(
							'comments' => array(), 'endorsements' => array()
						);
						$comments[$key]['comments'] = $insert($result['comments'], $args);
						$comments[$key]['endorsements'] = $endorse($result);
					}
				}
				return $comments;
			};
			$comments = $insert($data['comments'], $args);
		}

		$endorsements = $endorse($data);
		return $self->save(compact('comments', 'endorsements'));
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