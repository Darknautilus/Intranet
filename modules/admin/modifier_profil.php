<?php

function isValidMail($address) {
  if(!preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]_?])*@[[:alnum:]]([-.]?[[:alnum:]])+\.([a-z]{2,6})$`',
      $address))
  {
    return false;
  }
  else
  {
    return true;
  }
}

if(isLogged() && isClient()) {
  $errors = array();
  $values = getMembInfos();
  $modified = false;
  if(isset($_POST["filled"])) {
    // Controles de surface
    if(empty($_POST["nomclient"]) || strlen($_POST["nomclient"]) > 30)
      $errors[] = "Nom incorrect";
    else
      $values["nomclient"] = $_POST["nomclient"];
    
    if(empty($_POST["adrclient"]) || strlen($_POST["adrclient"]) > 50)
      $errors[] = "Adresse incorrecte";
    else
      $values["adrclient"] = $_POST["adrclient"];
      
    if(empty($_POST["telclient"]) || strlen($_POST["telclient"]) != 10 || !is_numeric($_POST["telclient"]))
      $errors[] = "Numéro de téléphone incorrect";
    else
      $values["telclient"] = $_POST["telclient"];
    
    if(empty($_POST["emailclient"]) || strlen($_POST["emailclient"]) > 20 || !isValidMail($_POST["emailclient"]))
      $errors[] = "Adresse email incorrecte";
    else
      $values["emailclient"] = $_POST["emailclient"];
    
    if(empty($errors)) {
      $bdd = new BDD();
      $req = $bdd->update("client", array("nomclient" => $values["nomclient"],"adrclient" => $values["adrclient"],"telclient" => $values["telclient"],"emailclient" => $values["emailclient"]), array("idclient" => $values["idclient"]));
      if(!$req) {
        $errors[] = "Erreur mise à jour";
      }
      else {
       updateInfos("infos", $values);
       $modified = true;
      }
    }
  }
  echo $twig->render("admin_modifier_profil.html",array("values" => $values, "errors" => $errors, "modified" => $modified));
}
else {
  header("Location:".queries("", "", array()));
}