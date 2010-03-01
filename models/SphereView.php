<?php

namespace app\models;

class SphereView extends \lithium\data\Model {

	protected $_meta = array('source' => 'lithosphere');

	public static $views = array(
		'all' => array(
			'id' => '_design/all',
			'language' => 'javascript',
			'views' => array(
				'posts' => array(
					'map' => 'function(doc) {
						if (doc.type && doc.type == "post" && doc.created) {
							emit(doc.created, doc);
						}
					}'
				),
				'users' => array(
					'map' => 'function(doc) {
						if (doc.type && doc.type == "user" && doc.created) {
							emit(doc.created, doc);
						}
					}'
				),
			),
		),
		'user' => array(
			'id' => '_design/user',
			'language' => 'javascript',
			'views' => array(
				'by_username' => array(
					'map' => 'function(doc) {   
						if(doc.type && doc.type == "user" && doc.username) {
							emit(doc.username, doc);
						}
					}'
				)
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
