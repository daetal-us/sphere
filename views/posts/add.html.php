<div class="post">

	<h1><?=$this->title('Post a Note')?></h1>
	<?php echo $this->form->create(); ?>
	<label for="PostTitle">Title:</label>
	<?php echo $this->form->text('title', array('id' => 'PostTitle')); ?>
	<label for="PostContent">Content:</label>
	<?php echo $this->form->textarea('content', array('id' => 'PostContent')); ?>
	<label for="PostTags">Tags:</label>
	<?php echo $this->form->text('tags', array('id' => 'PostTags')); ?>
	<small class="post-notes">Separate tags with commas.</small>
	<?php echo $this->form->submit('save'); ?>
	<?php echo $this->form->end(); ?>

</div>