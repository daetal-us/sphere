<?php
/**
 * Lithium Sphere: communized sphere of influence
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://www.opensource.org/licenses/MIT The MIT License
 */

/**
 * @see lithium\security\auth\adapter\Form
 */
use lithium\security\Auth;

Auth::config(array(
	'user' => array(
		'session' => array(
			'options' => array(
				'name' => 'li3_user'
			)
		),
		'adapter' => array(
			'Form' => array(
				'filters' => array(
					'password' => array('lithium\util\String', 'hash')
				),
				'validators' => array()
			)
		),
		'model' => '\li3_users\models\User',
		'fields' => array('_id', 'password'),
		'validators' => array(
			'password' => function($submitted, $actual) {
				return \lithium\util\String::hash($submitted) == $actual;
			}
		)
	)
));

?>