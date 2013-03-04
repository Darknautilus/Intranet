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
    $values["titrebien"] = $_POST["titrebien"];
  if(empty($_POST["detailbien"]))
    $errors[] = "Veuillez entrer des détails pour ce bien";
  else
    $values["detailbien"] = $_POST["detailbien"];
  if(empty($_POST["adrbien"]))
    $errors[] = "Veuillez entrer un adresse";
  else
    $values["adrbien"] = $_POST["adrbien"];
  if(empty($_POST["prixbien"]) || $_POST["prixbien"] <= 0)
    $errors[] = "Veuillez entrer un prix positif";
  else
    $values["prixbien"] = $_POST["prixbien"];
  if(empty($_POST["idtype"]))
    $errors[] = "Veuillez sélectionner une catégorie";
  else
    $values["idtype"] = $_POST["idtype"];
  if(!isset($_FILES['photoBien']))
    $errors[] = "Veuillez sélectionner une image";
  
  if(empty($errors)) {
    
    //var_dump($_FILES["photoBien"]);
    
    // Récupération du fichier
    $dossier = images();
    $fichier = basename($_FILES["photoBien"]["name"]);
    $taille_maxi = 500000;
    $taille = filesize($_FILES["photoBien"]["tmp_name"]);
    $extensions = array(".png", ".gif", ".jpg", ".jpeg");
    $extension = strrchr($_FILES["photoBien"]["name"], ".");
    //Début des vérifications de sécurité...
    if(!in_array($extension, $extensions)) //Si l'extension est incorrecte
    {
      $errors[] = "Vous devez uploader une image";
    }
    if($taille>$taille_maxi)
    {
      $errors[] = 'Taille de fichier supérieure à 500ko';
    }
    if(empty($errors))
    {
      $idbien = $bdd->select("select max(idbien) as mid from bien;");
      $idbien = "b" . str_pad((intval(substr($idbien[0]["mid"], 1)) + 1), 4, '0', STR_PAD_LEFT);
      if(!resizeImage($_FILES["photoBien"], 153, 204, PATH_IMAGES, $idbien)) {
        $errors[] = "Echec de l'upload !";
      }
      else {
        $success[] = "Image sauvegardée";
        // Ajout dans la base
        if(!$bdd->insert("bien", array("idbien" => $idbien, "titrebien" => $values["titrebien"], "detailbien" => $values["detailbien"], "adrbien" => $values["adrbien"], "prixbien" => intval($values["prixbien"]), "idtype" => $values["idtype"], "photoBien" => $idbien.".jpg"))) {
          unlink(PATH_IMAGES."/".$idbien.".jpg");
          $errors[] = "Erreur insertion, image supprimée";
        }
        else {
          $success[] = "Bien enregistré";
          $values = array();
        }
      }
    }
    
  }
}

$bdd->close();

echo $twig->render("biens_ajouter.html",array("values" => $values, "errors" => $errors, "success" => $success, "types" => $types));