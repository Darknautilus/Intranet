<?php

$bien = false;
$nbVisite = null;
$idValide = null;
$errors = array();
$values = array();
$redirect = false;

// Controle du paramètre (id du bien)
if(isset($_GET["id"]) && !empty($_GET["id"])) {
	$bdd = new BDD();
	$bien = $bdd->select("select idbien from bien where idbien = '".$_GET["id"]."';");
	$bdd->close();
}

if($bien != false) {
	
	$idValide = true;
	
	$bdd = new BDD();
	
	$bien = $bdd->select("select b.idbien, b.titrebien, b.detailbien, b.adrbien, b.prixbien, b.idtype, b.photobien, t.nomtype from bien b, typebien t where idbien = '".$_GET["id"]."' AND t.idtype = b.idtype;");
	
	$nbVisite = $bdd->select("select COUNT(*) as nbvisite from visiter v where v.idbien = '".$_GET["id"]."';");
	if(!$nbVisite)
		$nbVisite = 0;
	else
		$nbVisite = $nbVisite[0]["nbvisite"];
	
	
	// Controle du formulaire
	if(isset($_POST["filled"]) && $_POST["filled"] == "true") {
	
		if(!isset($_POST["nom"]) || empty($_POST["nom"]))
			$errors[] = "Vous devez entrer un nom";
		else
			$values["nom"] = $_POST["nom"];
	
		if(!isset($_POST["adresse"]) || empty($_POST["adresse"]))
			$errors[] = "Vous devez entrer une adresse";
		else
			$values["adresse"] = $_POST["adresse"];
	
		if(!isset($_POST["tel"]) || empty($_POST["tel"]))
			$errors[] = "Vous devez entrer un numéro de téléphone correct";
		else
			$values["tel"] = $_POST["tel"];
	
		if(!isset($_POST["email"]) || empty($_POST["email"]))
			$errors[] = "Vous devez entrer une adresse e-mail valide";
		else
			$values["email"] = $_POST["email"];
	
		if(!isset($_POST["dispo"]) || empty($_POST["dispo"]))
			$errors[] = "Vous devez entrer des disponibilités valides";
		else
			$values["dispo"] = $_POST["dispo"];
		
		// Si le formulaire est correct, on insère les infos dans la base
		if(empty($errors)) {
			$idClientInser = $bdd->insert("client", array("nomclient" => $_POST["nom"], "adrclient" => $_POST["adresse"], "telclient" => $_POST["tel"], "emailclient" => $_POST["email"]));
			if(!$idClientInser)
				$errors[] = "Erreur insertion client : ".$bdd->getLastError();
			$idDemandeInser = $bdd->insert("demande", array("datedemande" => date("Y-m-j H:i:s"), "disponibilite" => $_POST["dispo"], "idclient" => $idClientInser));
			if(!$idDemandeInser)
				$errors[] = "Erreur insertion demande : ".$bdd->getLastError();
			$resultInsertVisiter = $bdd->insert("visiter", array("idbien"=> $_GET["id"], "iddemande" => $idDemandeInser, "priorite" => 1));
			
			if(!$resultInsertVisiter)
				$errors[] = "Erreur insertion visite : ".$bdd->getLastError();
			
			if(empty($errors))
				$redirect = true;
		}
		
	}
	
	$bdd->close();
}
else {
	$idValide = false;
}

if($redirect)
	echo $twig->render("visites_confirm_saisie.html", array("idclient" => $idClientInser, "dispo" => $_POST["dispo"]));
else
	echo $twig->render("visites_saisir.html", array("values" => $values, "idValide" => $idValide, "bien" => $bien, "nbVisite" => $nbVisite, "errors" => $errors));
