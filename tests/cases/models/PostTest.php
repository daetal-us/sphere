<?php

namespace app\tests\cases\models;

use \app\models\Post;
use \lithium\data\Connections;

class PostTest extends \lithium\test\Unit {

	public function setUp() {
		Connections::add('test', 'http', array('adapter' => 'CouchDb'));
		Connections::get('test')->describe('test_posts');
		Post::__init(array('connection' => 'test', 'source' => 'test_posts'));
	}

	public function tearDown() {

	}

	public function testSave() {
		$post = Post::create(array('title' => 'the title', 'content' => 'the content'));
		$result = $post->save();
		$this->assertTrue($result);
	}

	public function testSaveComments() {
		$post = Post::find('first');

		$result = $post->save(array(
 			'comments' => array(
				'comment on the post'
			)
		));
		$this->assertTrue($result);

		$post = Post::find($post->id);

		$expected = 'comment on the post';
		$result = $post->comments[0];
		$this->assertEqual($expected, $result);
	}

}

?>