<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\data\collection\Document;

class Search extends \lithium\data\Model {

	protected $_meta = array('source' => 'lithosphere');

	public static function __init(array $options = array()) {
		parent::__init($options);
		$self = static::_instance();
		$self->_setupFinders();
	}

	protected function _setupFinders() {
		$finders = array(
			'by_title',
			'by_subject',
			'by_email',
			'by_comments'
		);
		$self = static::_instance();
		foreach ($finders as $finder) {
			$self->_finders[$finder] = function($self, $params, $chain) {
				$query = (array) $params['options']['conditions'] + array('include_docs' => 'true');
				$connection = Connections::get($self::meta('connection'));
				$result = $connection->get(
					$self::meta('source') . '/_fti/search/' . $params['type'],
					$query, array('type' => null)
				);
				if (empty($result->total_rows)) {
					return 0;
				}
				return new Document(array(
					'items' => $result,
					'model' => __CLASS__
				));
			};
		}
	 }

	public function post($record) {
		$record->doc->id = $record->doc->_id;
		return new Document(array(
			'items' => $record->doc->data(),
			'model' => '\app\models\Post'
		));
	}

}

