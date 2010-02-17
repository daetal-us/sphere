<?php

namespace app\extensions\helper;

use \lithium\util\String;

class Oembed extends \lithium\template\Helper {

	/**
	 * Method to wrap urls within a string with an html anchor tag with a class for use with oEmbed.
	 *
	 * @param string $string string to search for urls
	 * @param array|string $options if string, assumed classname to use for anchors. otherwise,
	 * 	array used to merge against default options.
	 */
	public function classify($string = null, $options = array()) {
		$defaults = array(
			'class' => 'oembed',
			'title' => 'oEmbed'
		);

		if (!empty($options) && is_string($options)) {
			$options = array('class' => $options);
		}

		extract($options + $defaults);

		$string = preg_replace(
			'@(https?://([-\w\.]+)+(:\d+)?(/([-\w/_\.]*(#?)([-\w]+)(\?\S+)?)?)?)@',
			'<a href="$1" title="'.$title.'" class="'.$class.'">$1</a>',
			$string
		);

		return $string;
	}

}

?>