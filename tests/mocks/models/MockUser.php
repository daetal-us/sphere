<?php

namespace app\tests\mocks\models;

use lithium\data\collection\DocumentSet;
use lithium\data\entity\Document;

class MockUser extends \lithium\data\Model {

	protected $_meta = array(
		'connection' => false,
	);

	public static function find($type = 'all', array $options = array()) {
		switch ($type) {
			case 'all':
				return new DocumentSet(array('data' => array(
					array('_id' => 'pointlessjon', 'email' => 'pointlessjon@example.com'),
					array('_id' => 'gwoo', 'title' => 'gwoo@example.com'),
				)));
			break;
			case 'gwoo':
				return new Document(array('data' => array(
					'_id' => 'gwoo', 'title' => 'gwoo@example.com'
				)));
			break;
			case 'pointlessjon':
			case 'first':
				return new Document(array('data' => array(
					'_id' => 'pointlessjon', 'email' => 'pointlessjon@example.com'
				)));
			break;
			default:
				return null;
			break;
		}
	}
}

?>