<div class="post">
	<?php echo
		($endorsed) ?
			'<span class="endorsed"></span>' :
			$this->Post->link('endorse', compact('user') + array('_id' => $post->_id));
	?>
	<div class="post-meta">
		<aside>
		<?php $date = date("F j, Y, g:i a T", $post->created->sec); ?>
		<span class="post-date pretty-date" title="<?php echo $date;?>">
			<?php echo $date;?>
			<span class="timestamp"><?php echo $post->created->sec;?></span>
		</span>
		<?php $gravatar = $this->gravatar->url(array('email' => $post->user()->email, 'params' => array('size' => 16))); ?>
		<span class="post-author" style="background-image:url(<?php echo $gravatar;?>);">
			<?php echo $this->html->link($post->user()->_id, array('controller' => 'search', 'action' => 'filter', '_id' => $post->user()->_id), array('title' => 'Search for more posts by this author'));?>
		</span>
		<?php if (!empty($post->tags)) { ?>
		<span class="tags">
		<?php
			$tags = array();
		 	foreach ($post->tags as $tag) {
				$tags[] = $this->post->tag($tag);
			} ?>
			<?php echo implode(", \n", array_unique($tags)); ?>
		</span>
		<?php } ?>
		</aside>
	</div>

	<h1><?php echo $this->title($post->title);?></h1>

	<div class="post-content">
		<?php
			$content = $this->oembed->classify($h($post->content), array('markdown' => true));
			echo \markdown\Markdown::parse($content);
		?>
	</div>


	<?php if (!empty($user)) { ?>
	<div id="add-comment" style="display:none;">
		<h3>add a comment</h3>
		<?php echo $this->form->create(); ?>
		<?php echo $this->form->textarea("content", array('class' => 'custom-vertical-scroll')); ?>
		<?php echo $this->form->submit('post comment'); ?>
		<?php echo $this->form->end(); ?>
	</div>
	<?php } ?>
	<?php echo $this->Post->link('comment', compact('user') + array('_id' => $post->_id)); ?>
	<?php
	$args = $this->request()->args;
	if (!empty($post->comments)) { ?>
		<h3 class="comments">comments</h3>
		<?php
		$comments = $post->comments();
		if ($comments->count() != $post->comment_count) {
			echo $this->html->link('show all comments', '#', array('class' => 'view-all-comments button'));
		}

		echo $this->thread->comments($post->data(), compact('args'));
	}
	?>

</div>