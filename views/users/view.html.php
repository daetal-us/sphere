<?php
/**
 * Lithium Sphere: communized sphere of influence
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://www.opensource.org/licenses/MIT The MIT License
 */
?>

<h1><?php echo $user->_id;?></h1>
<ul>
	<li><strong>email:</strong> <?php echo $user->email;?></li>
	<li><strong>user since: </strong> <?php echo date('l jS \of F, Y', $user->created->sec); ?></li>
</ul>