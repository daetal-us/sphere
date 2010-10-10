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
	<title>▴❍ <?php echo $this->title?></title>
	<?php echo $this->html->style(array('http://localhost/lithify_me/css/reset.css', 'http://localhost/lithify_me/css/base.css', 'http://localhost/lithify_me/css/forms.css', 'http://localhost/lithify_me/css/polish.css', 'sphere'));?>
	<?php echo $this->scripts();?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon'));?>
</head>
<body class="app">
	<div id="header">
		<h1><?=$this->html->link('Lithium Sphere', '/');?></h1>
		<h2><?=$this->html->link('power of community', '/');?></h2>
		<div class="nav account">
			<?php
				if ($user = $this->user->session()) {
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
			| <?php echo $this->html->link('new post', array('controller' => 'posts', 'action' => 'add')); ?>
		</div>
	</div>
	<div class="width-constraint">
		<div class="nav timespan">
			<nav>
				<span id="timespan-icon" class="icon">Timespan</span>
				<?php
				$source = $date = null;
				if (isset($filters['source'])) {
					$source = $filters['source'][0];
				}
				if (isset($filters['date'])) {
					$date = $filters['date'][0];
				}
				?>
				<ul>
					<li><?php echo $this->sphere->link('today', 'source/date', array('date' => 'today') + compact('source','filters'));?></li>
					<li><?php echo $this->sphere->link('yesterday', 'source/date', array('date' => 'yesterday') + compact('source','filters'));?></li>
					<li><?php echo $this->sphere->link('1wk', 'source/date', array('date' => '1wk') + compact('source','filters'));?></li>
					<li><?php echo $this->sphere->link('2wk', 'source/date', array('date' => '2wk') + compact('source','filters'));?></li>
					<li><?php echo $this->sphere->link('1mo', 'source/date', array('date' => '1mo') + compact('source','filters'));?></li>
					<li><?php echo $this->sphere->link('1yr', 'source/date', array('date' => '1yr') + compact('source','filters'));?></li>
					<li><?php echo $this->sphere->link('all', 'source/date', array('date' => null) + compact('source','filters'));?></li>
				</ul>
			</nav>
		</div>
		<div class="nav search">
			<nav>
				<?php
				echo $this->form->create(null, array('url' => array('controller' => 'search', 'action' => 'index'), 'method' => 'GET', 'class' => 'mini-search-form'));
				echo $this->form->text('q', array('class' => 'search-query', 'value' => (isset($q) ? $q : null)));
				echo $this->form->submit('Search', array('class' => 'search-submit'));
				echo $this->form->end();
				?>
			</nav>
		</div>
		<div class="nav sources closed">
			<nav>
				<span id="sources-icon" class="icon" title="Click me to toggle the Sources drawer.">Sources</span>
				<ul>
					<li><?php echo $this->sphere->link('<span>All</span>', 'source/date', array('escape' => false, 'class' => 'all', 'source' => null, 'title' => 'All sources') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Sphere</span>', 'source/date', array('escape' => false, 'class' => 'sphere', 'source' => 'sphere', 'title' => 'Sphere') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Lithium Network</span>', 'source/date', array('escape' => false, 'class' => 'lithium', 'source' => 'lithium', 'title' => 'Lithium') + compact('date','filters'));?></li>
				</ul>
			</nav>
		</div>
		<div id="content">
			<div class="article">
				<article>
					<?php echo $this->content;?>
				</article>
			</div>
		</div>
	</div>
	<?php echo $this->html->script(array(
		'jquery-1.4.1.min',
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
	<?php if (isset($this->viewJs)) echo $this->viewJs; ?>
</body>
</html>
