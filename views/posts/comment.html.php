<div class="post">

	<div class="post-meta">
		<aside>
		<?php $date = date("F j, Y, g:i a T", $post->created); ?>
		<span class="post-date pretty-date" title="<?=$date;?>">
			<?=$date;?>
			<span class="timestamp"><?=$post->created;?></span>
		</span>
		<?php $gravatar = $this->gravatar->url(array('email' => $post->user()->email, 'params' => array('size' => 16))); ?>
		<span class="post-author" style="background-image:url(<?=$gravatar;?>);">
			<?=$this->html->link($post->user()->username, array('controller' => 'search', 'action' => 'filter', 'username' => $post->user()->username), array('title' => 'Search for more posts by this author'));?>
		</span>
		<?php if (!empty($post->tags)) { ?>
		<span class="tags">
		<?php
			$tags = array();
		 	foreach ($post->tags as $tag) {
				$tags[] = $this->post->tag($tag);
			} ?>
			<?php echo implode(", \n", $tags); ?>
		</span>
		<?php } ?>
		</aside>
	</div>

	<h1><?php echo $this->title($h($post->title));?></h1>

	<div class="post-content">
		<pre class="markdown"><?php
			echo $h($this->oembed->classify($post->content, array('markdown' => true)));
		?></pre>
	</div>
	<?php echo $this->Post->link('endorse', compact('user') + array('id' => $post->id)); ?>
	<?php echo $this->Post->link('comment', compact('user') + array('id' => $post->id)); ?>

	<?php if (!empty($user)) { ?>
	<div id="add-comment" style="display:none;">
		<h3>add a comment</h3>asdf
		<?php echo $this->form->create(); ?>
		<?php echo $this->form->textarea("content", array('class' => 'custom-vertical-scroll')); ?>
		<?php echo $this->form->submit('post comment'); ?>
		<?php echo $this->form->end(); ?>
	</div>
	<?php } ?>

	<?php
	$args = $this->request()->args;
	if (!empty($post->comments)) { ?>
		<h3 class="comments">comments</h3>
		<?php
		$comments = $post->comments();
		if ($comments->count() != $post->comment_count) {
				echo $this->html->link('open all comments', '#', array('class' => 'view-all-comments button'));
			}

		echo $this->thread->comments($post->data(), compact('args'));
	}
	?>

</div>