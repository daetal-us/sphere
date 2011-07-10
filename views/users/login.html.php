<?php
if (!empty($user)) {
	echo "<h1>" . $this->User->greeting($user) . "</h1>";
	if (!empty($return)) {
		echo $this->html->link('continue what you were doing...', base64_decode($return));
	}
	return;
}
?>
<h1><?php echo $this->title('Login')?></h1>
<?php if ($errors) {
	echo "<h2>{$errors}</h2>";
} ?>
<?php
if (!$disabled) {
	echo $this->form->create();
	echo $this->form->field('_id', array('label' => 'User ID'));
	echo $this->form->field('password', array('type' => 'password'));
	echo $this->form->submit('login');
	echo $this->form->end();
}
?>