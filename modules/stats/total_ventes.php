<?php

$bdd = new BDD();
if(isset($_POST["ajax"])) {
  if($_POST["section"] == "montant") {
    $prix = $bdd->select("select prixbien from bien;");
    $total = 0;
    if($prix) {
      foreach ($prix as $pri)
        $total += $pri["prixbien"];
    }
    echo json_encode(array("montantTotal" => $total, "montantCom" => $total*(10/100)));
  }
  else if($_POST["section"] == "montantCat") {
    $prix = $bdd->select("select prixbien from bien where idtype = '".$_POST["type"]."';");
    $total = 0;
    if($prix) {
      foreach ($prix as $pri)
        $total += $pri["prixbien"];
    }
    echo json_encode(array("montantTotal" => $total, "montantCom" => $total*(10/100)));
  }
  else if($_POST["section"] == "affDetails") {
    $bien = $bdd->select("select b.idbien,b.titrebien,b.adrbien,b.detailbien,t.nomtype from bien b,typebien t where b.idbien = '".$_POST["idbien"]."' and t.idtype = b.idtype;");
    if(!$bien) {
      echo json_encode(array("result" => false));
    }
    else {
      $bien = $bien[0];
      echo json_encode(array("result" => true, "bien" => $bien, "urlDetails" => queries("biens", "afficher_detail", array("id" => $bien["idbien"]))));
    }
  }
}
else {
  $types = $bdd->select("select idtype,nomtype from typebien;");
  echo $twig->render("stats_total_ventes.html", array("types" => $types));
}
$bdd->close();