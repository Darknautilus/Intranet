<?php

$redirect = false;
$errors = array();
$values = array();

// Si l'on a rempli le formulaire et qu'on est pas loggué
if(isset($_POST["filled"]) && !isLogged()) {
  
  if(isset($_POST["client"])) {
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
  			// On controle le mot de passe
  			if($member[0]["emailclient"] != $_POST["email"]) {
  				$errors[] = "L'adresse email est incorrecte";
  			}
  		}
  	}
  }
  else if(isset($_POST["admin"])) {
    
    
  }
  
	// On définit les variables de session
	if(empty($errors)) {
	  
		$_SESSION["logged"] = true;
		$_SESSION["infos"] = $member[0];
		$GLOBALS["infos"] = $member[0];
		
		if(isset($_POST["cookie"])) {
		  creerCookie("logged", true);
		  creerCookie("infos",$member[0]);
		}
		
		// On redirige
		header("Location:".queries("admin", "home", array()));
	}
}

echo $twig->render("admin_connexion.html", array("values" => $values, "errors" =>  $errors));