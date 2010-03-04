<div class="post">

	<h1><?=$this->title('Search')?></h1>
	<?php

    echo $this->form->create(null, array('method' => 'GET'));
	echo $this->form->text('term');
	echo $this->form->submit('Search now');
	echo $this->form->end();

	?>

</div>
<?php if (empty($results->rows)) {
	echo "<h2>No Results</h2>";
	return;
} ?>
<ul class="posts">
<?php
	foreach ($results->rows as $item) {
		echo $this->post->row($item->post());
	}
?>
</ul>