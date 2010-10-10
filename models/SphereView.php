<?php

namespace app\models;

class SphereView extends \lithium\data\Model {

	protected $_meta = array(
		'key' => 'id',
		'name' => null,
		'title' => null,
		'class' => null,
		'locked' => false,
		'source' => null,
		'connection' => 'default',
		'initialized' => false
	);

	public $_schema = array(
		'id' => array('type' => 'string', 'primary' => true),
		'language' => array('type' => 'string', 'default' => 'javascript'),
		'views' => array('type' => 'array'),
		'fulltext' => array('type' => 'array')
	);

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
				)
			)
		),
		'search' => array(
			'id' => '_design/search',
			'fulltext' => array(
				'posts' => array(
					/**
					 * CouchDB-Lucene indexing
					 */
					'index' => 'function(doc) {
						var ret = null;
						if (doc.type && doc.type == "post") {
							ret = new Document();
							ret.add(doc.content + \' \' + doc.title);
							ret.add(doc.content, {field: "content"});
							ret.add(doc.title, {field: "title"});
							ret.add(doc.user_username, {field: "author", index:"not_analyzed"});
							ret.add(doc.user_id, {field: "user_id", index:"not_analyzed"});

							if (doc.tags && typeof doc.tags == "object") {
								for (tag in doc.tags) {
									ret.add(doc.tags[tag], {field: "tag"});
								}
							}

							var date = new Date(doc.created * 1000);

							var d = {
								month: date.getMonth() + 1,
								day: date.getDate(),
								hour: date.getHours(),
								minute: date.getMinutes()
							};

							var altDate = date.getFullYear() + "-" + d.month + "-" + d.day;

							for (piece in d) {
								if (d[piece] < 10) {
									// Add invididual piece without padded zero for friendlier searching
									ret.add(d[piece] + "", {field: piece, type: "string", index: "not_analyzed"});
									d[piece] = "0" + d[piece];
								}
							}

							d["year"] = date.getFullYear();

							var date = d.year + "-" + d.month + "-" + d.day;
							ret.add(date, {field: "date", type: "string", index: "not_analyzed"});
							ret.add(altDate, {field: "date", type: "string", index: "not_analyzed"});

							// Time (HH:MM)
							ret.add(d.hour + ":" + d.minute, {field: "time", type: "string", index: "not_analyzed"});

							// Add individual time pieces
							for (i in d) {
								ret.add(d[i] + "", {field: i, type: "string", index: "not_analyzed"});
							}

							var source = "sphere";
							// Add domain as source if content is simply a url
							var reg = /^https?\:\/\/([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3})(:[a-zA-Z0-9]*)?\/?(\S)*$/;
							if (reg.test(doc.content)) {
								var match = reg.exec(doc.content);
								if (match[1]) {
									source = match[1];
								}
							}
							ret.add(source, {field: "source", index: "not_analyzed"});
						}
						return ret;
					}'
				)
			)
		)
	);
}

?>
