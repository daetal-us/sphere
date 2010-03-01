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
	<?php echo $this->html->charset();?>
	<title>Sphere ❍ <?php echo $this->title?></title>
	<?php echo $this->html->style(array('http://lithify.me/css/lithium.css', 'sphere'));?>
	<?php echo $this->scripts();?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon'));?>
</head>
<body class="app">
	<div id="header">
		<h1><?=$this->html->link('Lithium Sphere', '/');?></h1>
		<h2><?=$this->html->link('power of community', '/');?></h2>
		<div class="nav account">
			<?php
				if ($user = \lithium\storage\Session::read('user')) {
					echo $this->html->image(
						'http://gravatar.com/avatar/' . md5($user['email']) . '?s=16',
						array('title' => $user['username'])
					);
					echo $this->html->link('logout', array(
						'controller' => 'users', 'action' => 'logout'
					));
				} else {
					echo $this->html->link('login', array(
						'controller' => 'users', 'action' => 'login'
					));
					echo ' | ';
					echo $this->html->link('register', array(
						'controller' => 'users', 'action' => 'register'
					));
				}
			?>
		</div>
	</div>
	<div class="width-constraint">
		<div class="nav timespan">
			<nav>
				<span id="timespan-icon" class="icon">Timespan</span>
				<ul>
					<li><a href="#">today</a></li>
					<li><a href="#">yesterday</a></li>
					<li><a href="#" title="1 week">1wk</a></li>
					<li><a href="#" title="2 weeks">2wk</a></li>
					<li><a href="#" title="1 month">1mo</a></li>
					<li><a href="#" title="1 year">1yr</a></li>
					<li><a href="#" class="active">all</a></li>
				</ul>
			</nav>
		</div>
		<div class="nav sources">
			<nav>
				<span id="sources-icon" class="icon">Sources</span>
				<ul>
					<li><a href="#" class="active">All</a></li>
					<li><a href="#">Sphere</a></li>
				</ul>
			</nav>
		</div>
		<?php echo $this->html->link('contribute', array('controller' => 'posts', 'action' => 'add'), array('class' => 'new-post')); ?>
		<div id="content">
			<div class="article">
				<article>
					<?php echo $this->content;?>
				</article>
			</div>
		</div>
	</div>
	<?php echo $this->html->script(array(
		'http://code.jquery.com/jquery-1.4.2.min.js',
		"sphere",
		"jquery.oembed",
		"pretty.date",
		"showdown/showdown",
		"http://lithify.me/js/rad.cli.js",
	)); ?>
	<script type="text/javascript">
		$(document).ready(function() {
			li3Sphere.setup();
			$('.post-content a, .post-comment-content a').oembed(null, {
				embedMethod: 'annotate',
				maxWidth: 425,
				maxHeight: 425
			});
			RadCli.setup();
		});
	</script>
</body>
</html>