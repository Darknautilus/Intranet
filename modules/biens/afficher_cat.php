<?php

if(!isset($_GET["selected"]))
	$selected = "all";
else
	$selected = $_GET["selected"];

$result = false;
$categories = null;
$biens = null;
$error = "";

$bdd = new BDD();

$categories = $bdd->select("select idtype,nomtype from typebien;");
if(!$categories)
	$error .= "Erreur à la récupération des catégories : ".$bdd->getLastError();

if(isset($_GET["formfill"]) && $_GET["formfill"] == 1) {
	
	$requete = "select idbien, titrebien, photobien from bien";
	if($_POST["cat"] == "all")
		$requete .= ";";
	else
		$requete .= " where idtype = '".$_POST["cat"]."';";
		
	$selected = $_POST["cat"];
	
	$biens = $bdd->select($requete);
	if(!$biens)
		$error .= "Erreur à la récupération des biens : ".$bdd->getLastError();
	
	$result = true;
}
	


$bdd->close();

echo $twig->render("biens_afficher_cat.html",array("categories" => $categories, "biens" => $biens, "result" => $result, "selected" => $selected, "error" => $error));