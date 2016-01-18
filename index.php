<?php

// Try to handle it with the upper level index.php. (it should know what to do.)
if (file_exists(dirname(dirname(__FILE__)) . '/index.php'))
	include (dirname(dirname(__FILE__)) . '/index.php');
		//Agregando este indice para resolver el tema del titulo, para evitar la redundancia*/
	$context['estoyenindex'] = true;
else
	exit;

?>