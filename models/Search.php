<?php

namespace app\models;

use \lithium\data\Connections;

class Search extends \lithium\data\Model {

    protected $_meta = array('source' => 'lithosphere');

    public static function __init(array $options = array()) {
        parent::__init($options);
        $self = static::_instance();
        $self->_setupFinders();
    }

    protected function _setupFinders() {
		$finders = array('by_title', 'by_content');
        $self = static::_instance();

        foreach ($finders as $finder) {
            $self->_finders[$finder] = function($self, $params, $chain) {
                $query = (array) $params['options']['conditions'] + array('include_docs' => 'true');
                $result = Connections::get($self::meta('connection'))->get(
                    $self::meta('source') . '/_fti/search/' . $params['type'],
					$query, array('type' => null)
                );
                if (empty($result->total_rows)) {
                    return 0;
                }
                return $result;
            };
        }
    }
}

