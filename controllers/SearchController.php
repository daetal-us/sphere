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

		$title = '';

		if (!empty($filters['source'])) {
			$title = $filters['source'][1];
		}

		$title .= ' posts';

		if (!empty($filters['date'])) {
			$title .= $filters['date'][1];
		}

		return compact('title','filters','results');
	}

}
