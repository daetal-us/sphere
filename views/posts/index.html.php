<ul class="posts">
<?php
foreach ($posts as $post) {
	$comment = $this->html->link('comment', array('action' => 'comment', 'args' => array($post->id)));
	echo "<li>{$post->title} : {$post->created} : {$comment}</li>";
}
?>
</ul>