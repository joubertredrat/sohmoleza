<?php
	require('sohMoleza.class.php');

	sohMoleza::iniciar()
	->setDadosBanco('code', 'user', 'pass')
	->validarMetodoSet()
	->gerarClasses();