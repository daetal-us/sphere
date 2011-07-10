<?php

namespace app\tests\cases\extensions\helper;

use \app\extensions\helper\Thread;
use \lithium\data\collection\Document;
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
			'_id' => 1,
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
				)
			)
		);

		$document = new Document(array('items' => $data, 'model' => '\app\models\Post'));
		$result = $thread->comments($document);
		$this->assertNull($result);

		$data->comments[] = (object) array(
			'content' => 'two',
			'user' => (object) array(
				'_id' => 'user@example.com',
				'_id' => 'user name',
				'email' => 'user@example.com'
			),
			'comments' => array(
				(object) array(
					'content' => 'two-one',
					'comments' => array(
						(object) array('content' => 'two-one-one')
					)
				)
			)
		);

		$document = new Document(array('items' => $data, 'model' => '\app\models\Post'));
		$result = $thread->comments($document);
		$this->assertTrue(!empty($result));
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

	public function render($template, $data = array(), array $options = array()) {
	}
}
?>