<?php

function majGlobals() {
  if(isset($_COOKIE["logged"])) {
    $GLOBALS["logged"] = $_COOKIE["logged"];
    $GLOBALS["infos"] = getCookie("infos");
  }
  else if(isset($_SESSION["logged"])) {
    $GLOBALS["logged"] = $_SESSION["logged"];
    $GLOBALS["infos"] = $_SESSION["infos"];
  }
  else {
    $GLOBALS["logged"] = false;
  }

//   if($GLOBALS["logged"]) {
//     if(!isset($_SESSION["grpMb"])) {
//       majGrpMb();
//       majGrpMbPlus();
//     }
//     $GLOBALS["grpMb"] = $_SESSION["grpMb"];
//     $GLOBALS["grpMbPlus"] = $_SESSION["grpMbPlus"];
//   }
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

function updateInfos($id, $content) {
  // On vérifie que l'info voulue existe
  if(isset($GLOBALS[$id])) {
    // On modifie l'info dans chaque conteneur (session et cookie)
    if(isset($_SESSION[$id])) {
      foreach($content as $field => $value) {
        $_SESSION[$id][$field] = $value;
      }
    }
    $cookieInfos = getCookie($id);
    if($cookieInfos) {
      foreach ($content as $field => $value) {
        if(isset($cookieInfos[$field]))
          $cookieInfos[$field] = $value;
      }
      creerCookie($id, $cookieInfos);
    }
    majGlobals();
    return true;
  }
  else {
    return false;
  }
}

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

/*
 * @param $datetime La donnée datetime de la base
 * @param $ret ALL : date et heure, DATE : seulement date, TIME : seulement heure
 * @param $dateformat défaut : Séparateur de la date, MO_LETTERS : le mois en lettres
 * @param $timeformat Séparateur de l'heure
 */
function sqlDatetimeToFrench($datetime, $ret = "ALL", $dateformat = "/", $timeformat = ":") {
  
  $months = array("janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
  
  list($date,$time) = explode(" ", $datetime);
  list($year,$month,$day) = explode("-", $date);
  list($hour,$minute,$second) = explode(":", $time);
    
  if($dateformat == "MO_LETTERS")
    $date = $day." ".$months[(int)$month]." ".$year;
  else
    $date = $day.$dateformat.$month.$dateformat.$year;
  
  $time = $hour.$timeformat.$minute.$timeformat.$second;
  
  if($ret == "ALL")
    return array($date,$time);
  else if($ret == "DATE")
    return $date;
  else if($ret == "TIME")
    return $time;
  else
    return false;
}

