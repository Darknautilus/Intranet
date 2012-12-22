<?php

$error = "";

$bdd = new BDD();

$bien = $bdd->select("select b.titrebien, b.detailbien, b.adrbien, b.prixbien, b.idtype, b.photobien, t.nomtype from bien b, typebien t where idbien = '".$_GET["id"]."' AND t.idtype = b.idtype;");
if(!$bien)
	$error .= $bdd->getLastError()." : table bien";

$nbVisite = $bdd->select("select COUNT(*) as nbvisite from visiter v where v.idbien = '".$_GET["id"]."';");
if(!$nbVisite)
	$nbVisite = 0;
else
	$nbVisite = $nbVisite[0]["nbvisite"];

$bienRessemb = $bdd->select("select b.idbien, b.titrebien, b.photobien from ressembler r, bien b where r.idbien1 = '".$_GET["id"]."' and b.idbien = r.idbien2;");

$bdd->close();

echo $twig->render("biens_afficher_detail.html", array("bien" => $bien, "nbVisite" => $nbVisite, "bienRessemb" => $bienRessemb, "error" => $error));