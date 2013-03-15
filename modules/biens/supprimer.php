<?php

if(isLogged() && isAdmin()) {
  if(!isset($_POST["ajax"])) {
    echo json_encode(array("result" => false));
  }
  else {
    $errors = array();
    $bdd = new BDD();
    $bien = $bdd->select("select idbien, photoBien from bien where idbien='".$_GET["idbien"]."';");
    if(!$bien) {
      $errors[] = "Le bien spécifié est inconnu";
    }
    else {
      $bien = $bien[0];
    }
    
    if(empty($errors)) {
      
      $req = array();
      
      $result = $bdd->delete("ressembler", array("idbien1" => $bien["idbien"], "idbien2" => $bien["idbien"]));
      if(!$result) {
        $errors[] = "Erreur suppression ressemblance";
      }
      $req[] = $bdd->getLastReq();
      $result = $bdd->delete("visiter", array("idbien" => $bien["idbien"]));
      if(!$result) {
        $errors[] = "Erreur suppression visite";
      }
      $req[] = $bdd->getLastReq();
      $result = $bdd->delete("bien", array("idbien" => $bien["idbien"]));
      if(!$result) {
        $errors[] = "Erreur suppression bien";
      }
      $req[] = $bdd->getLastReq();
      
      // Suppression de la photo
      $result = unlink(PATH_IMAGES."/".$bien["photoBien"]);
      if(!$result) {
        $errors[] = "Erreur suppression image";
      }
      if(empty($errors)) {
        echo json_encode(array("result" => true, "idbien" => $bien["idbien"]));
      }
      else {
        echo json_encode(array("result" => false, "errors" => $errors));
      }
    }
    else {
      echo json_encode(array("result" => false, "errors" => $errors));
    }
    $bdd->close();
  }
}