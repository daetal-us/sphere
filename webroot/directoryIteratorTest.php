<?php

$DI = new \DirectoryIterator('/Users/jon/Sites/lithium_sphere/resources/tmp/cache');

foreach ($DI as $file) {
	var_dump($file->getFilename());
	var_dump($file->isFile());
}

?>