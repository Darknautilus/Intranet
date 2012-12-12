<?php

if(isset($_GET["checked"]) && $_GET["checked"] < 4)
	$checked = $_GET["checked"];
else
	$checked = 1;// Pour le radio sélectionné par défaut

$result = false;// Pour l'affichage des résultats
$biens = null;
$error = "";

if(isset($_SESSION["paramURL"]["formfill"]) && $_SESSION["paramURL"]["formfill"] == 1) {
	// on traite le formulaire
	$requete = "SELECT b.idbien, b.titrebien, b.detailbien, b.prixbien, b.photobien, t.nomtype FROM bien b, typebien t WHERE ";
	if($_POST["prix"] == "lt200k") {
		$requete .= "prixBien < 200000";
		$checked = 1;
	}
	else if($_POST["prix"] == "b200ka300k") {
		$requete .= "prixBien >= 200000 AND prixBien <= 300000";
		$checked = 2;
	}
	else if($_POST["prix"] == "gt300k") {
		$requete .= "prixBien > 300000";
		$checked = 3;
	}
	$requete .= " AND t.idtype = b.idtype;";
	
	$bdd = new BDD();
	$biens = $bdd->select($requete);
	
	if(!$biens)
		$error .= "Erreur lors de la récupération des biens : ".$bdd->getLastError();
	
	$bdd->close();
	
	$result = true;
}


echo $twig->render("biens_afficher_prix.html", array("checked" => $checked, "result" => $result, "biens" => $biens, "error" => $error));