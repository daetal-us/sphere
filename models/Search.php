<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\data\entity\Document;
use \lithium\data\collection\DocumentSet;

class Search extends \lithium\data\Model {

	protected $_meta = array('source' => 'lithosphere');

	public static function __init(array $options = array()) {
		parent::__init($options);
		$self = static::_object();
		$self->_setupFinders();
	}

	protected function _setupFinders() {
		$finders = array(
			'posts'
		);
		$self = static::_object();
		foreach ($finders as $finder) {
			$self->_finders[$finder] = function($self, $params, $chain) {
				$defaults = array(
					'include_docs' => 'true',
					'limit' => 10,
					'skip' => 0
				);
				$query = (array) $params['options']['conditions'] + $defaults;

				if (isset($query['page']) && $query['page'] > 1) {
					$query['skip'] = ($query['page'] - 1) * $query['limit'];
				}
				unset($query['page']);

				$connection = Connections::get($self::meta('connection'));
				$result = $connection->get(
					$self::meta('source') . '/_fti/_design/search/' . $params['type'],
					$query, array('type' => null)
				);

				if (empty($result->total_rows)) {
					return 0;
				}
				$result = new Document(array(
					'data' => (array) $result,
					'model' => __CLASS__
				));

				$result->rows = new DocumentSet(array(
					'data' => $result->rows->data(),
					'model' => __CLASS__
				));

				return $result;
			};
		}
	 }

	/**
	 * Extract post data from a search result row and return as a Post model
	 *
	 * @param object $result
	 * @return object Post data
	 * @see \app\models\Post
	 */
	public function post($result) {
		$result->doc->id = $result->doc->_id;
		$data = $result->data();
		return \app\models\Post::create($data['doc']);
	}

}

