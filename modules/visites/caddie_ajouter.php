<?php

if(isset($_POST["idbien"]) && isset($_POST["priorite"])) {
  $priorite = visiteExiste($_POST["idbien"]);
  if($priorite == false) {
    $_SESSION["panier"][] = array("idbien" => $_POST["idbien"], "priorite" => $_POST["priorite"]);
    echo json_encode(array("result" => true, "priorite" => $_POST["priorite"]));
  }
  else {
    echo json_encode(array("result" => false, "priorite" => $priorite));
  }
}