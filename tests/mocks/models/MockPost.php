<?php

namespace app\tests\mocks\models;

class MockPost extends \app\models\Post {

	protected $_meta = array(
		'connection' => 'test', 'source' => 'test_posts'
	);
}

?>