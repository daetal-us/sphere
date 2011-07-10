<?php if (isset($title)) { ?>
	<h1><?php echo $title;?></h1>
<?php } ?>

<?php if ($results->count()) { ?>
	<ul class="posts">
	<?php
		foreach ($results as $item) {
			echo $this->post->row($item);
		}
	?>
	</ul>
	<?php echo $this->search->pagination($results, compact('url','count','page','limit')); ?>
<?php } else { ?>
	<h2>no posts at this time.</h2>
<?php } ?>