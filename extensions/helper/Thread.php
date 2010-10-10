<?php

namespace app\extensions\helper;

use \lithium\storage\Session;
use \lithium\net\http\Router;

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
		$gravatar = $this->_context->helper('gravatar');
		$defaults = array('args' => null);
		$options += $defaults;
		$parts = array();

		$user = Session::read('user', array('name' => 'li3_user'));
		$thread->rating();

		foreach ($thread->comments as $key => $comment) {
			$comment->id = $thread->id;
			if (empty($comment->user)) {
				continue;
			}

			$args = array_merge($parent, (array) $key);

			$commentUrl = Router::match(array(
				'controller' => 'posts', 'action' => 'comment', 'id' => $thread->id,
				'args' => $args
			));
			$endorseUrl = Router::match(array(
				'controller' => 'posts', 'action' => 'endorse',
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
					'class' => 'button post-comment-reply' . ((empty($user)) ? ' inactive' : ''),
					'title' => 'reply to this comment',
					'escape' => false
				)
			);
			$endorsement = $html->link(
				'<span>endorse</span>',
				$endorseUrl,
				array(
					'class' => 'button endorse-post-comment',
					'title' => 'endorse this comment',
					'escape' => false
				)
			);

			$comment->content = '<pre class="markdown">' .
				$oembed->classify($comment->content, array('markdown' => true)) .
			'</pre>';

			$replies = null;
			if (!empty($comment->comment_count)) {
				$replies = $html->link(
					"view replies ({$comment->comment_count})",
					'#',
					array('class' => 'view-post-comment-replies')
				);
			}

			$timestamp = $comment->created;
			$date = date("F j, Y, g:i a T", $timestamp);
			$time = "<span class=\"post-comment-created pretty-date\" title=\"{$date}\">{$date}" .
						"<span class=\"timestamp\">{$timestamp}</span></span>";

			$size = $this->threadedIconSize(count($args));
			$style = 'style="padding:' . $size . 'px 0 0 0; width:' . $size . 'px; background-image:url('.$gravatar->url(array(
				'email' => $comment->user->email, 'params' => compact('size')
			)).');"';
			$author = "<span $style title=" . $comment->user->username . "></span>";

			$ratingClass = ($comment->rating == 0 ? ' empty' : null);
			$rating = '<span class="post-comment-rating' . $ratingClass .'">' .
				$comment->rating .
			'</span>';

			$meta = 	$time . $rating;

			$row = 	"<div class=\"meta aside\"><aside>{$rating}<aside></div> {$endorsement} {$reply}" .
						"<div class=\"post-comment-author-icon\">{$author} </div>" .
						"<div class=\"post-comment-author-content\" style=\"padding-left:" . $size . "px;\"><div class=\"post-comment-author\">" .
						$html->link($comment->user->username, array('controller' => 'search', 'action' => 'index', 'args' => '?q=author:'.$comment->user->username), array('title' => 'Search for more posts by this author')) .
						" {$time}</div>" .
						"<div class=\"post-comment-content\">{$comment->content}</div></div> {$replies}";

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

	protected function threadedIconSize($factor) {
		$result = 64;
		if ($factor > 1) {
			$result = floor($result * (1.61803399 / $factor));
		}
		return ($result > 16) ? $result : 16;
	}
}
?>