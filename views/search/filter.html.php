<h1><?=$title;?></h1>

<?php if (!empty($results)) { ?>
	<ul class="posts">
	<?php
		while ($item = $results->current()) {
			echo $this->post->row($item->post());
			$results->next();
		}
	?>
	</ul>
<?php } else { ?>
	<h2>no posts at this time.</h2>
<?php } ?>