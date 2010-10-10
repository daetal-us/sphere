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
		extract($options);

		$html = $this->_context->helper('html');
		$gravatar = $this->_context->helper('gravatar');

		$post->rating();

		$ratingClass = ($post->rating == 0 ? ' empty' : null);
		$rating = '<span class="post-rating' . $ratingClass .'">' .
			'<span>' . $post->rating . '</span>' .
		'</span>';
		$image = $html->image($gravatar->url($post->user()->email), array('class' => 'gravatar'));

		$image = $html->link($image, array(
			'controller' => 'posts', 'action' => 'comment', 'id' => $post->id
		), array('escape' => false));

		$heading = '<h2>' . $html->link($post->title, array(
			'controller' => 'posts', 'action' => 'comment', 'id' => $post->id
		)) . '</h2>';

		$author = $html->link($post->user()->username, array(
			'controller' => 'search',
			'action' => 'index',
			'args' => '?q=author:'.$post->user()->username
		), array(
			'class' => 'post-author',
			'title' => 'Search for more posts by this author',
			'style' => 'background-image:url(' . $gravatar->url(array(
				'email' => $post->user()->email, 'params' => array('size' => 16)
			)) . ')'
		));

		$timestamp = $post->created;
		$date = $date = date("F j, Y, g:i a T", $timestamp);
		$time = "<span class=\"post-created pretty-date\" title=\"{$date}\">{$date}" .
					"<span class=\"timestamp\">{$timestamp}</span></span>";

		$count = (empty($post->comment_count) ? 0 : $post->comment_count);
		$commentsClass = ($count > 0) ? (($count > 1) ? 'many' : 'one') : 'none';
		$commentsText = (($count < 1) ? 'no' : $count)
			. ' comment' . (($count !== 1) ? 's' : '');
		$comments = $html->link($commentsText,
			array(
				'controller' => 'posts', 'action' => 'comment', 'id' => $post->id,
			),
			array('class' => 'comments ' . $commentsClass)
		);
		return '<li class="post">' . $image . $rating . $heading . $comments . $author . $time . '</li>';
	}
}

?>