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
				$navigation = array_fill_keys(array('source','tag','date'), null);
				foreach ($navigation as $_key => $_value) {
					if (isset($filters[$_key])) {
						$navigation[$_key] = $filters[$_key][0];
					}
				}
				extract($navigation);
				?>
				<ul>
					<li><?php echo $this->sphere->link('today', 'tag/date', array('date' => 'today') + compact('tag','filters'));?></li>
					<li><?php echo $this->sphere->link('yesterday', 'tag/date', array('date' => 'yesterday') + compact('tag','filters'));?></li>
					<li><?php echo $this->sphere->link('1wk', 'tag/date', array('date' => '1wk') + compact('tag','filters'));?></li>
					<li><?php echo $this->sphere->link('2wk', 'tag/date', array('date' => '2wk') + compact('tag','filters'));?></li>
					<li><?php echo $this->sphere->link('1mo', 'tag/date', array('date' => '1mo') + compact('tag','filters'));?></li>
					<li><?php echo $this->sphere->link('1yr', 'tag/date', array('date' => '1yr') + compact('tag','filters'));?></li>
					<li><?php echo $this->sphere->link('all', 'tag/date', array('date' => null) + compact('tag','filters'));?></li>
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
					<li><?php echo $this->sphere->link('<span>All</span>', 'source/date', array('escape' => false, 'class' => 'all', 'source' => null, 'title' => 'All posts from all sources') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Apps</span>', 'tag/date', array('escape' => false, 'class' => 'apps', 'tag' => 'apps', 'title' => 'Lithium powered applications') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Questions</span>', 'tag/date', array('escape' => false, 'class' => 'questions', 'tag' => 'questions', 'title' => 'Questions') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Press</span>', 'tag/date', array('escape' => false, 'class' => 'press', 'tag' => 'press', 'title' => 'Press') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Tutorials</span>', 'tag/date', array('escape' => false, 'class' => 'tutorials', 'tag' => 'tutorials', 'title' => 'Tutorials') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Code</span>', 'tag/date', array('escape' => false, 'class' => 'code', 'tag' => 'code', 'title' => 'Code') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Videos</span>', 'tag/date', array('escape' => false, 'class' => 'videos', 'tag' => 'videos', 'title' => 'Videos') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Podcasts</span>', 'tag/date', array('escape' => false, 'class' => 'podcasts', 'tag' => 'podcasts', 'title' => 'Podcasts') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Slides</span>', 'tag/date', array('escape' => false, 'class' => 'slides', 'tag' => 'slides', 'title' => 'Slides') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Events</span>', 'tag/date', array('escape' => false, 'class' => 'events', 'tag' => 'events', 'title' => 'Events') + compact('date','filters'));?></li>
					<li><?php echo $this->sphere->link('<span>Documentation</span>', 'tag/date', array('escape' => false, 'class' => 'docs', 'tag' => 'docs', 'title' => 'Documentation') + compact('date','filters'));?></li>
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
	<div class="footer">
		<p class="copyright">this badapp &copy; 2010 and beyond, <?php echo $this->html->link('the Union of Rad', 'http://union-of-rad.org/'); ?>.</p>
	</div>
	<?php echo $this->html->script(array(
		"jquery-1.4.1.min",
		"jquery.xdomainajax",
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
