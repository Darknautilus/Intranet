<?php

$redirect = false;
$errors = array();
$values = array();

if(isset($_GET["membtype"]))
  $membtype = $_GET["membtype"];
else
  $membtype = null;

// Si l'on a rempli le formulaire et qu'on est pas loggué
if(isset($_POST["filled"]) && !isLogged()) {
  
  if(isset($_POST["client"])) {
    $membtype = "client";
    
  	// Controles de surface
  	if(empty($_POST["numclient"])) {
  		$errors[] = "Veuillez entrer un numéro client.";
  	}
  	else {
  		$values["numclient"] = $_POST["numclient"];
  		if(empty($_POST["email"]))
  			$errors[] = "Veuillez entrer une adresse email valide.";
  		else
  		  $values["email"] = $_POST["email"];
  	}
  	
  	// Controles en profondeur
  	if(empty($errors)) {
  		// On teste la présence de l'email dans la base
  		$bdd = new BDD();
  		$member = $bdd->select("SELECT idclient, emailclient, nomclient, adrclient, telclient FROM client WHERE idclient = ".$_POST["numclient"].";");
  		$bdd->close();
  		
  		if(!$member) {
  				$errors[] = "Cet identifiant client n'existe pas";
  		}
  		else {
  			// On controle l'adresse email
  			if($member[0]["emailclient"] != $_POST["email"]) {
  				$errors[] = "L'adresse email est incorrecte";
  			}
  			// On rajoute un champ pour différencier le client de l'admin
  			$member[0]["client"] = true;
  			$infos = $member[0];
  		}
  	}
  }
  else if(isset($_POST["admin"])) {
    $membtype = "admin";
    
    // Controles de surface
    if(empty($_POST["nomcompte"])) {
      $errors[] = "Veuillez entrer un nom de compte.";
    }
    else {
      $values["nomcompte"] = $_POST["nomcompte"];
      if(empty($_POST["password"]))
        $errors[] = "Veuillez entrer un mot de passe.";
      else
        $values["password"] = $_POST["password"];
    }
    
    // Controles en profondeur
    if(empty($errors)) {
      $infos = array();
      if($values["nomcompte"] != "adm") {
        $errors[] = "Nom de compte inconnu";
      }
      else {
        if($values["password"] != "adm") {
          $errors[] = "Mot de passe inconnu";
        }
        else {
          $infos["nomcompte"] = $values["nomcompte"];
          $infos["admin"] = true;
        }
      }
      
    }
    
  }
  
	// On définit les variables de session
	if(empty($errors)) {
	  
		$_SESSION["logged"] = true;
		$_SESSION["infos"] = $infos;
		$GLOBALS["infos"] = $infos;
		
		if(isset($_POST["cookie"])) {
		  creerCookie("logged", true);
		  creerCookie("infos",$infos);
		}
		
		// On redirige
		header("Location:".queries("admin", "home", array()));
	}
}

echo $twig->render("admin_connexion.html", array("values" => $values, "errors" =>  $errors, "membtype" => $membtype));