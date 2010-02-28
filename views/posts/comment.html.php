<div class="post">

	<h1><?=$this->title($post->title);?></h1>

	<?php $date = date("F j, Y, g:i a T", strtotime($post->created)); ?>
	<span class="post-date pretty-date" title="<?=$date;?>">
		<?=$date;?>
		<span class="timestamp"><?=strtotime($post->created);?></span>
	</span>
	<?php $gravatar = "http://gravatar.com/avatar/" . md5($post->user->email) . "?s=16"; ?>
	<span class="post-author" style="background-image:url(<?=$gravatar;?>);">
		submitted by <b><?=$post->user->username;?></b>
	</span>

	<div class="post-content">
		<pre class="markdown"><?php
			echo $this->oembed->classify($post->content, array('markdown' => true));
		?></pre>
	</div>
	<?php
		$commentClass = 'post-comment';
		$commentUrl = \lithium\net\http\Router::match(
			array('controller' => 'posts', 'action' => 'comment', 'args' => array('id' => $post->id))
		);
		if (empty($author)) {
			$commentClass .= ' inactive';
			$commentUrl = array(
				'controller' => 'users', 'action' => 'login', 'return' => base64_encode($commentUrl)
			);
		}
	?>
	<?=$this->html->link(
		'<span>comment on this post</span>',
		$commentUrl,
		array('class' => $commentClass, 'escape' => false)
	);?>

	<h3>comments</h3>
	<?php if (!empty($author)) { ?>
	<div id="add-comment" style="display:none;">
		<?php echo $this->form->create(); ?>
		<?php echo $this->form->textarea("comment"); ?>
		<?php echo $this->form->submit('post comment'); ?>
		<?php echo $this->form->end(); ?>
	</div>
	<?php } ?>

	<?php
	$args = $this->request()->params['args'];
	$id = array_shift($args);

	if (!empty($post->comments)) {
		echo $this->thread->comments($post, compact('args'));
	}
	?>

</div>