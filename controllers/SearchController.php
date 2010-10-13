<?php

namespace app\controllers;

use \app\models\Search;
use \lithium\util\Set;
use \lithium\storage\Session;

class SearchController extends \lithium\action\Controller {


	public function index() {
		$q = $results = null;
		if (isset($this->request->query) && isset($this->request->query['q'])) {
			$q = $this->request->query['q'];
			$results = Search::find('posts', array('conditions' => compact('q')));
		}
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
		$filters = $this->request->params['filter'];
		$q = array();
		foreach ($filters as $filter => $options) {
			$q[] = "{$filter}:{$options[2]}";
		}
		$q = implode(' && ', $q);

		$results = Search::find('posts', array('conditions' => compact('q')));

		$title = 'Posts';

		if (!empty($filters['source'])) {
			$title = $filters['source'][1] . ' posts';
		}
		if (!empty($filters['tag'])) {
			$title = $filters['tag'][1];
		}

		if (!empty($filters['date'])) {
			$title .= $filters['date'][1];
		}

		return compact('title','filters','results');
	}

}
