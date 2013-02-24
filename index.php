<?php

/*
 Initialisation des sessions
*/

session_start();

/*
	Gestion des redirections URL
	Pour enregistrer une page où rediriger : rajouter le paramètre GET &redirect=true
*/
function redirectURL()
{
	$url = "./index.php?module=".$_SESSION["module"]."&action=".$_SESSION["action"];
	foreach($_SESSION["paramURL"] as $key => $value)
		$url .= "&".$key."=".$value;

	return $url;
}

/*
	Récupération des fichiers de configuration
*/
include_once("./config.php");
include_once("./queries.php");

/*
	Initialisation du moteur de templates
*/
include_once("./loadTwig.php");

/*
	Désactivation des guillemets magiques
*/
if(get_magic_quotes_gpc())
{
        $_POST = array_map('stripslashes', $_POST);
        $_GET = array_map('stripslashes', $_GET);
        $_COOKIE = array_map('stripslashes', $_COOKIE);
}

/*
	Sécurisation des formulaires
*/
$_POST = array_map("htmlspecialchars", $_POST);
$_GET = array_map("htmlspecialchars", $_GET);
$_COOKIE = array_map("htmlspecialchars", $_COOKIE);

/*
 * Inclusion des modeles
 */
include_once(PATH_MODELES."/bdd.class.php");
include_once(PATH_MODELES."/anti-spam.class.php");

/*
	Démarrage de la temporisation de sortie
*/
ob_start();

if(!isset($_GET["module"]) || !is_module($_GET["module"]))
	$module = default_module();
else
	$module = $_GET["module"];

	
if(!isset($_GET["action"]) || !is_action($module, $_GET["action"]))
		$action = default_action($module);
else
		$action = $_GET["action"];

if(!isset($_GET["redirect"]) || $_GET["redirect"] != true)
{
	$_SESSION["module"] = $module;
	$_SESSION["action"] = $action;
	$_SESSION["paramURL"] = array();
	foreach($_GET as $key => $value)
	{
		if($key != "module" && $key != "action" && $key != "redirect")
			$_SESSION["paramURL"][$key] = $value;
	}
}

/*var_dump($module);
var_dump($action);
var_dump($_GET);
var_dump($_SESSION);*/

if(is_config($module))
{
	$chemin_include = PATH_MODULES."/".$module."/".configFile($module).".php";
	include($chemin_include);
}

$chemin_include = PATH_MODULES."/".$module."/".$action.".php";
include($chemin_include);

/*
	Récupération du contenu et fin de la temporisation
*/
$content = ob_get_contents();
ob_end_clean();

/*
	Affichage final
*/
echo $content;

?>