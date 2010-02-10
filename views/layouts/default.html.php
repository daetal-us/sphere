<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */
?>
<!doctype html>
<html>
<head>
	<?=$this->html->charset();?>
	<title>Application > <?=$this->title?></title>
	<?=$this->html->style('base');?>
	<?=$this->scripts();?>
	<?=$this->html->link('Icon', null, array('type' => 'icon'));?>
</head>
<body class="app">
	<div id="container">
		<div id="header">
			<h1>Lithosphere</h1>
			<h2><?=$this->html->link('power of community', '/');?></h2>
		</div>
		<div id="content">
			<?=$this->content;?>
		</div>
	</div>
</body>
</html>