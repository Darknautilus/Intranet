<?php

$biens = array();

if(isLogged() && isAdmin()) {
  $bdd = new BDD();
  $biens = $bdd->select("select idbien,titrebien,detailbien,adrbien,prixbien,b.idtype,t.nomtype from bien b,typebien t where b.idtype = t.idtype;");
  $types = $bdd->select("select idtype,nomtype from typebien;");
  $bdd->close();
}

echo $twig->render("biens_afficher.html", array("biens" => $biens, "types" => $types));