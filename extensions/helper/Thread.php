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
				'action' => 'comment',
				'args' => array_merge(array($thread->id), $args)
			));
			$endorseUrl = \lithium\net\http\Router::match(array(
				'controller' => 'posts',
				'action' => 'endorse',
				'args' => array_merge(array($thread->id), $args)
			));
			if (empty($user)) {
				$commentUrl = array(
					'controller' => 'users',
					'action' => 'login',
					'return' => base64_encode($commentUrl)
				);
				$endorsUrl = array(
					'controller' => 'users',
					'action' => 'login',
					'return' => base64_encode($endorseUrl)
				);
			}
			$reply = $html->link(
				'<span>reply</span>',
				$commentUrl,
				array(
					'class' => 'post-comment-reply' . ((empty($user)) ? ' inactive' : ''),
					'title' => 'reply to this comment',
					'escape' => false
				)
			);
			$endorsement = $html->link(
				'<span>endorse</span>',
				$endorseUrl,
				array(
					'class' => 'endorse-post-comment',
					'title' => 'endorse this comment',
					'escape' => false
				)
			);

			$comment->content = 	'<pre class="markdown">' .
										$oembed->classify($comment->content, array('markdown' => true)) .
										'</pre>';

			$replies = null;
			if (empty($parent) && !empty($comment->comment_count)) {
				$replies = $html->link(
					"view replies ({$comment->comment_count})",
					'#',
					array('class' => 'view-post-comment-replies')
				);
			}

			$style =	'style="background-image:url(http://gravatar.com/avatar/' .
						$comment->user->email . '?s=16);"';
			$name = $comment->user->username;

			$timestamp = strtotime($comment->created);
			$date = date("F j, Y, g:i a T", $timestamp);
			$time = "<span class=\"post-comment-created pretty-date\" title=\"{$date}\">{$date}" .
						"<span class=\"timestamp\">{$timestamp}</span></span>";

			$author = "<b>{$name}</b>";
			$author = "<span class=\"post-comment-author\" $style>{$author}</span>";

			if (empty($comment->rating)) {
				$comment->rating = 0;
			}
			$rating = 	"<span class=\"post-comment-rating " .
							((empty($comment->rating) ? 'empty' : '' )) .
							"\">{$comment->rating}</span>";

			$meta = 	'<span class="post-comment-source">posted ' . $time . ' by ' . $author .
						'</span>' . $rating;

			$row = 	"<div class=\"meta aside\"><aside>{$meta}<aside></div> {$reply} {$endorsement}" .
						"<div class=\"post-comment-content\">{$comment->content}</div> {$replies}";

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