<?php

if(isLogged() && isAdmin()) {
  if(!isset($_POST["ajax"])) {
    echo json_encode(array("result" => false));
  }
  else {
    $errors = array();
    $values = array();
    // Controle des valeurs
    if(empty($_POST["titrebien"]))
      $errors[] = "Veuillez entrer un titre";
    else
     $values["titrebien"] = $_POST["titrebien"];
    if(empty($_POST["adrbien"]))
      $errors[] = "Veuillez entrer une adresse";
    else
      $values["adrbien"] = $_POST["adrbien"];
    if(empty($_POST["detailbien"]))
      $errors[] = "Veuillez entrer des détails pour ce bien";
    else
      $values["detailbien"] = $_POST["detailbien"];
    if(empty($_POST["prixbien"]) || !is_numeric($_POST["prixbien"]) || $_POST["prixbien"] <= 0)
      $errors[] = "Veuillez entrer un prix";
    else
      $values["prixbien"] = $_POST["prixbien"];
    $values["idtype"] = $_POST["idtype"];
    $values["idbien"] = $_POST["idbien"];
    
    if(empty($errors)) {
      $bdd = new BDD();
      $bdd->update("bien", array("titrebien"=>$values["titrebien"],"adrbien"=>$values["adrbien"],"detailbien"=>$values["detailbien"],"prixbien"=>$values["prixbien"],"idtype"=>$values["idtype"]), array("idbien"=>$values["idbien"]));
      $nomtype = $bdd->select("select nomtype from typebien where idtype = '".$values["idtype"]."';");
      $values["nomtype"] = $nomtype[0]["nomtype"];
            
      echo json_encode(array("result" => true, "values" => $values));
    }
    else {
      echo json_encode(array("result" => false, "errors" => $errors, "values" => $values));
    }
  }
}