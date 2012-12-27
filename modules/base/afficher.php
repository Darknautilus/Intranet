<?php

if(isset($_POST["ajax"]) && $_POST["ajax"] == "true") {
	
	$errors = array();
	$tableFields = null;
	$tableFieldsNames = null;
	
	$bdd = new BDD();
	
	$tableContent = $bdd->select("select * from ".$_POST["table"].";");
	if(!$tableContent) {
		$error[] = "Erreur de sélection de ".$_POST["table"]." : ".$bdd->getLastError();
	}
	else {
		$tableFieldsNames = array_keys($tableContent[0]);
	}
	
	$bdd->close();
	
	echo $twig->render("base_afficher_table.html", array("tableContent" => $tableContent, "tableFieldsNames" => $tableFieldsNames, "errors" => $errors));
}
else {
	echo $twig->render("base_afficher.html", array("tables" => $GLOBALS["BASE_TABLES_NAMES"]));
}