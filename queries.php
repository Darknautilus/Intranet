<?php

/*
	Module affiché par défaut
*/
define("DEFAULT_MODULE", "index");

/*
	Action par défaut pour chaque module
	array(module => action)
*/
$DEFAULT_ACTION = array(
	"index" => "show",
	"biens" => "afficher",
	"visites" => "saisir",
	"base" => "afficher",
  "admin" => "home",
  "stats" => "total_ventes",
  "enchere" => "home"
);

$GLOBALS["DEFAULT_ACTION"] = $DEFAULT_ACTION;

/*
	Liste des modules et des actions
	array(module => array(action [,action...]) )
*/
$MODULES = array(
	"index" => array(
		"show"
		),
	"biens" => array(
		"afficher",
		"afficher_prix",
		"afficher_cat",
		"afficher_detail",
		"rechercher",
	  "ajouter",
	  "modifier",
	  "supprimer"
		),
	"visites" => array(
		"saisir",
		"controle_saisie",
	  "caddie_gerer",
	  "afficher_panier"
		),
	"base" => array(
		"afficher",
		"raz"
		),
  "admin" => array(
    "home",
    "deconnexion",
    "connexion",
    "anciennes_demandes",
    "anciennes_demandes_visites",
    "modifier_profil"
    ),
  "stats" => array(
    "total_ventes"
    ),
  "enchere" => array(
    "home",
    "rencherir",
    "getStatsSurenchere"
    )
	);

$GLOBALS["MODULES"] = $MODULES;

/*
	Fichiers de configuration des modules
	array(module => nom_fichier)
*/
$MODULES_CONFIG = array(
	"visites" => "config",
	"base" => "config",
  "admin" => "filter"
);

$GLOBALS["MODULES_CONFIG"] = $MODULES_CONFIG;

// ===================== -v- Ne pas toucher -v- ==================

/*
	Fonction de controles des modules et actions
*/
function is_module($pModule)
{
	foreach($GLOBALS["MODULES"] as $module => $action)
	{
		if($pModule == $module)
			return true;
	}
	
	return false;
}

function is_action($pModule, $pAction)
{
	foreach($GLOBALS["MODULES"] as $module => $tabAction)
	{
		if($pModule == $module)
		{
			foreach($tabAction as $action)
			{
				if($action == $pAction)
					return true;
			}
		}
	}
	
	return false;
}

function default_module()
{
	return DEFAULT_MODULE;
}

function default_action($pModule)
{
	return $GLOBALS["DEFAULT_ACTION"][$pModule];
}

function is_config($pModule)
{
	foreach($GLOBALS["MODULES_CONFIG"] as $module => $config)
	{
		if($module == $pModule && $config != null)
			return true;
	}
	
	return false;
}

function configFile($pModule)
{
	return $GLOBALS["MODULES_CONFIG"][$pModule];
}