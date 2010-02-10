<?php

namespace app\controllers;

use \app\models\Post;
use \lithium\util\Set;

class PostsController extends \lithium\action\Controller {

	public function index() {
		$posts = Post::all(array(
			'conditions' => array('design' => 'all', 'view' => 'latest')
		));
		return compact('posts');
	}

	public function view($id = null) {
		$post = Post::find($id);
		return compact('post');
	}

	public function add() {
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

	public function comment($id = null) {
		$post = Post::find($id);
		if (empty($post)) {
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			$data = $this->request->data;
			if (!empty($this->request->data['comments']) && $post->comments) {
				$data['comments'] = Set::merge(
					Set::reverse($post->comments->data()), $this->request->data['comments']
				);
			}
			if ($post->save($data)) {
				$this->redirect(array(
					'controller' => 'posts', 'action' => 'comment',
					'args' => array($post->id)
				));
			}
		}
		return compact('post');
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