<?php

namespace app\controllers;

use \app\models\Search;
use \lithium\util\Set;
use \lithium\storage\Session;

class SearchController extends \lithium\action\Controller {


	public function index() {
		$q = $page = $results = null;
		if (isset($this->request->query)) {
			$keys = array_flip(array('q','page'));
			$get = array_intersect_key($this->request->query, $keys);
			$post = array_intersect_key($this->request->params, $keys);
			$data = $post + $get;
			extract($data);
		}
		$results = Search::find('posts', array('conditions' => compact('q','page')));

		return compact('q','results');
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
			'tag' => null,
			'date' => null,
			'title' => null,
			'page' => null
		);
		extract($this->request->params + $defaults);

		$q = array();
		if (!empty($tag)) {
			$q[] = "tag:{$tag}";
		}

		if (!empty($date)) {
			$q[] = "date:{$date}";
		}

		$q = implode($q, " && ");

		$results = Search::find('posts', array('conditions' => compact('q','page')));

		$url = $this->request->params;

		return compact('title','results','url');
	}

	public function latest() {
		$page = 0;
		// if (!isset($this->request->params['page'])) {
		// 	 $page = $this->request->params['page'];
		// }

		$start = strtotime("last week");
		$end = time();
		$q = "timestamp:[{$start} TO {$end}]";
		$sort = "\\rating";

		$url = $this->request->params;

		$results = Search::find('posts', array('conditions' => compact('q','sort')));

		return compact('results','page','url');
	}

}
