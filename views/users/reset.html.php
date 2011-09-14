<?php
/**
 * Lithium Sphere: communized sphere of influence
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://www.opensource.org/licenses/MIT The MIT License
 */
?>

<h1><?php echo $this->title($title); ?></h1>

<?php if ($cooldown) { ?>
	<h2>you have failed too many times. try again later.</h2>
<?php } ?>

<?php
if ($errors) {
	echo "<ul class=\"errors\">";
	foreach ($errors as $field => $_errors) {
		foreach ($_errors as $error) {
			echo "<li>{$error}</li>";
		}
	}
	echo "</ul>";
}
?>


<?php if (!$cooldown && $resetting && !$success) {
	echo $this->form->create();
	echo $this->form->field('_id', array('label' => 'User ID'));
	echo $this->form->field('password', array('type' => 'password'));
	echo $this->form->submit('save');
	echo $this->form->end();
} ?>

<?php if (!$cooldown && !$resetting && !$success) {
	if (!$emailed) {
		echo $this->form->create();
		echo $this->form->field('_id', array('label' => 'User ID'));
		echo $this->form->submit('email token');
		echo $this->form->end();
	} else {
		echo "<h2>Check your inbox.</h2>";
	}
} ?>