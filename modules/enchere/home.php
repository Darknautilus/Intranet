<?php

$bdd = new BDD();

// Récupère les informations des biens mis aux enchères
$biens = $bdd->select("select b.idbien,b.titrebien,b.prixbien,b.photoBien,t.nomtype,e.prixdepart
                       from bien b, typebien t, enchere e
                       where b.idbien = e.idbien and t.idtype = b.idtype;");
if(!$biens) {
  $biens = array();
}
else {
  // Récupère le nombre de clients ayant sur-enchérit et cherche le montant maximum
  foreach($biens as &$bien) {
    $statsSur = getSurenchere($bien["idbien"]);
    $bien["nbcli"] = $statsSur["nbCli"];
    $bien["montantmax"] = $statsSur["montantMax"];
  }
}

echo $twig->render("enchere_home.html", array("biens" => $biens));

$bdd->close();