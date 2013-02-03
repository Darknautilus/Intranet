<?php

if(isset($_POST["filled"]) && isset($_POST["idbien"])) {
  $priorite = visiteExiste($_POST["idbien"]);
  $nbVisites = getNbVisitesCaddie();
  if(isset($_POST["modify"]) && isset($_POST["priorite"])) { 
    // Cas d'une modification
    if($priorite == false) {
      echo json_encode(array("result" => false, "priorite" => $priorite, "modify" => "modified"));
    }
    else {
      modifierVisite($_POST["idbien"], $_POST["priorite"]);
      echo json_encode(array("result" => true, "priorite" => $_POST["priorite"], "modify" => "modified"));
    }
  }
  else if(!isset($_POST["modify"])) {
    // Cas d'un ajout
    if($priorite == false) {
      ajouterVisite($_POST["idbien"], $_POST["priorite"]);
      echo json_encode(array("result" => true, "priorite" => $_POST["priorite"], "nbVisites" => $nbVisites+1));
    }
    else {
      echo json_encode(array("result" => false, "priorite" => $priorite));
    }
  }
  else {
    // Cas d'une suppression
    if($priorite == false) {
      echo json_encode(array("result" => false, "modify" => "deleted"));
    }
    else {
      modifierVisite($_POST["idbien"]);
      echo json_encode(array("result" => true, "modify" => "deleted", "nbVisites" => $nbVisites-1));
    }
  }
}