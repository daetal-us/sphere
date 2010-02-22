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

	<?=$this->html->link('<span>comment on this post</span>', '#', array(
		'class' => 'post-comment', 'escape' => false
	));?>
	<h3>comments</h3>
	<div id="add-comment" style="display:none;">
		<?php echo $this->thread->form((array) count($post->comments));?>
	</div>

	<?php
	$args = $this->request()->params['args'];
	$id = array_shift($args);

	if (!empty($post->comments)) {
		echo $this->thread->comments($post, compact('args'));
	}
	?>

</div>