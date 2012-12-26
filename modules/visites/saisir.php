<?php

$bien = null;
$nbVisite = null;
$idValide = null;

// Controle du paramètre (id du bien)
$bdd = new BDD();
$bien = $bdd->select("select idbien from bien where idbien = '".$_GET["id"]."';");
$bdd->close();

// Controle du formulaire

if($bien != false && isset($_GET["id"]) && !empty($_GET["id"])) {
	
	$idValide = true;
	
	$bdd = new BDD();
	
	$bien = $bdd->select("select b.titrebien, b.detailbien, b.adrbien, b.prixbien, b.idtype, b.photobien, t.nomtype from bien b, typebien t where idbien = '".$_GET["id"]."' AND t.idtype = b.idtype;");
	
	$nbVisite = $bdd->select("select COUNT(*) as nbvisite from visiter v where v.idbien = '".$_GET["id"]."';");
	if(!$nbVisite)
		$nbVisite = 0;
	else
		$nbVisite = $nbVisite[0]["nbvisite"];
	
	$bdd->close();
}
else {
	$idValide = false;
}

echo $twig->render("visites_saisir.html", array("idValide" => $idValide, "bien" => $bien, "nbVisite" => $nbVisite));
