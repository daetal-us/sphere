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
		),
        /**
         * For the search we are going to do a very simple
         * saerch to start with a very simple search that
         * will return all values from within the title and
         * from the content of a post, for a searched term. 
         */
        'search' => array(
            'id'       => '_design/search',
            'fulltext' => array(
                // Search by the post title
                'by_title' => array(
                    'index' => 'function(doc) {
                        var ret = new Document();
                        if (doc.type && doc.type == "post" && doc.title) {
                            ret.add(doc.title);
                        }

                        return ret;
                    }'
                ),

                // Search by the post content
                'by_content' => array(
                    'index' => 'function(doc) {
                        var ret = new Document();
                        if (doc.type && doc.type == "post" && doc.type) {
                            ret.add(doc.content);
                        }

                        return ret;
                    }'
                ),


            ),
        ),
	);

	public static function create($data = 'latest') {
		if (!isset(static::$views[$data])) {
			return false;
		}
		return parent::create(static::$views[$data]);
	}
}

?>
