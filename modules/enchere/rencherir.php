<?php

$bdd = new BDD();
$errors = array();

if(isset($_GET["idbien"]) && $bdd->exists("enchere", "idbien", $_GET["idbien"])) {
  
  $bien = $bdd->select("select b.idbien,b.titrebien,b.detailbien,b.prixbien,b.photoBien,t.nomtype
                        from bien b, typebien t
                        where t.idtype = b.idtype and b.idbien = '".$_GET["idbien"]."';");
  $bien = $bien[0];
  
  $enchere = $bdd->select("select debut,fin,prixdepart from enchere where idbien = '".$_GET["idbien"]."';");
  $enchere = $enchere[0];
  
  if(isset($_POST["filled"])) {
    if(!$bdd->exists("client","idclient",$_POST["idclient"])) {
      $errors[] = "Client inconnu";
    }
    else {
      $surenchere = getSurenchere($_GET["idbien"]);
      if($_POST["enchere"] <= $surenchere["montantMax"]) {
        $errors[] = "Montant enchère incorrect";
      }
      else {
        if(strtotime($enchere["fin"]) <= strtotime(date("Y-m-d H:i:s"))) {
          $errors[] = "Temps dépassé";
        }
      }
    }
    
    if(empty($errors)) {
      $bdd->insert("surencherir", array("idbien" => $_GET["idbien"],"idclient" => $_POST["idclient"],"dateenchere" => date("Y-m-d H:i:s"), "montant" => $_POST["enchere"]));
    }
  }
  
  
  echo $twig->render("enchere_rencherir.html",array("bien" => $bien,"enchere" => $enchere,"errors" => $errors));
}
else {
  $bdd->close();
  header("Location:".queries("enchere", "", array()));
}
