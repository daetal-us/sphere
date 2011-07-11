<?php

namespace app\tests\mocks\models;

class MockComment extends \app\models\Comment {

	protected static $_classes = array(
		'session' => 'app\tests\mocks\extensions\storage\MockSession'
	);
}

?>