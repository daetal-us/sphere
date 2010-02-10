<h4><?=$this->html->link('post a note', array('action' => 'add'));?></h4>
<ul>
<?php
foreach ($posts as $post) {
	$comment = $this->html->link('comment', array('action' => 'comment', 'args' => array($post->id)));
	echo "<li>{$post->title} : {$post->created} : {$comment}</li>";
}
?>
</ul>

