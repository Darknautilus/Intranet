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

/*
 * Redimensionne une image et l'exporte en jpg dans le dossier donné
 * code initial par Florentin Le Moal, boosté par Aurélien Bertron
 */
function resizeImage($imgSrc, $height, $width, $dir, $name) {
  // Récupération des informations de l'image uploadée
  $extension = strrchr($imgSrc["name"], '.');
  list($width_orig, $height_orig) = getimagesize($imgSrc["tmp_name"]);
  // Importation de l'image uploadée pour la manipuler
  switch ($extension) {
    case '.jpg':
    case '.jpeg':
      $src_image = imagecreatefromjpeg($imgSrc["tmp_name"]);
      break;
  
    case '.png':
      $src_image = imagecreatefrompng($imgSrc["tmp_name"]);
      break;
  
    case '.gif':
      $src_image = imagecreatefromgif($imgSrc["tmp_name"]);
      break;
    default:
      return false;
      break;
  }
  // Taille de l'image finale et position du morceau collé
  $dst_x = 0;
  $dst_y = 0;
  $dst_w = $width;
  $dst_h = $height;
  // Détermination de la portion d'image à récupérer
  if ($width_orig/$height_orig < $dst_w/$dst_h) {
    $src_w = $width_orig;
    $src_h = $width_orig * ($dst_h/$dst_w);
    $src_x = 0;
    $src_y = ($height_orig/2)-($src_h/2);
  }
  else {
    $src_w = $height_orig * ($dst_w/$dst_h);
    $src_h = $height_orig;
    $src_x = ($width_orig/2)-($src_w/2);
    $src_y = 0;
  }
  // Création de l'image finale
  $dst_image = imagecreatetruecolor($dst_w, $dst_h);
  // Copie de la portion souhaitée
  imagecopyresampled ($dst_image, $src_image , $dst_x , $dst_y ,$src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h);
  // Exporte l'image en jpg
  imagejpeg($dst_image,$dir."/".$name.".jpg",100);
  // Destruction des images pour libérer de la mémoire
  imagedestroy($dst_image);
  imagedestroy($src_image);
  return true;
}



// Récupère le nombre de clients ayant surenchéri sur un bien donné, et l'enchère maximale
function getSurenchere($idbien) {
  $bdd = new BDD();
  $enchere = $bdd->select("select prixdepart from enchere where idbien = '".$idbien."';");
  $surencheres = $bdd->select("select idclient, montant from surencherir where idbien = '".$idbien."';");
  $nbCli = 0;
  $montantMax = $enchere[0]["prixdepart"];
  if($surencheres) {
    // Récupère le montant maximum et la liste des clients intéressés
    $clients = array();
    foreach($surencheres as $surenchere) {
      if(!in_array($surenchere["idclient"],$clients)) {
        $clients[] = $surenchere["idclient"];
        $nbCli++;
      }
      if($surenchere["montant"] > $montantMax)
        $montantMax = $surenchere["montant"];
    }
  }
  $bdd->close();
  return array("nbCli" => $nbCli, "montantMax" => $montantMax);
}
