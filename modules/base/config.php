<?php

include(PATH_MODELES."/bdd.class.php");

$bdd = new BDD();
$tables = $bdd->select("SHOW TABLES;");
if($tables != false) {
	$tablesNames = array();
	foreach($tables as $elem) {
		$tablesNames[] = $elem["Tables_in_".DBNAME];
	}
}
$bdd->close();

$GLOBALS["BASE_TABLES_NAMES"] = $tablesNames;