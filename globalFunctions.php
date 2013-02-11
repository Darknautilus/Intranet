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

function majGlobals() {
  if(isset($_COOKIE["logged"])) {
    $GLOBALS["logged"] = $_COOKIE["logged"];
    $GLOBALS["infoscli"] = getCookie("infoscli");
  }
  else if(isset($_SESSION["logged"])) {
    $GLOBALS["logged"] = $_SESSION["logged"];
    $GLOBALS["infoscli"] = $_SESSION["infoscli"];
  }
  else {
    $GLOBALS["logged"] = false;
  }
}
majGlobals();

function creerCookie($name, $value) {
  if(is_array($value)) {
    $cookie = "";
    foreach ($value as $key => $field) {
      $cookie .= $key."=".$field.";;";
    }
  }
  else {
    $cookie = $value;
  }
  setcookie($name, urlencode($cookie), time()+3600*24*365);
}

function getCookie($name) {
  if(!isset($_COOKIE[$name])) {
    return false;
  }
  else if(!strpos(urldecode($_COOKIE[$name]), ";;")) {
    return urldecode($_COOKIE[$name]);
  }
  else {
    $values = array();
    $couples = explode(";;", urldecode($_COOKIE[$name]));
    foreach($couples as $couple) {
      $list = explode("=",$couple);
      if(!empty($list[0]))
        $values[$list[0]] = $list[1];
    }
    return $values;
  }
}

