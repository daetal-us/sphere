<h3><?=$this->title('Post a Note')?></h3>
<?php

echo $this->form->create();
echo $this->form->text('title');
echo $this->form->textarea('content');
echo $this->form->submit('save');
echo $this->form->end();

?>