<?php

namespace app\models;

use \lithium\data\Connections;

class Search extends \lithium\data\Model {

    protected $_meta = array('source' => 'lithosphere');

    protected $finders = array('by_title', 'by_content');

    public static function __init(array $options = array()) {
        parent::__init($options);
        $self = static::_instance();
        $self->_setupFinders();
    }

    protected function _setupFinders()
    {
        $self = static::_instance();
        foreach ($self->finders as $finder) {
            $self->_finders[$finder] = function($self, $params, $chain) {
                $query = (array) $params['options']['conditions'];

                $result = Connections::get($self::meta('connection'))->get(
                    $self::meta('source') . '/_fti/search/' . $params['type'], $query
                );
                
                if (empty($result->total_rows)) {
                    return 0;
                }

                return $result->total_rows;
            };
        }
    }
}

