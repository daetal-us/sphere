<?php

namespace app\controllers;

use \app\models\Post;
use \lithium\util\Set;
use \lithium\storage\Session;

class PostsController extends \lithium\action\Controller {

	public function index() {
		$posts = Post::all(array(
			'conditions' => array('design' => 'all', 'view' => 'posts'),
			'order' => array('descending' => 'true')
		));
		return compact('posts');
	}

	public function add() {
		if (!$user = Session::read('user')) {
			$this->redirect(array(
				'controller' => 'users', 'action' => 'login'
			));
		}
		if (!empty($this->request->data)) {
			$post = Post::create($this->request->data);
			if ($post->save()) {
				return $this->redirect(array(
					'controller' => 'posts', 'action' => 'index',
					//'args' => array($post->id)
				));
			}
		}
		if (empty($post)) {
			$post = Post::create();
		}
		return compact('post');
	}

	public function comment() {
		$post = Post::find($this->request->id);
		if (empty($post)) {
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
		}
		$user = Session::read('user');

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
					'controller' => 'posts', 'action' => 'comment', 'id' => $post->id
				));
			}
		}
		return compact('post', 'user');
	}

	public function endorse($id = null) {
		$post = Post::find($id);
		if (empty($post)) {
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
		}
		$author = Session::read('user');
		$args = $this->request->args;
		$endorsement = Post::endorse($id, compact('post', 'author', 'args'));
		$this->redirect(array(
			'controller' => 'posts', 'action' => 'comment', 'id' => $id
		));
	}

	public function edit($id = null) {
		$post = Post::find($id);
		if (empty($post)) {
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($post->save($this->request->data)) {
				$this->redirect(array(
					'controller' => 'posts', 'action' => 'view',
					'args' => array($post->id)
				));
			}
		}
		return compact('post');
	}
}

?>