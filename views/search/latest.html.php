<?php if (!empty($results)) { ?>
	<ul class="posts">
	<?php
		while ($item = $results->rows->current()) {
			echo $this->post->row($item->post());
			$results->rows->next();
		}
	?>
	</ul>
<?php } else { ?>
	<h2>no posts at this time.</h2>
<?php } ?>