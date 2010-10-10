<?php

namespace app\extensions\helper;

class Gravatar extends \lithium\template\Helper {

	/**
	 * Base url for image paths
	 *
	 * @var string
	 */
	protected $_base = 'http://gravatar.com/avatar/';

	/**
	 * Default parameters
	 *
	 * @var string
	 */
	protected $_params = array(
		'size' => 64,
		'default' => '',
		'rating' => 'G'
	);

	/**
	 * Generate a gravatar image url
	 *
	 * @param array options
	 * @return string
	 */
	public function url($options = array()) {
		$defaults = array(
			'email' => null,
			'extension' => false,
			'params' => array()
		);

		if (is_string($options)) {
			$options = array('email' => $options);
		}

		$options += $defaults;
		extract($options);

		$params += $this->_params;

		$params['default'] = urlencode($params['default']);

		$ext = null;
		if ($extension) {
			$ext = '.jpg';
		}

		return $this->_base . md5($email) . '?' . http_build_query($params) . $ext;
	}

}

?>