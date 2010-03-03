<?php
use \lithium\net\http\Router;
?>
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
			echo $h($this->oembed->classify($post->content, array('markdown' => true)));
		?></pre>
	</div>
	<?php
		$commentClass = 'post-comment';
		$commentUrl = Router::match(
			array('controller' => 'posts', 'action' => 'comment', 'id' => $post->id)
		);
		$endorseClass = 'endorse-post';
		$endorseUrl = Router::match(
			array('controller' => 'posts', 'action' => 'endorse',
			'args' => array('id' => $post->id))
		);
		if (empty($author)) {
			$commentClass .= ' inactive';
			$commentUrl = array(
				'controller' => 'users', 'action' => 'login', 'return' => base64_encode($commentUrl)
			);
			$endorseClass .= ' inactive';
			$endorseUrl = array(
				'controller' => 'users', 'action' => 'login', 'return' => base64_encode($endorseUrl)
			);
		}
		$comment = $this->html->link(
			'<span>comment</span>',
			$commentUrl,
			array('class' => $commentClass, 'escape' => false, 'title' => 'comment on this post')
		);
		$endorse = $this->html->link(
			'<span>endorse</span>',
			$endorseUrl,
			array('class' => $endorseClass, 'escape' => false, 'title' => 'endorse this post')
		);
	?>

	<?php echo $endorse;?>
	<?php echo $comment;?>

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
	$args = $this->request()->args;
	if (!empty($post->comments)) {
		echo $this->thread->comments($post, compact('args'));
	}
	?>

</div>