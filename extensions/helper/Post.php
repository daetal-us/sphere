<?php

namespace app\extensions\helper;

/**
 * This helper provides tools to uniformly render posts across views.
 */
class Post extends \lithium\template\Helper {

	public function posts($posts = null, $options = array()) {
		$out = null;
		if (!empty($posts)) {
			$defaults = array(
				'class' => 'posts',
				'gravatar' => array(
					'size' => '64',
					'link' => false
				)
			);
			$options += $defaults;
			extract($options);

			$html = $this->_context->helper('html');
			$content = null;
			foreach ($posts as $post) {
				$image = $html->image(
					'http://gravatar.com/avatar/'.md5($post->user->email).'?s='.$gravatar['size'],
					array('class' => 'gravatar')
				);
				if ($gravatar['link']) {
					$image = $html->link($img, $gravatar['link'], array('escape' => false));
				}

				$heading = '<h2>' . $html->link($post->title, array(
					'controller' => 'posts', 'action' => 'comment', 'args' => array('id' => $post->id)
				)) . '</h2>';

				$author = 	'<cite class="author">submitted by <strong>' .
								$post->user->username . '</strong></cite>';

				$count = (empty($post->comments) ? 0 : $post->comments->count());
				$commentsClass = ($count > 0) ? (($count > 1) ? 'many' : 'one') : 'none';
				$comments = 	'<span class="comments ' . $commentsClass . '">' .
									(($count < 1) ? 'no' : $count) . ' comment' .
									(($count !== 1) ? 's' : '') . '</span>';

				$content .= '<li class="post">' . $image . $heading . $author . $comments;
			}

			$out = '<ul class="' . $class . '">' . $content . '</ul>';
		}
		return $out;
	}

}


?>