<?php

namespace app\extensions\helper;

use lithium\storage\Session;
use lithium\net\http\Router;
use markdown\Markdown;

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

	public function comments($data, $options = array(), $parent = array()) {
		if (!isset($data['comments'])) {
			return null;
		}

		$defaults = array('args' => null);
		$options += $defaults;

		$comments = $data['comments'];
		$html = $this->_context->helper('html');
		$oembed = $this->_context->helper('oembed');
		$gravatar = $this->_context->helper('gravatar');

		$parts = array();

		$user = Session::read('user', array('name' => 'li3_user'));

		foreach ($comments as $key => $comment) {
			$comment = (array) $comment;

			$comment['user'] = (array) $comment['user'];
			$comment['_id'] = $data['_id'];
			if (empty($comment['user'])) {
				continue;
			}

			$args = array_merge($parent, (array) $key);

			$commentUrl = Router::match(array(
				'controller' => 'posts', 'action' => 'comment', '_id' => $comment['_id'],
				'args' => $args
			));
			$endorseUrl = Router::match(array(
				'controller' => 'posts', 'action' => 'endorse',
				'args' => array_merge(array($comment['_id']), $args)
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

			if (!empty($user)) {
				$author = $comment['user']['_id'] == $user['_id'];
				$endorsed = !empty($comment['endorsements'])
					&& in_array($user['_id'], $comment['endorsements']);

				if ($author || $endorsed) {
					$endorsement = null;
				}
			}

			$comment['content'] = $oembed->classify(
				$html->escape($comment['content']), array('markdown' => true)
			);

			$comment['content'] = Markdown::parse($comment['content']);

			$replies = null;
			if (!empty($comment['comment_count'])) {
				$count = count($comment['comments']);
				if ($comment['comment_count'] > $count) {
					$count .= "+";
				}
				$replies = $html->link(
					"view replies ({$count})",
					'#',
					array('class' => 'view-post-comment-replies')
				);
			}

			$timestamp = $comment['created'];
			$date = date("F j, Y, g:i a T", $timestamp);
			$time = "<span class=\"post-comment-created pretty-date\" title=\"{$date}\">";
			$time .= "{$date}<span class=\"timestamp\">{$timestamp}</span></span>";

			$size = $this->threadedIconSize(count($args));
			$avatar = $gravatar->url(array(
				'email' => $comment['user']['email'], 'params' => compact('size')
			));
			$style = 'style="padding:' . $size . 'px 0 0 0; width:' . $size . 'px; ';
			$style .= 'background-image:url(' . $avatar . ');"';
			$author = "<span {$style} title=" . $html->escape($comment['user']['_id']) . "></span>";

			$class = ($comment['rating'] == 0 ? ' empty' : null);
			$rating = '<span class="post-comment-rating' . $class . '">';
			$rating .= $comment['rating'] . '</span>';

			if ($comment['rating']) {
				$rating = "<div class=\"meta aside\"><aside>{$rating}<aside></div>";
			} else {
				$rating = null;
			}

			$row = "{$rating} {$reply} {$endorsement}" ;
			$row .= "<div class=\"post-comment-author-icon\">{$author} </div>";
			$row .= "<div class=\"post-comment-author-content\">";
			$row .= "<div class=\"post-comment-author\">";
			$row .= $html->link(
				$comment['user']['_id'],
				array(
					'controller' => 'search',
					'action' => 'filter',
					'_id' => $comment['user']['_id']
				), array(
					'title' => 'Search for more posts by this author'
				)
			);
			$row .= " {$time}</div>";
			$row .= "<div class=\"post-comment-content\">{$comment['content']}</div>";
			$row .= "</div> {$replies}";
			$row .= $this->comments($comment, $options, $args);

			$_id = implode('-', $args);
			$parts[] = "<li class=\"comment\" id=\"comment-{$_id}\">{$row}</li>";
		}
		if (empty($parts)) {
			return null;
		}
		$list = join("", $parts);
		return "<ul class=\"comments\">{$list}</ul>";
	}

	protected function threadedIconSize($factor) {
		$result = 32;
		if ($factor > 1) {
			$result = floor($result * (1.61803399 / $factor));
		}
		return ($result > 16) ? $result : 16;
	}
}

?>