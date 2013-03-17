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
      
      if(!$bdd->delete("ressembler", array("idbien1" => $bien["idbien"], "idbien2" => $bien["idbien"]))) {
        $errors[] = "Erreur suppression ressemblance";
      }
      if(!$bdd->delete("visiter", array("idbien" => $bien["idbien"]))) {
        $errors[] = "Erreur suppression visite";
      }
      if(!$bdd->delete("bien", array("idbien" => $bien["idbien"]))) {
        $errors[] = "Erreur suppression bien";
      }
      
      // Suppression de la photo
      if(!unlink(PATH_IMAGES."/".$bien["photoBien"])) {
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