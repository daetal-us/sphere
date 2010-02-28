<?php

namespace app\extensions\helper;

use \lithium\storage\Session;

class Thread extends \lithium\template\Helper {

	public function form($key, $options = array()) {
		$form = $this->_context->helper('form');
		$children = null;
		if (is_array($key)) {
			$next = array_shift($key);

			foreach ($key as $k) {
				$children .= "[comments][{$k}]";
			}
		}

		return join("\n", array(
			$form->create(),
			$form->textarea("comments[{$next}]{$children}[content]"),
			$form->submit('post comment'),
			$form->end()
		));
	}

	public function comments($thread, $options = array(), $parent = array()) {
		if (empty($thread->comments)) {
			return null;
		}
		$html = $this->_context->helper('html');
		$oembed = $this->_context->helper('oembed');
		$defaults = array('args' => null);
		$options += $defaults;
		$parts = array();

		$user = Session::read('user');

		foreach ($thread->comments as $key => $comment) {
			$comment->id = $thread->id;
			$args = array_merge($parent, (array) $key);

			$commentUrl = \lithium\net\http\Router::match(array(
				'controller' => 'posts',
				'action' => 'comment', 'args' => array_merge(array($thread->id), $args)
			));
			if (empty($user)) {
				$commentUrl = array(
					'controller' => 'users',
					'action' => 'login',
					'return' => base64_encode($commentUrl)
				);
			}
			$reply = $html->link(
				'<span>reply</span>',
				$commentUrl,
				array(
					'class' => 'post-comment-reply',
					'title' => 'reply to this comment',
					'escape' => false
				)
			);

			$comment->content = 	'<pre class="markdown">' .
										$oembed->classify($comment->content, array('markdown' => true)) .
										'</pre>';

			$style =	'style="background-image:url(http://gravatar.com/avatar/' .
						$comment->user->email . '?s=16);"';
			$name = $comment->user->username;

			$timestamp = strtotime($comment->created);
			$date = date("F j, Y, g:i a T", $timestamp);
			$time = "<span class=\"post-comment-created pretty-date\" title=\"{$date}\">{$date}" .
						"<span class=\"timestamp\">{$timestamp}</span></span>";

			$author = "<b>{$name}</b>";
			$author = "<span class=\"post-comment-author\" $style>{$author}</span>";

			$meta = $author . $time;

			$row = 	"<div class=\"meta aside\"><aside>{$meta}<aside></div> {$reply}" .
						"<div class=\"post-comment-content\">{$comment->content}</div>";

			// if (isset($options['args']) && $options['args'] == $args) {
			// 	$next = (!empty($comment->comments) ? count($comment->comments) : 0);
			// 	$row .= $this->form(array_merge($args, array($next)));
			// }
			$row .= $this->comments($comment, $options, $args);
			$id = implode('-', $args);
			$parts[] = "<li class=\"comment\" id=\"comment-{$id}\">{$row}</li>";
		}
		if (empty($parts)) {
			return null;
		}
		$list = join("", $parts);
		return "<ul class=\"comments\">{$list}</ul>";
	}
}
?>