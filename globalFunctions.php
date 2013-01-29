<?php

function visitesCaddie() {
  if(isset($_SESSION["panier"]))
    return $_SESSION["panier"];
  else
    return false;
}

function visiteExiste($idbien) {
  if(isset($_SESSION["panier"])) {
    foreach($_SESSION["panier"] as $visite) {
      if($visite["idbien"] == $idbien)
        return $visite["priorite"];
    }
  }
  return false;
}