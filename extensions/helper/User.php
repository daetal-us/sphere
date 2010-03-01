<?php

namespace app\extensions\helper;

use \lithium\util\String;
use \lithium\storage\Session;

class User extends \lithium\template\Helper {

	protected $_greetings = array(
		'nice, {:username}. you logged in.',
		'welcome back, {:username}.',
		'hi, {:username}.',
		'hello, {:username}.',
		'what\'s up, {:username}?',
		'how\'s it going, {:username}?',
		'what\'s happening, {:username}?',
		'we missed you, {:username}.',
		'there you are, {:username}.',
		'the sphere has you, {:username}.',
		'it\'s good to see you again, {:username}.',
		'...you\'ve been here before, {:username}.',
		'enjoy your stay, {:username}.',
		'thanks for that, {:username}.',
		'{:username}, you\'re good at this.'
	);

	public function greeting($user = array()) {
		$greeting = null;
		if (!empty($user)) {
			extract($user);
			$greeting = String::insert(
				$this->_greetings[array_rand($this->_greetings)], compact('username')
			);
			if (rand(0,100) == 47) {
				$greeting = base64_decode(
					'UEMgTE9BRCBMRVRURVIgPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTouMzVlbTsgY29sb3I6I2'
					. 'U2ZTZlNjsiPih5b3UncmUgbG9nZ2VkIGluKTwvc3Bhbj4='
				);
			}
		}
		return $greeting;
	}
	
	public function session() {
		return Session::read('user');
	}

}

?>