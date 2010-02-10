<?php

namespace app\models;

class SphereView extends \lithium\data\Model {

	protected $_meta = array('source' => 'lithosphere');

	public static $views = array(
		'all' => array(
			'id' => '_design/all',
			'language' => 'javascript',
			'views' => array(
				'latest' => array(
					'map' => 'function(doc) {
						emit(doc.created, doc);
					}'
				),
				'popular' => array(
					'map' => 'function(doc) {
						emit(doc.points, doc);
					}'
				),
			)
		)
	);

	public static function create($data = 'latest') {
		if (!isset(static::$views[$data])) {
			return false;
		}
		return parent::create(static::$views[$data]);
	}
}

?>