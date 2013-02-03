<?php

function visitesCaddie() {
  if(isset($_SESSION["panier"]))
    return $_SESSION["panier"];
  else
    return false;
}

function visiteExiste($idbien) {
  if(isset($_SESSION["panier"][$idbien]))
    $priorite = $_SESSION["panier"][$idbien];
  else
    $priorite = false;
  return $priorite;
}

function ajouterVisite($idbien, $priorite) {
  $_SESSION["panier"][$idbien] = $priorite;
}

function modifierVisite($idbien, $priorite = null) {
  if($priorite == null)
    unset($_SESSION["panier"][$idbien]);
  else
    $_SESSION["panier"][$idbien] = $priorite;
}

