<div class="post">
	<h1><?=$this->title($post->title);?></h1>
	<span class="post-date pretty-date" title="<?=date("F j, Y, g:i a T", strtotime($post->created))?>"><?=date("F j, Y, g:i a T", strtotime($post->created))?><Span class="timestamp"><?=strtotime($post->created);?></span></span>
	<span class="post-author" style="background-image:url(http://gravatar.com/avatar/<?php
		echo md5($post->user->email);
	?>?s=16);"> submitted by <b><?=$post->user->username;?></b></span>

<div class="post-content"><pre class="markdown"><?php echo $post->content; ?></pre></div>
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