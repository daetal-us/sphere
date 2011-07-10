<?php if ($count) { ?>
	<ul class="posts">
	<?php
		foreach ($results as $post) {
			echo $this->post->row($post);
		}
	?>
	</ul>
	<?php echo $this->search->pagination($results, compact('url','count','page','limit')); ?>
<?php } else { ?>
	<h2>no posts at this time.</h2>
<?php } ?>