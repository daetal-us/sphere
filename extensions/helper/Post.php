<?php

namespace app\extensions\helper;

use \lithium\net\http\Router;
use \lithium\util\String;

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

		foreach ($post->tags as $tag) {
			if (!in_array($tag, \app\models\Post::$tags)) {
				$tag = '';
				continue;
			}
			$tag = $this->tag($tag, array('class' => "icon tag {$tag}"));
			break;
		}

		$content = implode('', compact('image','rating','heading','tag','comments','author','time'));

		return '<li class="post">' . $content . '</li>';
	}

	/**
	 * Generate a link to tags
	 *
	 * @param string $tag
	 * @return string
	 */
	public function tag($tag, $options = array()) {
		$defaults = array(
			'text' => $tag,
			'class' => '',
			'title' => 'View all posts tagged `{:tag}`'
		);
		if (is_array($tag)) {
			$options = $tag;
			if (!isset($options['tag'])) {
				throw new BadMethodCallException('A tag must be specified in order to link to it.');
			}
		}
		extract($options + $defaults);

		$title = String::insert($title, compact('tag'));
		$url = array('controller' => 'search', 'action' => 'tag') + compact('tag');
		if (in_array($tag, \app\models\Post::$tags)) {
			$url = $tag;
		}
		$html = $this->_context->helper('html');
		return $html->link($text, $url, compact('title') + $options);
	}

	public function link($type, $options = array()) {
		$defaults = array(
			'id' => null,
			'user' => array(),
			'url' => null,
			'text' => null,
			'options' => array(
				'class' => 'button',
				'title' => null,
				'escape' => false
			)
		);
		if (is_array($type)) {
			$options = $type;
		}
		extract($options + $defaults);

		switch ($type) {
			case 'tag':
				return $this->tag($options);
			break;
			case 'endorse':
				$options['class'] .= ' endorse-post';
				if (empty($options['title'])) {
					$options['title'] = 'endorse this post';
				}
				if (empty($text)) {
					$text = '<span>endorse</span>';
				}
				$url = Router::match(
					array('controller' => 'posts', 'action' => 'endorse',
					'args' => array('id' => $id))
				);
			break;
			case 'comment':
				$options['class'] .= ' post-comment';
				if (empty($options['title'])) {
					$options['title'] = 'comment on this post';
				}
				if (empty($text)) {
					$text = '<span>comment</span>';
				}
				$url = Router::match(
					array('controller' => 'posts', 'action' => 'comment', 'id' => $id)
				);
			break;
			default:
				return null;
			break;
		}

		if (empty($user)) {
			$options['class'] .= ' inactive';
			$url = array(
				'controller' => 'users', 'action' => 'login', 'return' => base64_encode($url)
			);
		}
		$html = $this->_context->helper('html');
		return $html->link($text, $url, $options);
	}
}

?>