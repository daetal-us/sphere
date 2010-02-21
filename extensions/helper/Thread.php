<?php

namespace app\extensions\helper;


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
			$form->submit('save'),
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

		foreach ($thread->comments as $key => $comment) {
			$comment->id = $thread->id;
			$args = array_merge($parent, (array) $key);

			$reply = $html->link('reply', array(
				'action' => 'comment', 'args' => array_merge(array($thread->id), $args)
			), array('class' => 'post-comment-reply', 'title' => 'reply to this comment'));
			$comment->content = $oembed->classify($comment->content);

			$style =	'style="background-image:url(http://gravatar.com/avatar/' .
						$comment->user->email . '?s=16);"';
			$name = $comment->user->username;
			$date = date("F j, Y, g:i a T", strtotime($comment->created));
			$author = "<b>{$name}</b> said";
			$author = "<span class=\"post-comment-author\" title=\"{$date}\" $style>{$author}</span>";

			$row = "{$reply} {$author} <div class=\"post-comment-content\">{$comment->content}</div>";

			if (isset($options['args']) && $options['args'] == $args) {
				$next = (!empty($comment->comments) ? count($comment->comments) : 0);
				$row .= $this->form(array_merge($args, array($next)));
			}
			$row .= $this->comments($comment, $options, $args);
			$parts[] = "<li class=\"comment\">{$row}</li>";
		}
		if (empty($parts)) {
			return null;
		}
		$list = join("", $parts);
		return "<ul class=\"comments\">{$list}</ul>";
	}
}
?>