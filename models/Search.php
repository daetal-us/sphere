<?php

namespace app\models;

use \lithium\data\model\Document;
use \lithium\util\Collection;
use \lithium\util\Inflector;
use \lithium\util\Set;
use \lithium\util\String;

class Search extends \lithium\data\Model {

	protected $_meta = array('source' => 'lithosphere');

	public static function __init(array $options = array()) {
		parent::__init($options);
	}
}
