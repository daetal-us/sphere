<?php

namespace app\tests\mocks\extensions\storage;

class MockSession extends \lithium\storage\Session {

	public static function read($key = null, array $options = array()) {
		if ($key == 'user') {
			return array(
				'_id' => 'pointlessjon',
				'email' => 'pointlessjon@example.com'
			);
		}
		return parent::read($key, $options);
	}
}

?>