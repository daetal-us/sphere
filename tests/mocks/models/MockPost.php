<?php

namespace app\tests\mocks\models;

class MockPost extends \app\models\Post {

	protected $_meta = array(
		'connection' => 'test',
		'source' => 'test_posts'
	);

	protected static $_classes = array(
		'user'    => 'app\tests\mocks\models\MockUser',
		'session' => 'app\tests\mocks\extensions\storage\MockSession',
		'comment' => 'app\tests\mocks\models\MockComment'
	);
}

?>