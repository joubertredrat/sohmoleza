<?php
	require('../src/sohMoleza.class.php');

	sohMoleza::iniciar()
	->setDadosBanco('joubert_teste', 'root', 'verdade')
	->validarMetodoSet()
	->gerarClasses();