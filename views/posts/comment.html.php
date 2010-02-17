<h2><?=$this->title($post->title);?></h2>
<p><?php echo $this->oembed->classify($post->content); ?></p>
<h3>comments</h3>
<?php echo $this->thread->form((array) count($post->comments));?>
<?php
$args = $this->request()->params['args'];
$id = array_shift($args);

if (!empty($post->comments)) {
	echo $this->thread->comments($post, compact('args'));
}
?>