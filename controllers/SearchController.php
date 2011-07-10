<?php

namespace app\controllers;

use \app\models\Post;
use \lithium\util\Set;
use \lithium\storage\Session;

class SearchController extends \lithium\action\Controller {

	protected $_limit = 20;

	public function index() {
		$q = $results = null;

		$page = 1;
		if (isset($this->request->params['page'])) {
			 $page = $this->request->params['page'];
		}

		if (isset($this->request->query)) {
			$keys = array_flip(array('q','page'));
			$get = array_intersect_key($this->request->query, $keys);
			$post = array_intersect_key($this->request->params, $keys);
			$data = $post + $get;
			extract($data);
		}
		$conditions = $this->_queryToConditions($q);

		$limit = $this->_limit;
		$offset = ($page - 1) * $limit;

		$count = Post::find('count', compact('conditions'));
		$results = Post::find('all', compact('conditions','limit','offset'));
		$url = $this->request->params + $data;

		return compact('q','results','count','url','page','limit');
	}

	public function tag() {
		$tag = $this->request->params['tag'];
		$this->request->params['filter'] = array(
			'tag' => array(null, null, $tag)
		);
		$render = $this->filter();
		$this->set(array('title' => "Posts tagged `{$tag}`") + $render);
		$this->render(array('template' => 'filter'));
	}

	/**
	 * Filter posts by common rules as defined in application routes.
	 *
	 * @see /app/config/routes.php
	 */
	public function filter() {
		$defaults = array(
			'author' => null,
			'tag' => null,
			'date' => null,
			'title' => null,
			'page' => 1,
			'limit' => 2,
			'order' => array(
				'rating' => -1,
				'created' => -1
			),
		);
		extract($this->request->params + $defaults);

		$conditions = array();
		if (!empty($tag)) {
			if (empty($title)) {
				$title = "Posts tagged `{$tag}`";
			}
			$conditions['tags'] = $tag;
		}
		if (!empty($date)) {
			$conditions['created'] = array(
				'$gte' => new \MongoDate($date[0]),
				'$lte' => new \MongoDate($date[1])
			);
		}
		if (!empty($_id)) {
			if (empty($title)) {
				$title = "Posts by {$_id}";
			}
			$conditions['user_id'] = $_id;
		}

		$limit = $this->_limit;
		$offset = ($page - 1) * $limit;

		$count = Post::find('count', compact('conditions'));
		$results = Post::find('all', compact('conditions','order','offset','limit'));

		$url = $this->request->params;

		return compact('title','results','count','url','page','limit');
	}

	public function latest() {
		$page = 1;
		if (isset($this->request->params['page'])) {
			 $page = $this->request->params['page'];
		}

		$start = strtotime("last month");
		$end = time();
		$created = array(
			'$gte' => new \MongoDate($start),
			'$lte' => new \MongoDate($end)
		);

		$conditions = compact('created');
		$order = array('rating' => -1);

		$url = $this->request->params;

		$limit = $this->_limit;
		$offset = ($page - 1) * $limit;

		$count = Post::find('count', compact('conditions'));
		$results = Post::find('all', compact('conditions', 'order','offset','limit'));

		return compact('results','count','url','page','limit');
	}

	protected function _queryToConditions($query) {
		$columns = array(
			'user'   => 'user_id',
			'author' => 'user_id',
			'title'  => '_title',
			'tag'    => 'tags',
			'tags'   => 'tags',
			'date'   => 'created',
			'from'   => 'created',
			'to'     => 'created',
			'on'     => 'created'
		);

		$conditions = array();
		if (!empty($query)) {
			$pattern = '/(?P<key>\w+):\s*(?P<value>(?!(\w)+:)(\w|".+")+)/';
			preg_match_all($pattern, $query, $matches);
			if (!empty($matches)) {
				foreach ($matches['key'] as $index => $key) {
					if (array_key_exists($key, $columns)) {
						$value = $matches['value'][$index];
						switch ($key) {
							case 'created':
							case 'date':
							case 'on':
								$start = strtotime(date('Y-m-d', strtotime($value)));
								$end = strtotime(
									'midnight', strtotime(date('Y-m-d', strtotime($value)))
								);
							break;
							case 'to':
								$end = $end ?: strtotime($value);
							break;
							case 'from':
								$start = $start ?: strtotime($value);
							break;
							default:
								$conditions[$columns[$key]] = $matches['value'][$index];
							break;
						}
						if (!empty($start) || !empty($end)) {
							$conditions['created'] = array();
						}
						if (!empty($start)) {
							$conditions['created']['$gte'] = $start;
						}
						if (!empty($end)) {
							$conditions['created']['$lte'] = $end;
						}
					}
				}
			}
			if (empty($conditions)) {
				$conditions = array(
					'$or' => array(
						array('tags' => $query),
						array('_title' => $query),
						array('user_id' => $query),
					)
				);
			}
		}
		return $conditions;
	}

}
