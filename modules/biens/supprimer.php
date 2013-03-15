<?php

if(isLogged() && isAdmin()) {
  if(!isset($_POST["ajax"])) {
    echo json_encode(array("result" => false));
  }
  else {
    $errors = array();
    $bdd = new BDD();
    $idbien = $bdd->select("select idbien from bien where idbien='".$_GET["idbien"]."';");
    if(!$idbien) {
      $errors[] = "Le bien spécifié est inconnu";
    }
    
    if(empty($errors)) {
      $result = $bdd->delete("bien", array("idbien" => $idbien[0]["idbien"]));
      if(!$result) {
        $errors[] = "Erreur suppression bien";
      }
      $bdd->close();
      if(empty($errors)) {
        echo json_encode(array("result" => true, "idbien" => $idbien[0]["idbien"]));
      }
      else {
        echo json_encode(array("result" => false, "errors" => $errors, "idbien" => $_GET["idbien"]));
      }
    }
    else {
      echo json_encode(array("result" => false, "errors" => $errors, "idbien" => $_GET["idbien"]));
    }
  }
}