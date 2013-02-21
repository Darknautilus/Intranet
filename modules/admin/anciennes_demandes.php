<?php

$client = getMembInfos();

if(isset($client["idclient"])) {
  $bdd = new BDD();
  $demandes = $bdd->select("select iddemande, datedemande from demande where idclient = ".$client["idclient"]." order by datedemande;");
  if(!$demandes)
    $demandes = array();
  $bdd->close();
  
  for($i=0;$i<count($demandes);$i++) {
    $demandes[$i]["datedemande"] = sqlDatetimeToFrench($demandes[$i]["datedemande"], "DATE", "MO_LETTERS");
  }
  
  echo $twig->render("admin_anciennes_demandes.html",array("demandes" => $demandes));
  
}
else {
  echo $twig->render("admin_home.html", array());
}