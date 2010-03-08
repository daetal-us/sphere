<?php

namespace app\models;

class SphereView extends \lithium\data\Model {

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
		 * For the search we are going to do a very simple search to start with a very simple search
		 * that will return all values from within the title and from the content of a post, for a
		 * searched term.
		 */
		'search' => array(
			'id' => '_design/search',
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

				// Search by user email
				'by_email' => array(
					'index' => 'function(doc) {
						var ret = new Document();
						if (doc.type && doc.type == "user") {
							ret.add(doc.email);
						}

						return ret;
					}'
				 ),

				// Search by comment
				'by_comments' => array(
					'index' => 'function(doc) {
						var ret = new Document();
						if (doc.type && doc.type == "post" && doc.comments && doc.comments.length > 0) {
							for (var i = 0; i < doc.comments.length; i++) {
								if (doc.comments[i] && doc.comments[i].content) {
									ret.add(doc.comments[i].content);
								}
							}
						}

						return ret;
					}'
				),
			),
		),
	);
}

?>
