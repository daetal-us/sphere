<?php

namespace app\controllers;

use \app\models\User;
use \lithium\security\Auth;
use \lithium\storage\Session;

class UsersController extends \lithium\action\Controller {

	public function index() {
		$users = User::all();
		return compact('users');
	}

	public function login() {
		if (!empty($this->request->data)) {
			$user = Auth::check('user', $this->request);
			return compact('user');
		}
	}

	public function logout() {
		Auth::clear('user');
		$this->redirect(array('action' => 'login'));
	}

	public function view($id = null) {
		$user = User::find($id);
		return compact('user');
	}

	public function register() {
		if (!empty($this->request->data)) {
			$user = User::create($this->request->data);
			if ($user->save()) {
				Auth::set('user', $user);
				$this->redirect(array(
					'controller' => 'users', 'action' => 'view',
					'args' => array($user->id)
				));
			}
		}
		if (empty($user)) {
			$user = User::create();
		}
		return compact('user');
	}

	public function edit($id = null) {
		$user = User::find($id);
		if (empty($user)) {
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($user->save($this->request->data)) {
				$this->redirect(array(
					'controller' => 'users', 'action' => 'view',
					'args' => array($user->id)
				));
			}
		}
		return compact('user');
	}
}

?>