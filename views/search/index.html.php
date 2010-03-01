<div class="post">

	<h1><?=$this->title('Search')?></h1>
	<?php

    echo $this->form->create(null, array('method' => 'GET'));
	echo $this->form->text('term');
	echo $this->form->submit('Search now');
	echo $this->form->end();

	?>

</div>
<?php if (empty($posts->rows)) {
	echo "<h2>No Results</h2>";
	return;
} ?>
<ul class="posts">
<?php
	foreach ($posts->rows as $post) {
		$post->doc->id = $post->doc->_id;
		echo $this->post->row($post->doc);
	}
?>
</ul>