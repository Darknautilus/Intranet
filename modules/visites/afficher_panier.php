<?php
$visites = array();
if(isset($_SESSION["panier"])) {
  $bdd = new BDD();
  foreach ($_SESSION["panier"] as $idbien => $priorite) {
    $infosBien = $bdd->select("select titrebien, adrbien, detailbien, prixbien from bien where idbien = '".$idbien."';");
    $visites[] = array("idbien" => $idbien, "titrebien" => $infosBien[0]["titrebien"], "adrbien" => $infosBien[0]["adrbien"], "prixbien" => $infosBien[0]["prixbien"], "priorite" => $priorite);
  }
  $bdd->close();
}

echo $twig->render("visites_afficher_panier.html", array("visites" => $visites));