<?php

namespace app\tests\cases\extensions\helper;

use \app\extensions\helper\Thread;
use \lithium\net\http\Router;

class ThreadTest extends \lithium\test\Unit {

	public function setUp() {
		$this->_context = new MockThreadRenderer();
		$this->_base = $this->_context->request()->env('base');
	}

	public function tearDown() {}

	public function testForm() {}

	public function testComments() {

		$thread = new Thread(array('context' => $this->_context));

		$data = (object) array(
			'id' => 1,
			'comments' => array(
				(object) array(
					'content' => 'one',
					'comments' => array(
						(object) array(
							'content' => 'one-one',
							'comments' => array(
								(object) array('content' => 'one-one-one')
							)
						)
					)
				),
				(object) array(
					'content' => 'two',
					'comments' => array(
						(object) array(
							'content' => 'two-one',
							'comments' => array(
								(object) array('content' => 'two-one-one')
							)
						)
					)
				)
			)
		);
		$expected = '<ul><li>one : <a href="' . $this->_base . '/posts/comment/1/0">reply</a>'
			. '<ul><li>one-one : <a href="' . $this->_base . '/posts/comment/1/0/0">reply</a>'
			. '<ul><li>one-one-one : <a href="' . $this->_base . '/posts/comment/1/0/0/0">reply</a></li></ul>'
			. '</li></ul></li>'
			. '<li>two : <a href="' . $this->_base . '/posts/comment/1/1">reply</a>'
			. '<form action="' . $this->_base . '" method="POST">'
			. "\n"
			. '<textarea name="comments[1][comments][1][content]"></textarea>'
			. "\n"
			. '<input type="submit" value="save" />'
			. "\n"
			. '</form>'
			. '<ul><li>two-one : <a href="' . $this->_base . '/posts/comment/1/1/0">reply</a>'
			. '<ul><li>two-one-one : <a href="' . $this->_base . '/posts/comment/1/1/0/0">reply</a>'
			. '</li></ul></li></ul></li></ul>';
		$result = $thread->comments($data, array('args' => array('0' => 1)));
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