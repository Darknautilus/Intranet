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
    // Récupère toutes les surenchères
    $surencheres = $bdd->select("select idclient, montant from surencherir where idbien = ".$bien["idbien"].";");
    $nbCli = 0;
    $montantMax = 0;
    if($surencheres) {
      // Récupère le montant maximum et la liste des clients intéressés
      $clients = array();
      foreach($surencheres as $surenchere) {
        if(!in_array($surenchere["idclient"],$clients)) {
          $clients[] = $surenchere["idclient"];
          $nbCli++;
        }
        if($surenchere["montant"] > $montantMax)
          $montantMax = $surenchere["montant"];
      }
    }
    $bien["nbcli"] = $nbCli;
    $bien["montantmax"] = $montantMax;
  }
}

echo $twig->render("enchere_home.html", array("biens" => $biens));

$bdd->close();