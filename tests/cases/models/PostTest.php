<?php

namespace app\tests\cases\models;

use app\tests\mocks\models\MockPost as Post;
use lithium\data\Connections;
use lithium\data\model\Query;
use lithium\util\Inflector;

class PostTest extends \lithium\test\Unit {

	public function setUp() {}

	public function tearDown() {
		Post::all()->delete();
	}

	public function testSave() {
		$post = Post::create(array('title' => 'the title', 'content' => 'the content'));
		$expected = Inflector::slug($post->title);
		$save = $post->save();
		$result = strstr($post->_id, $expected);
		$this->assertTrue($result);

		$data = $post->data();
		unset($data['_id']);

		$new = Post::create($data);
		$new->save($data);
		$this->assertNotEqual($post->_id, $new->_id);

		$expected = $content = 'updated content';
		$post->save(compact('content'));
		$post = Post::first($post->_id);
		$this->assertEqual($expected, $post->content);

		$post = Post::create(array('title' => 'sm', 'content' => 'title too short'));
		$title = $post->title;
		$post->save();
		$this->assertNotEqual($title, $post->_id);

		$post = Post::create(array(
			'title' => 'test',
			'content' => 'testing tags',
			'tags' => 'one,Tag Two, ∆three, '
		));
		$post->save();
		$result = $post->tags->data();

		$expected = array('one','Tag-Two','three');
		$this->assertEqual($expected, $result);

		$expected = 'pointlessjon';
		$result = $post->user_id;
		$this->assertEqual($expected, $result);

		$post = Post::create(array(
			'title' => 'forcing user id',
			'content' => 'shtuff',
			'user_id' => 'lithosphere'
		));
		$post->save();

		$expected = 'lithosphere';
		$result = $post->user_id;
		$this->assertEqual($expected, $result);
	}

	public function testPostUser() {
		$post = Post::create(array(
			'title' => 'my title',
			'content' => 'jurassic park 3 sucked'
		));
		$post->save();
		$user = $post->user();

		$expected = 'pointlessjon';
		$result = $user->_id;
		$this->assertEqual($expected, $result);

		// second time tests use of model cache
		$user = $post->user();

		$expected = 'pointlessjon';
		$result = $user->_id;
		$this->assertEqual($expected, $result);

		$post = Post::create(array(
			'title' => 'new title',
			'content' => 'titanic 2 was ok',
			'user_id' => 'gwoo'
		));
		$post->save();
		$user = $post->user();

		$expected = 'gwoo';
		$result = $user->_id;
		$this->assertEqual($expected, $result);

		$post = Post::create(array(
			'title' => 'how i do this?',
			'content' => 'LOL',
			'user_id' => 'mork'
		));
		$post->save();
		$user = $post->user();

		$expected = null;
		$result = $user;
		$this->assertEqual($expected, $result);
	}

	public function testComment() {
		$user = array(
			'_id' => 'gwoo',
			'email' => 'gwoo@example.com'
		);
		$post = Post::create(array('title' => 'another title', 'content' => 'the content'));
		$post->save();

		$data = array('content' => 'cool') + compact('user');
		$args = null;
		$result = $post->comment(compact('data', 'args'));

		$expected = 1;
		$result = $post->comments->count();
		$this->assertEqual($expected, $result);

		$comment = $post->comments->first();
		$expected = 'cool';
		$result = $comment['content'];
		$this->assertEqual($expected, $result);

		$expected = $user;
		$result = $comment['user']->data();
		$this->assertEqual($expected, $result);

		$data = array('content' => 'super cool') + compact('user');
		$args = array('0');
		$result = $post->comment(compact('data', 'args'));
		$this->assertTrue($result);

		$comment = $post->comments->first();
		$expected = 1;
		$result = $comment['comment_count'];
		$this->assertEqual($expected, $result);

		$expected = 1;
		$result = $post->comments->first()->comments->count();
		$this->assertEqual($expected, $result);

		$comments = $post->comments->first()->comments->data();
		$expected = 'super cool';
		$result = $comments[0]['content'];
		$this->assertEqual($expected, $result);

		$data = array('content' => 'nice') + compact('user');
		$args = array('0');
		$result = $post->comment(compact('data', 'args'));
		$this->assertTrue($result);

		$comment = $post->comments->first();
		$expected = 2;
		$result = $comment['comment_count'];
		$this->assertEqual($expected, $result);

		$expected = 2;
		$result = $post->comments->first()->comments->count();
		$this->assertEqual($expected, $result);

		$comments = $post->comments->first()->comments->data();
		$expected = 'nice';
		$result = $comments[1]['content'];
		$this->assertEqual($expected, $result);

		$expected = 'gwoo';
		$result = $comments[1]['user']['_id'];
		$this->assertEqual($expected, $result);

		$expected = 'gwoo@example.com';
		$result = $comments[1]['user']['email'];
		$this->assertEqual($expected, $result);

		$data = array('content' => 'super nice');
		$args = array('0', '0');
		$result = $post->comment(compact('data', 'args'));
		$this->assertTrue($result);

		$comment = $post->comments->first();
		$expected = 3;
		$result = $comment['comment_count'];
		$this->assertEqual($expected, $result);

		$expected = 2;
		$result = $post->comments->first()->comments->count();
		$this->assertEqual($expected, $result);

		$comments = $post->comments->first()->comments->data();
		$expected = 'super nice';
		$result = $comments[0]['comments'][0]['content'];
		$this->assertEqual($expected, $result);

		$expected = 'pointlessjon';
		$result = $comments[0]['comments'][0]['user']['_id'];
		$this->assertEqual($expected, $result);

		$expected = 'pointlessjon@example.com';
		$result = $comments[0]['comments'][0]['user']['email'];
		$this->assertEqual($expected, $result);
	}

