<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_users\extensions\helper;

use \lithium\util\String;
use \lithium\storage\Session;

class User extends \lithium\template\Helper {

	protected $_greetings = array(
		'much success!',
		'you\'re logged in',
		'you\'re good to go',
		'welcome back, {:_id}',
		'hi, {:_id}',
		'hello, {:_id}',
		'what\'s up, {:_id}?',
		'how\'s it going?',
		'what\'s happening?',
		'we missed you, {:_id}',
		'there you are, {:_id}',
		'knock, knock, {:_id}',
		'good to see you again, {:_id}',
		'...you\'ve been here before',
		'PC LOAD LETTER',
		'enjoy your stay',
		'ciao',
		'مرحبا',
		'你好',
		'halo',
		'bonjour',
		'Γεια σας',
		'שלום',
		'नमस्ते',
		'halló',
		'こんにちは',
		'안녕하세요',
		'witam',
		'olá',
		'buna ziua',
		'привет',
		'hola',
		'hej',
		'Здраво',
		'dobrý deň',
		'merhaba',
		'привіт',
		'אַ גוט יאָר'
	);

	public function greeting($user = array()) {
		$greeting = null;
		if (!empty($user)) {
			extract($user);
			$greeting = String::insert(
				$this->_greetings[array_rand($this->_greetings)], compact('_id')
			);
		}
		return $greeting . ' <span class="sub">(you\'re logged in)</span>';
	}

	public function session() {
		return Session::read('user', array('name' => 'li3_user'));
	}

}

?>