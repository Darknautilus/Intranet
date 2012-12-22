<?php

$checked = 1;// On sélectionne un choix par défaut

$result = false;// Permet de dire au template si le formulaire a été rempli ou non
$biens = null;// Liste des biens à afficher
$error = "";

// Si le formulaire a été rempli
if(isset($_POST["filled"]) && $_POST["filled"] == "true") {
	// On traite le formulaire
	// Préparation de la requête
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
	
	// Ouverture de la connexion
	$bdd = new BDD();
	// Exécution de la reuquête
	$biens = $bdd->select($requete);
	// Controle des erreurs
	if(!$biens)
		$error .= "Erreur lors de la récupération des biens : ".$bdd->getLastError();
	
	$bdd->close();
	
	$result = true;
}

// Appel du template
echo $twig->render("biens_afficher_prix.html", array("checked" => $checked, "result" => $result, "biens" => $biens, "error" => $error));