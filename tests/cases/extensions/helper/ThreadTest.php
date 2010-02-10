<?php

namespace app\tests\cases\extensions\helper;

use \app\extensions\helper\Thread;
use \lithium\net\http\Router;

class ThreadTest extends \lithium\test\Unit {

	public function setUp() {}

	public function tearDown() {}

	public function testForm() {}

	public function testComments() {
		$thread = new Thread(array('context' => new MockThreadRenderer()));

		$data = (object) array(
			'id' => 1,
			'comments' => array(
				(object) array(
					'content' => 'one',
					'comments' => array(
						(object) array(
							'content' => 'one-two',
							'comments' => array(
								(object) array('content' => 'one-two-three')
							)
						)
					)
				),
				(object) array(
					'content' => 'two',
					'comments' => array(
						(object) array(
							'content' => 'two-two',
							'comments' => array(
								(object) array('content' => 'two-two-three')
							)
						)
					)
				)
			)
		);
		$expected = '<ul><li>one : <a href="">comment</a>'
			. '<ul><li>one-two : <a href="">comment</a>'
			. '<ul><li>one-two-three : <a href="">comment</a></li></ul>'
			. '</li></ul></li>'
			. '<li>two : <a href="">comment</a>'
			. '<form action="/lithium_universe" method="POST">'
			. "\n"
			. '<textarea name="comments[1][comments][0][comments][1][content]"></textarea>'
			. "\n"
			. '<input type="submit" value="save" />'
			. "\n"
			. '</form>'
			. '<ul><li>two-two : <a href="/lithium_universe/posts/comment/1/0/1/0">comment</a>'
			. '<ul><li>two-two-three : <a href="/lithium_universe/posts/comment/1/0/1/0/0">comment</a>'
			. '</li></ul></li></ul></li></ul>';
		$result = $thread->comments($data, array('args' => array('0' => 0, '1' => 1)));
		$this->assertEqual($expected, $result);
	}
}
class MockThreadRenderer extends \lithium\template\view\Renderer {

	public function request() {
		if (empty($this->_request)) {
			$this->_request = new \lithium\action\Request();
			$this->_request->params += array(
				'controller' => 'posts', 'action' => 'comment', 123123123
			);
		}
		return $this->_request;
	}

	public function render($template, $data = array(), $options = array()) {
	}
}
?>