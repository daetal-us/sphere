<?php

namespace app\controllers;

use app\models\Post;
use lithium\storage\Session;

class PostsController extends \lithium\action\Controller {

	public function add() {
		if (!$user = Session::read('user', array('name' => 'li3_user'))) {
			$this->redirect(array(
				'controller' => 'users', 'action' => 'login'
			));
		}
		$errors = false;
		if (!empty($this->request->data)) {
			$post = Post::create($this->request->data);
			if ($post->save()) {
				return $this->redirect(array(
					'controller' => 'posts', 'action' => 'comment',
					'_id' => $post->_id
				));
			} else {
				$errors = $post->errors();
			}
		}
		if (empty($post)) {
			$post = Post::create();
		}
		$tags = Post::$tags;
		return compact('post','tags','errors');
	}

	public function comment() {
		$user = Session::read('user', array('name' => 'li3_user'));

		$post = Post::first($this->request->params['_id']);
		if (empty($post)) {
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
		}
		//print_r($post->data()); die();

		$endorsed =
			($user['_id'] == $post->user_id) ||
			(!empty($post->endorsements) && in_array($user['_id'], $post->endorsements->data()));

		if (!empty($this->request->data)) {
			if (!$user) {
				return $this->redirect(array(
					'controller' => 'users', 'action' => 'login'
				));
			}

			$data = $this->request->data;
			$args = $this->request->args;

			if ($post->comment(compact('data', 'args'))) {
				return $this->redirect(array(
					'controller' => 'posts', 'action' => 'comment', '_id' => $post->_id
				));
			}
		}
		return compact('post', 'user', 'endorsed');
	}

	public function endorse($_id = null) {
		$user = Session::read('user', array('name' => 'li3_user'));
		if (!$user) {
			return $this->redirect(array(
				'controller' => 'users', 'action' => 'login'
			));
		}

		$post = Post::first($_id);
		if (empty($post)) {
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
		}

		$args = $this->request->args;
		$endorsement = $post->endorse(compact('args'));

		$this->redirect(array(
			'controller' => 'posts', 'action' => 'comment', '_id' => $_id
		));
	}

	public function edit($_id = null) {
		$post = Post::find($_id);
		if (empty($post)) {
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($post->save($this->request->data)) {
				$this->redirect(array(
					'controller' => 'posts', 'action' => 'view',
					'args' => array($post->_id)
				));
			}
		}
		return compact('post');
	}
}

?>