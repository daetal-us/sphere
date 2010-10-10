<?php

namespace app\extensions\helper;

use \lithium\util\String;

class Sphere extends \lithium\template\Helper {

	protected $_links = array(
		'1wk' => '1wk',
		'2wk' => '2wk',
		'1mo' => '1mo',
		'1yr' => '1yr',
		'today' => 'today',
		'yesterday' => 'yesterday',
		'source/date' => '{:source}/{:date}'
	);

	/**
	 * A convenience method to output common site links
	 */
	public function link($text, $type, $options = array()) {
		$link = null;
		$html = $this->_context->helper('html');
		if (isset($this->_links[$type])) {
			$link = $this->_links[$type];
			if (!empty($options)) {
				$data = $options;
				if (isset($options['data'])) {
					$data = $options['data'];
				}
				$link = String::insert($link, $data);
			}
			$link = $html->link($text, $link, $options);
		}
		return $link;
	}

}

?>