<?php
/**
 * Lithium Sphere: communized sphere of influence
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://www.opensource.org/licenses/MIT The MIT License
 */

use li3_swiftmailer\mailer\Transports;

Transports::config(array('default' => array(
    'adapter' => 'PhpMail'
)));

?>