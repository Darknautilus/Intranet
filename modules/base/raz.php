<?php

$errors[] = array();

$bdd = new BDD();
$bdd->select("COMMIT;");

// Récupération des photos des biens
$dossierBak = PATH_IMAGES."/photosBien.bak";
if($dossierBak = opendir($dossierBak)) {
  while($photo = readdir($dossierBak)) {
    if($photo != "." && $photo != "..") {
      copy(PATH_IMAGES."/photosBien.bak/".$photo, PATH_IMAGES."/".$photo);
    }
  }
  closedir($dossierBak);
}

$fichier = file_get_contents(root()."/docs/RazBase.sql"); // on charge le fichier SQL
// foreach($sql as $l){ // on le lit
// 	if (substr(trim($l),0,2)!="--"){ // suppression des commentaires
// 		$requetes .= $l;
// 	}
// }
try {
	$bdd->getBDD()->exec($fichier);
}
catch (PDOException $e) {
	$errors[] = "Erreur de lecture du fichier : ".$e->getMessage();
	$bdd->select("ROLLBACK;");
}
 
// $reqs = split(";\n",$requetes);// on sépare les requêtes
// print_r($reqs);
// foreach($reqs as $req){ // et on les éxécute
// 	if (!$bdd->select($req) && trim($req)!=""){
// 		$errors[] = "Erreur de lecture du fichier";
// 		$bdd->select("ROLLBACK;");
// 		break;
// 	}
// 	var_dump($req);
// }
$bdd->close();

echo $twig->render("base_raz.html", array());