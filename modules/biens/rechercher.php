<?php

$result = false;
$biens = null;
$errors = array();
$value = "";

if(isset($_POST["filled"]) && $_POST["filled"] == true) {
	// Formulaire rempli
	// Controle du nombre de mots
	$mots = explode(" ",$_POST["search"]);
	$nbMots = count($mots);
	if(empty($_POST["search"]))
		$errors[] = "Vous devez au moins entrer un mot !";
	else if($nbMots > 3)
		$errors[] = "Vous devez entrer trois mots au maximum !";
	
	if(empty($errors)) {
		foreach($mots as $mot) {
			if(empty($mot)) {
				$errors[] = "N'essaye pas de me berner, petit malin !";
				break;
			}
		}
	}

	if(empty($errors)) {
		$bdd = new BDD();
		
		// Préparation à la mise en gras des mots recherchés
		$patterns = array();
		$replacements = array();
		foreach($mots as $mot) {
			$patterns[] = "/".$mot."/";
			$replacements[] = "<strong>".$mot."</strong>";
		}

		$requeteAND = "SELECT b.idbien, b.detailbien, b.photoBien FROM bien b WHERE b.detailbien LIKE '%".$mots[0]."%'";
		$requeteOR = "SELECT b.idbien, b.detailbien, b.photoBien FROM bien b WHERE b.detailbien LIKE '%".$mots[0]."%'";

		array_shift($mots);

		foreach ($mots as $mot) {
			$requeteAND .= " AND b.detailbien LIKE '%".$mot."%'";
			$requeteOR .= " OR b.detailbien LIKE '%".$mot."%'";
		}

		$requeteAND .= ";";
		$requeteOR .= ";";
		
		$biensAND = $bdd->select($requeteAND);
		$biensOR = $bdd->select($requeteOR);
		if(!$biensOR)
			$errors[] = "Aucun bien trouvé";

		/*
		 * Permet de fusionner les deux tableaux (AND et OR) :
		 * 	On met les AND en premier et on ajoute les OR qui ne sont pas déjà ajoutés.
		 * 	S'il n'y a pas de AND, on ajoute directement les OR.
		 */
		if(!$biensAND) {
			$biens = $biensOR;
		}
		else {
			$biens = $biensAND;
			foreach($biensOR as $bienO) {
				$exist = false;
				for($i = 0;$i<count($biensAND) && $exist == false;$i++) {
					if($biensAND[$i]["idbien"] == $bienO["idbien"])
						$exist = true;
				}
				if(!$exist)
					$biens[] = $bienO;
			}
		}
	
		$bdd->close();
		
		
		for ($i = 0; $i < count($biens); $i++) {
			$biens[$i]["detailbien"] = preg_replace($patterns, $replacements, $biens[$i]["detailbien"]);
		}
		
		$result = true;
	}
	
	$value = $_POST["search"];
}

echo $twig->render("biens_rechercher.html", array("biens" => $biens, "value" => $value, "result" => $result, "errors" => $errors));