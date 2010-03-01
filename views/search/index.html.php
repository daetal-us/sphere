<div class="post">

	<h1><?=$this->title('Search')?></h1>
	<?php

    echo $this->form->create(null, array('method' => 'GET'));
	echo $this->form->text('term');
	echo $this->form->submit('Search now');
	echo $this->form->end();

	?>

</div>
