<?php

/*
	Base de donnees
*/

define("DBSERVER", "localhost");
define("DBNAME", "iut_intranet");
define("DBUSER", "root");
define("DBPASSWD", "root");

define("DBHEADER", 'mysql:host='.DBSERVER.';dbname='.DBNAME);

/*
	Chemins des repertoires
*/

define("PATH_ROOT", dirname(__FILE__));
define("PATH_MODULES", PATH_ROOT."/modules");
define("PATH_MODELES", PATH_ROOT."/modeles");
define("PATH_TEMPLATES", PATH_ROOT."/templates");

include(PATH_MODELES."/anti-spam.class.php");