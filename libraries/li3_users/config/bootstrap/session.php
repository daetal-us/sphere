<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * This configures your session storage. You need to add this to your app's `bootstrap/session.php`
 */
	// use lithium\storage\Session;
	//
	// Session::config(array(
	// 	'li3_user' => array('adapter' => 'Php')
	// ));

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
		'adapter' => 'Form',
		'model' => '\li3_users\models\User',
		'query' => 'login',
		'fields' => array('_id', 'password')
	)
));

?>