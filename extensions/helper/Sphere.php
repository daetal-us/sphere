<?php

namespace app\extensions\helper;

class Sphere extends \lithium\template\Helper {

	/**
	 * A convenience method to output common site links
	 *
	 * @param mixed $text
	 * @param array $data
	 * @param array $options
	 */
	public function link($text, $data = array(), $options = array()) {
		$defaults = array(
			'tag' => null,
			'date' => null
		);
		extract($data + $defaults);

		$parts = array();
		if (isset($tag)) {
			$parts[] = $tag;
		}
		if (isset($date)) {
			$parts[] = $date;
		}
		$link = implode($parts, "/");
		$html = $this->_context->helper('html');
		return $html->link($text, $link, $options);
	}
}

?>