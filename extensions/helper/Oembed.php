<?php

namespace app\extensions\helper;

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
			'markdown' => false
		);

		if (!empty($options) && is_string($options)) {
			$options = array('class' => $options);
		}

		extract($options + $defaults);

		if (!$markdown) {
			$link = "<a href=\"$1\" class=\"{$class}\">$1</a>";
		} else {
			$link = "[$1]($1)";
		}
		$string = preg_replace(
			'@((?<![\[\(])https?://([-\w\.]+)+(:\d+)?(/([-\w/_\.]*(#?)([-\w]+)(\?\S+)?)?)?)@',
			$link,
			$string
		);
		return $string;
	}
}

?>