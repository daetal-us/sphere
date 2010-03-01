<?php

namespace app\extensions\helper;

/**
 * This helper provides tools to uniformly render posts across views.
 */
class Post extends \lithium\template\Helper {

	public function rows($posts = null, $options = array()) {
		if (empty($posts)) {
			return null;
		}
		$defaults = array(
			'class' => 'posts',
			'gravatar' => array('size' => '64', 'link' => false)
		);
		$options += $defaults;
		extract($options);

		$content = null;
		foreach ($posts as $post) {
			$content .= $this->row($post);
		}

		$out = '<ul class="' . $class . '">' . $content . '</ul>';
		return $out;
	}

	public function row($post, $options = array()) {
		$defaults = array(
			'gravatar' => array('size' => '64', 'link' => false)
		);
		$options += $defaults;
		extract($options);
		$html = $this->_context->helper('html');

		$image = $html->image(
			'http://gravatar.com/avatar/'.md5($post->user->email).'?s='.$gravatar['size'],
			array('class' => 'gravatar')
		);
		if ($gravatar['link']) {
			$image = $html->link($img, $gravatar['link'], array('escape' => false));
		}

		$heading = '<h2>' . $html->link($post->title, array(
			'controller' => 'posts', 'action' => 'comment',
			'args' => array($post->id)
		)) . '</h2>';

		$author = 	'<span class="post-author">submitted by'
			. '<b>' . $post->user->username . '</b>'
			. '</span>';

		$count = (empty($post->comment_count) ? 0 : $post->comment_count);
		$commentsClass = ($count > 0) ? (($count > 1) ? 'many' : 'one') : 'none';
		$commentsText = (($count < 1) ? 'no' : $count)
			. ' comment' . (($count !== 1) ? 's' : '');
		$comments = $html->link(
			$commentsText,
			array(
				'controller' => 'posts', 'action' => 'comment',
				'args' => array($post->id),
			),
			array('class' => 'comments ' . $commentsClass)
		);
		return '<li class="post">' . $image . $heading . $author . $comments .'</li>';
	}
}

?>