	public function testEndorsePost() {

		$post = Post::create(array(
			'title' => 'another title',
			'content' => 'the content',
			'user_id' => 'albert'
		));

		$author = array(
			'_id' => 'albert', 'email' => 'albert@example.com'
		);

		$this->assertTrue($post->save());

		$expected = 0;
		$this->assertEqual($expected, $post->rating);

		$expected = array();
		$result = $post->endorsements->data();
		$this->assertEqual($expected, $result);

		$authorEndorsed = $post->endorse(compact('author'));
		$this->assertTrue($authorEndorsed);

		$expected = 0;
		$this->assertEqual($expected, $post->rating);

		$expected = array();
		$result = $post->endorsements->data();
		$this->assertEqual($expected, $result);

		$this->assertTrue($post->endorse());

		$expected = 1;
		$this->assertEqual($expected, $post->rating);

		$expected = array('pointlessjon');
		$result = $post->endorsements->data();
		$this->assertEqual($expected, $result);

		$this->assertTrue($post->endorse());

		$expected = 1;
		$this->assertEqual($expected, $post->rating);

		$expected = array('pointlessjon');
		$result = $post->endorsements->data();

		$author = array(
			'_id' => 'gwoo', 'email' => 'gwoo@example.com'
		);
		$endorsed = $post->endorse(compact('author'));
		$this->assertTrue($endorsed);

		$expected = 2;
		$this->assertEqual($expected, $post->rating);

		$expected = array(
			'pointlessjon','gwoo'
		);
		$result = $post->endorsements->data();
		$this->assertEqual($expected, $result);
	}

	public function testEndorseComment() {
		$post = Post::create(array(
			'title' => 'another title',
			'content' => 'the content',
			'user_id' => 'albert'
		));

		$author = array(
			'_id' => 'albert', 'email' => 'albert@example.com'
		);

		$this->assertTrue($post->save());

		$data = array('content' => 'nice one!', 'user' => $author);
		$comment = $post->comment(compact('data'));
		$this->assertTrue($comment);

		$expected = 0;
		$result = $post->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array();
		$result = $post->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);

		$args = array('0');
		$authorEndorsed = $post->endorse(compact('author','args'));
		$this->assertTrue($authorEndorsed);

		$expected = 0;
		$result = $post->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array();
		$result = $post->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);

		$endorse = $post->endorse(compact('args'));
		$this->assertTrue($endorse);

		$expected = 1;
		$result = $post->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array('pointlessjon');
		$result = $post->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);

		$endorsedAgain = $post->endorse(compact('args'));
		$this->assertTrue($endorsedAgain);

		$expected = 1;
		$result = $post->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array('pointlessjon');
		$result = $post->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);

		$author = array(
			'_id' => 'gwoo', 'email' => 'gwoo@example.com'
		);
		$endorsed = $post->endorse(compact('author','args'));
		$this->assertTrue($endorsed);

		$expected = 2;
		$result = $post->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array(
			'pointlessjon','gwoo'
		);
		$result = $post->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);

		$comment = $post->comment(compact('data', 'args'));
		$this->assertTrue($comment);

		$expected = 0;
		$result = $post->comments->first()->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array();
		$result = $post->comments->first()->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);

		$author = array(
			'_id' => 'albert', 'email' => 'albert@example.com'
		);

		$args = array('0','0');
		$authorEndorsed = $post->endorse(compact('args','author'));

		$this->assertTrue($authorEndorsed);

		$expected = 0;
		$result = $post->comments->first()->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array();
		$result = $post->comments->first()->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);

		$endorse = $post->endorse(compact('args'));
		$this->assertTrue($endorse);

		$expected = 1;
		$result = $post->comments->first()->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array('pointlessjon');
		$result = $post->comments->first()->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);

		$endorsedAgain = $post->endorse(compact('args'));
		$this->assertTrue($endorsedAgain);

		$expected = 1;
		$result = $post->comments->first()->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array('pointlessjon');
		$result = $post->comments->first()->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);

		$author = array(
			'_id' => 'gwoo', 'email' => 'gwoo@example.com'
		);
		$endorsed = $post->endorse(compact('author','args'));
		$this->assertTrue($endorsed);

		$expected = 2;
		$result = $post->comments->first()->comments->first()->rating;
		$this->assertEqual($expected, $result);

		$expected = array(
			'pointlessjon','gwoo'
		);
		$result = $post->comments->first()->comments->first()->endorsements->data();
		$this->assertEqual($expected, $result);
	}

	public function testRating() {
		$post = Post::create(array('title' => 'unrated', 'content' => '[REDACTED]'));
	}
}

?>