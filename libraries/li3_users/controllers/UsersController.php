<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_users\controllers;

use li3_users\models\User;
use lithium\security\Auth;
use lithium\storage\Session;

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
		$attempts = (integer) Session::read('attempts');
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
						Session::write('attempts', $attempts);
						return $this->redirect(base64_decode($return));
					}
				} else {
					if (!empty($this->request->data)) {
						$errors = "That didn't seem to work.";
					}
				}
			break;
		}
		Session::write('attempts', $attempts);
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
				Session::write('attempts', 0);
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
}

?>