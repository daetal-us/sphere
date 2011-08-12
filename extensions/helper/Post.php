<?php

namespace app\extensions\helper;

use lithium\net\http\Router;
use lithium\util\String;

/**
 * This helper provides tools to uniformly render posts across views.
 */
class Post extends \lithium\template\Helper {

	public function rows($posts = null, $options = array()) {
		if (empty($posts)) {
			return null;
		}
		$defaults = array(
			'class' => 'posts'
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

		$class = ($post->rating == 0 ? ' empty' : null);
		$rating = '<span>' . $post->rating . '</span>';
		$rating = '<span class="post-rating' . $class . '">' . $rating . '</span>';

		$category = null;
		if (!empty($post->tags)) {
			foreach ($post->tags as $tag) {
				if (!in_array($tag, \app\models\Post::$tags)) {
					continue;
				}
				$category = $tag;
				break;
			}
		}

		$heading = $html->link(
			$post->title,
			array(
				'controller' => 'posts', 'action' => 'comment', '_id' => $post->_id
			)
		);
		$heading = '<h2 class="' . $category . '">' . $heading . '</h2>';

		$author = $html->link($post->user()->_id, array(
			'controller' => 'search',
			'action' => 'filter',
			'_id' => $post->user()->_id
		), array(
			'class' => 'post-author',
			'title' => 'Search for more posts by this author',
			'style' => 'background-image:url(' . $gravatar->url(array(
				'email' => $post->user()->email, 'params' => array('size' => 16)
			)) . ')'
		));

		$timestamp = $post->created->sec;
		$date = $date = date("F j, Y, g:i a T", $timestamp);
		$time = "<span class=\"post-created pretty-date\" title=\"{$date}\">{$date}";
		$time .= "<span class=\"timestamp\">{$timestamp}</span></span>";

		$count = (empty($post->comment_count) ? 0 : $post->comment_count);
		$class = ($count > 0) ? (($count > 1) ? 'many' : 'one') : 'none';
		$text = (($count < 1) ? 'no' : $count);
		$text .= ' comment' . (($count !== 1) ? 's' : '');
		$comments = $html->link(
			$text,
			array(
				'controller' => 'posts', 'action' => 'comment', '_id' => $post->_id
			),
			array('class' => 'comments ' . $class)
		);

		$meta = implode('', compact('comments','author','time'));
		$meta = "<div class=\"meta\">{$meta}</div>";

		$content = implode('', compact('heading','meta'));

		return '<li class="post">' . $content . '</li>';
	}

	/**
	 * Generate a link to tags
	 *
	 * @param string $tag
	 * @param array $options
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
		$url = array('controller' => 'search', 'action' => 'filter') + compact('tag');
		if (in_array($tag, \app\models\Post::$tags)) {
			$url = $tag;
		}
		$html = $this->_context->helper('html');
		return $html->link($text, $url, compact('title') + $options);
	}

	public function link($type, $options = array()) {
		$defaults = array(
			'_id' => null,
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
					'args' => array('_id' => $_id))
				);
			break;
			case 'comment':
				$options['class'] .= ' post-comment';
				if (empty($options['title'])) {
					$options['title'] = 'comment on this post';
				}
				if (empty($text)) {
					$text = '<span>add comment</span>';
				}
				$url = Router::match(
					array('controller' => 'posts', 'action' => 'comment', '_id' => $_id)
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