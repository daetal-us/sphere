<?php

namespace app\controllers;

use \app\models\Search;
use \lithium\util\Set;
use \lithium\storage\Session;

class SearchController extends \lithium\action\Controller {

	public function index() {
        if (isset($this->request->query) && isset($this->request->query['term'])) {

            $term = $this->request->query['term'];

            $results = Search::find(
                'by_title', array('conditions' => array('q' => $term)));

            return $results;
        }

        return true;
	}
}
