<?php
/**
 * Lithium Sphere: communized sphere of influence
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://www.opensource.org/licenses/MIT The MIT License
 */

namespace li3_users\controllers;

use li3_users\models\User;
use lithium\security\Auth;
use lithium\storage\Session;
use li3_swiftmailer\mailer\Transports;
use li3_swiftmailer\mailer\Message;

class UsersController extends \lithium\action\Controller {

	/**
	 * Max number of login attempts before cooldown
	 */
	protected $_cooldown = 6;

	public function index() {
		$users = User::all();
		return compact('users');
	}

	public function login() {
		$return = null;
		if (!empty($this->request->params['return'])) {
			$return = $this->request->params['return'];
		}
		$errors = $disabled = false;
		$attempts = (integer) Session::read('attempts', array('name' => 'cooldown'));
		if (!empty($this->request->data)) {
			$attempts++;
		}
		switch (true) {
			case $attempts == $this->_cooldown:
				$attempts = strtotime('+10 minutes');
			case $attempts > $this->_cooldown && $attempts > time():
				$disabled = true;
				$errors = "You've failed too many times. Try again later.";
			break;
			case $attempts > $this->_cooldown && $attempts <= time():
				$attempts = 0;
			default:
				$user = Auth::check('user', $this->request);
				if (!empty($user)) {
					$attempts = 0;
					if (!empty($return)) {
						Session::write('attempts', $attempts, array('name' => 'cooldown'));
						return $this->redirect(base64_decode($return));
					}
				} else {
					if (!empty($this->request->data)) {
						$errors = "That didn't seem to work.";
					}
				}
			break;
		}
		Session::write('attempts', $attempts, array('name' => 'cooldown'));
		return compact('user', 'return', 'errors', 'disabled');
	}

	public function logout() {
		Auth::clear('user');
		$this->redirect(array('action' => 'login'));
	}

	public function view($_id = null) {
		$user = User::find($_id);
		return compact('user');
	}

	public function register() {
		$errors = false;
		if (!empty($this->request->data)) {
			$user = User::create($this->request->data);
			if ($user->save()) {
				Auth::set('user', $user->to('array'));
				Session::write('attempts', 0, array('name' => 'cooldown'));
				$this->redirect(array(
					'controller' => 'users', 'action' => 'view',
					'args' => array($user->_id)
				));
			} else {
				$errors = $user->errors();
			}
		}
		if (empty($user)) {
			$user = User::create();
		}
		return compact('user', 'errors');
	}

	public function edit($_id = null) {
		$user = User::find($_id);
		if (empty($user)) {
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($user->save($this->request->data)) {
				$this->redirect(array(
					'controller' => 'users', 'action' => 'view',
					'args' => array($user->_id)
				));
			}
		}
		return compact('user');
	}

	public function reset($token = null) {
		$success = $resetting = $errors = $emailed = false;
		Session::write('reset_attempts', 0, array('name' => 'cooldown'));
		$user = Session::read('user', array('name' => 'li3_user'));

		$attempts = (integer) Session::read('reset_attempts', array('name' => 'cooldown'));
		$cooldown = $this->_cooldown < $attempts;

		$title = 'forgot your password?';

		if (!empty($user)) {
			$token = $user->token();
		}

		if (!$cooldown && !empty($token)) {
			$resetting = true;
			$title = "update your password";
			if (!empty($this->request->data['_id']) && !empty($this->request->data['password'])) {
				$_id = $this->request->data['_id'];
				if (User::reset(compact('token','_id'))) {
					$user = User::first($this->request->data['_id']);
					$user->set(array('password' => $this->request->data['password']));
					$success = $user->save();
					$title = 'password updated!';
					if (!$success) {
						$title = 'almost there...';
						$errors = $user->errors();
					}
					Auth::check('user', $this->request);
					Session::write('reset_attempts', 0, array('name' => 'cooldown'));
				} else {
					$errors = array(
						'token' => array('Your token has expired.')
					);
				}
				$attempts++;
				Session::write('reset_attempts', $attempts, array('name' => 'cooldown'));
			}
		}

		if (empty($token) && !empty($this->request->data['_id'])) {
			if ($user = User::first($this->request->data['_id'])) {
				$user->token();
				if ($this->_emailToken($user->data())) {
					$emailed = true;
				} else {
					$errors = array(
						'email' => array("Hm. It appears we couldn't email you.")
					);
				}
			} else {
				$errors = array(
					'_id' => array('I think you typed that username incorrectly.')
				);
			}
		}

		return compact('success','resetting','errors','title','user','cooldown', 'emailed');
	}

	protected function _emailToken($data = array()) {
		if (!empty($data)) {
			$body = implode(array(
				"you have requested to reset your password for lithium sphere.",
				"the following url is just what you need:",
				"\t" . \lithium\net\http\Router::match(array(
					'controller' => 'users', 'action' => 'reset'
				)) . "/{$data['token']}",
				'this email will self destruct in 10 minutes and counting...'
			), "\n\n");

			$mailer = Transports::adapter('default');
			$message = Message::newInstance()
				->setSubject(' : reset your password : ')
				->setFrom(array('lithified@lithify.me' => 'lithium sphere'))
				->setTo($data['email'])
				->setBody($body);
			return $mailer->send($message);
		}
		return false;
	}
}

?>