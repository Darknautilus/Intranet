<?php

$error = "";

$bdd = new BDD();

$bien = $bdd->select("select b.titrebien, b.detailbien, b.adrbien, b.prixbien, b.idtype, b.photobien, t.nomtype from bien b, typebien t where idbien = '".$_GET["id"]."' AND t.idtype = b.idtype;");

if(!$bien) {
	$error .= $bdd->getLastError();
}

$bdd->close();

echo $twig->render("biens_afficher_detail.html", array("bien" => $bien, "error" => $error, "checked" => $_GET["checked"]));