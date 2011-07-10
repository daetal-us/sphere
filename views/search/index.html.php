<h1>search results</h1>
<?php if (!empty($q) && !$count) { ?>
	<p>No posts matched your query. Need <?php echo $this->html->link('help', '#', array('class' => 'toggle-search-help')); ?>?</p>
<?php } elseif ($count) { ?>
	<p><?php echo $count; ?> post<?php echo ($count > 1) ? 's' : null; ?> matched your query. Need <?php echo $this->html->link('help', '#', array('class' => 'toggle-search-help')); ?> finding better results?</p>
<?php } else { ?>
	<p>Try typing in your search query in the input to the top right. Need <?php echo $this->html->link('help', '#', array('class' => 'toggle-search-help')); ?>?</p>
<?php } ?>
<aside>
<div class="search-help">
	<section>
	<h2>searching sphere</h2>
	<p>Sphere currently features limited search functionality. Full-text search is not implemented at the moment, and though you can search titles, it is simply a multi-keyed index by word - so you'll have to search for a single, exact word. Hang in there while we explore an optimal solution.</p>
	<h3>terms</h3>
	<p>The core of your search consists of your terms. You can input a single term, or word to start. Your search will return posts with the term provided if present in either the title, the author username, or as a tag.</p>
	<h3>field: term</h3>
	<p>We index particular data relating to each post referred to as fields. You can target a specific field, or even combination of them, for your specific search using the field name followed by colon and your query. For example, <code>title: "lithium"</code> would result in posts with "lithium" in their title.</p>
	<table>
		<thead>
			<th>field</th>
			<th>description of post data</th>
		</thead>
		<tbody>
			<tr>
				<td><code>title</code></td>
				<td>the post title</td>
			</tr>
			<tr>
				<td><code>author</code></td>
				<td>the post author's _id</td>
			</tr>
			<tr>
				<td><code>tag</code></td>
				<td>tags that have been associated with the post</td>
			</tr>
			<tr>
				<td><code>date</code></td>
				<td><code>Y-m-d</code> post creation date</td>
			</tr>
			<tr>
				<td><code>from</code></td>
				<td><code>Y-m-d</code> post created starting from date</td>
			</tr>
			<tr>
				<td><code>to</code></td>
				<td><code>Y-m-d</code> post created up to date</td>
			</tr>

		</tbody>
	</table>
	<a class="close-search-help" href="#">close help</a>
	</section>
</div>
</aside>

<?php if ($count) { ?>
	<ul class="posts">
	<?php
	foreach ($results as $post) {
		echo $this->post->row($post);
	}
	?>
	</ul>
	<?php echo $this->search->pagination($results, compact('url','count','page','limit')); ?>
<?php } ?>

<?php $this->viewJs = "
<script type=\"text/javascript\">
	$(document).ready(function() {
		$('.search-help').hide();
		$('.toggle-search-help').toggle(function() {
			$('.search-help').show('slow');
		}, function() {
			$('.search-help').hide('slow');
		});
		$('.close-search-help').click(function() {
			$('.toggle-search-help').click();
			return false;
		});
	});
</script>"; ?>