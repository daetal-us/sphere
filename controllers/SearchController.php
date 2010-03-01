<?php

namespace app\controllers;

use \app\models\Search;
use \lithium\util\Set;
use \lithium\storage\Session;

class SearchController extends \lithium\action\Controller {

	public function index() {
        /**
		$posts = Search::all(array(
			//'conditions' => array('design' => 'all', 'view' => 'search')
		));
        */
    	return compact('search');
	}
}
