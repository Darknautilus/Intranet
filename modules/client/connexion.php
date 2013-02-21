<?php

$redirect = false;
$errors = array();
$values = array();

// Si l'on a rempli le formulaire et qu'on est pas loggué
if(isset($_POST["filled"]) && !isLogged()) {
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
		$client = $bdd->select("SELECT idclient, emailclient, nomclient, adrclient, telclient FROM client WHERE idclient = ".$_POST["numclient"].";");
		$bdd->close();
		
		if(!$client) {
				$errors[] = "Cet identifiant client n'existe pas";
		}
		else {
			// On controle le mot de passe
			if($client[0]["emailclient"] != $_POST["email"]) {
				$errors[] = "L'adresse email est incorrecte";
			}
		}
	}
	// On définit les variables de session
	if(empty($errors)) {
	  
		$_SESSION["logged"] = true;
		$_SESSION["infoscli"] = $client[0];
		$GLOBALS["infoscli"] = $client[0];
		
		if(isset($_POST["cookie"])) {
		  creerCookie("logged", true);
		  creerCookie("infoscli",$client[0]);
		}
		
		// On redirige
		// $redirect = true;
		header("Location:".queries("admin", "home", array()));
	}
}

if($redirect)
  echo $twig->render("admin_home.html", array());
else
  echo $twig->render("client_connexion.html", array("values" => $values, "errors" =>  $errors));