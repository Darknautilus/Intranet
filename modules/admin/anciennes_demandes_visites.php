<?php

// Si la page est appelée en AJAX
if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
  $bdd = new BDD();
  $biens = $bdd->select("select b.titrebien, b.prixbien, b.photoBien from bien b, visiter v where v.iddemande = ".$_GET["demande"]." and v.idbien = b.idbien order by v.priorite;");
  if(!$biens) {
    echo "Erreur";
  }
  else {
    echo $twig->render("admin_anciennes_demandes_visites.html", array("biens" => $biens));
  }
}