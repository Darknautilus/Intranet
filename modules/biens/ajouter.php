<?php

if(!isLogged())
  header("Location:".queries("","",array()));

$bdd = new BDD();
$types = $bdd->select("select idtype,nomtype from typebien;");

$values = array("titrebien" => "Villa Adams","detailbien" => "Superbe tombeau, colonies d'araignées, excellent voisinage","adrbien" => "Jardin des Orangers, Toulouse", "prixbien" => 23000,00);
$errors = array();
$success = array();

if(isset($_POST["filled"])) {
  
  if(empty($_POST["titrebien"]))
    $errors[] = "Veuillez entrer un titre";
  else
    $values = $_POST["titrebien"];
  if(empty($_POST["detailbien"]))
    $errors[] = "Veuillez entrer des détails pour ce bien";
  else
    $values = $_POST["detailbien"];
  if(empty($_POST["adrbien"]))
    $errors[] = "Veuillez entrer un adresse";
  else
    $values = $_POST["adrbien"];
  if(empty($_POST["prixbien"]))
    $errors[] = "Veuillez entrer un prix";
  else
    $values = $_POST["prixbien"];
  if(empty($_POST["idtype"]))
    $errors[] = "Veuillez sélectionner une catégorie";
  else
    $values = $_POST["idtype"];
  if(!isset($_FILES['photoBien']))
    $errors[] = "Veuillez sélectionner une image";
  
  if(empty($errors)) {
    
    // Récupération du fichier
    $dossier = images();
    $fichier = basename($_FILES['photoBien']['name']);
    $taille_maxi = 500000;
    $taille = filesize($_FILES['photoBien']['tmp_name']);
    $extensions = array('.png', '.gif', '.jpg', '.jpeg');
    $extension = strrchr($_FILES['photoBien']['name'], '.');
    //Début des vérifications de sécurité...
    if(!in_array($extension, $extensions)) //Si l'extension est incorrecte
    {
      $errors[] = "Vous devez uploader une image";
    }
    if($taille>$taille_maxi)
    {
      $errors[] = 'Taille de fichier supérieure à 500ko';
    }
    if(!isset($errors))
    {
      if(!resizeImage($_FILES["photoBien"], 153, 204, images())) {
        $errors[] = "Echec de l'upload !";
      }
      else {
        // Renommage du fichier
        rename(images()."/".$_FILES["photoBien"]["name"], images()."/"."test");
        $success[] = "Image sauvegardée";
        // Ajout dans la base
      }
    }
    
  }
}

$bdd->close();

echo $twig->render("biens_ajouter.html",array("values" => $values, "errors" => $errors, "success" => $success, "types" => $types));