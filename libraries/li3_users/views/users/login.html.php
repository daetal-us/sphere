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
<?php if (!empty($errors)) {
	echo "<h2>That didn't seem to work.</h2>";
} ?>
<?php
echo $this->form->create();
echo $this->form->field('_id');
echo $this->form->field('password', array('type' => 'password'));
echo $this->form->submit('login', array('options' => compact('disabled')));
echo $this->form->end();
?>