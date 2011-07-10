<div class="post" id="add-post">

	<h1><?php echo $this->title('New Post')?></h1>

	<?php if (!empty($errors)) {
		echo "<h2>There were errors tring to save your post.</h2>";
		echo "<ul>";
		foreach ($errors as $field => $_errors) {
			foreach ($_errors as $error) {
				echo "<li>{$error}</li>";
			}
		}
		echo "</ul>";
	} ?>

	<?php echo $this->form->create(); ?>
	<label for="PostTitle">Title:</label>
	<?php echo $this->form->text('title', array('id' => 'PostTitle')); ?>
	<label for="PostContent">Content:</label>
	<?php echo $this->form->textarea('content', array('id' => 'PostContent', 'class' => 'custom-vertical-scroll')); ?>
	<div class="base-tags">
		<label class="label">Primary Tag:</label>
		<?php foreach ($tags as $tag) { ?>
			<?php echo $this->html->link($tag, "/{$tag}", array('title' => $tag, 'class' => "icon tag $tag", 'data-tag' => $tag)); ?>
		<?php } ?>
	</div>
	<label for="PostTags" class="tags">Tags:</label>
	<?php echo $this->form->text('tags', array('id' => 'PostTags')); ?>
	<small class="post-notes">separate tags with commas.</small>
	<?php echo $this->form->submit('save'); ?>
	<?php echo $this->form->end(); ?>

</div>