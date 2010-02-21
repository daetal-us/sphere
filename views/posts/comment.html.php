<div class="post">
	<h1><?=$this->title($post->title);?></h1>
	<span class="post-author" style="background-image:url(http://gravatar.com/avatar/<?php
		echo md5($post->user->email);
	?>?s=16);"> submitted by <b><?=$post->user->username;?></b></span>
	<span class="post-date"><?=date("F j, Y, g:i a T", strtotime($post->created))?></span>
<p class="post-content"><?php echo $this->oembed->classify(nl2br($post->content)); ?></p>
<?=$this->html->link('comment on this post', '#', array('class' => 'post-comment'));?>
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