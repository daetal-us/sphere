<h1><?php echo $this->title('Register')?></h1>

<?php if ($errors) {
	echo "<h2>There were errors tring to create your account.</h2>";
	echo "<ul>";
	foreach ($errors as $field => $_errors) {
		foreach ($_errors as $error) {
			echo "<li>{$error}</li>";
		}
	}
	echo "</ul>";
} ?>

<?php
echo $this->form->create();
echo $this->form->field('_id');
echo $this->form->field('password', array('type' => 'password'));
echo $this->form->field('email');
echo $this->form->submit('save');
echo $this->form->end();
?>