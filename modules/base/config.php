<?php

include(PATH_MODELES."/bdd.class.php");

$tables = array(
	"client",
	"typebien",
	"demande",
	"bien",
	"ressembler",
	"visiter"
);

$GLOBALS["BASE_TABLES_NAMES"] = $tables;