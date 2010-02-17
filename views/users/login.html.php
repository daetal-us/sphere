<?php
	if (!empty($user)) {
		echo "Nice, {$user['username']} you logged in.";
	}
?>

<h3><?=$this->title('Login')?></h3>
<?php
echo $this->form->create();
echo $this->form->field('username');
echo $this->form->field('password', array('type' => 'password'));
echo $this->form->submit('login');
echo $this->form->end();
?>