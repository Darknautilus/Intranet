<?php

/*
 * Classe de connexion à la base de données
 * Utilise PDO (veillez à bien l'activer sur le serveur !)
 */

class BDD {
	
private $errors = array();
private $bdd = NULL;
private $lastError;
private $tables;

/*
 * Constructeur de la classe :
 * crée l'objet de connexion à la base
 */
function __construct() {
	try {
		$this->bdd = new PDO(DBHEADER, DBUSER, DBPASSWD);
		$this->bdd->exec("SET NAMES UTF8");
		
		// Remplissage de la liste des tables
		$this->tables = array();
		$tables = $this->select("SHOW TABLES;");
		if($tables != false) {
		  foreach($tables as $elem) {
		    $this->tables[] = $elem["Tables_in_".DBNAME];
		  }
		}
		
		
	}
	catch (PDOException $e) {
		$this->errors[] = $e->getMessage();
		$this->bdd = NULL;
	}
}

function getBDD() {
	return $this->bdd;
}

function getLastError() {
	return $this->lastError;
}

function getTables() {
  return $this->tables;
}

/*
 * Ferme la connexion et retourne les éventuelles erreurs survenues
 */
function close() {
	$this->bdd = NULL;
	return $this->errors;
}
	
/*
 * Pour faire un SELECT sur la table.
 * Paramètres :
 * 		$requete : une requête SQL classique
 * 
 * Retourne le tableau des résultats. Chaque enregistrement est un élément du tableau et est présenté sous la forme d'un tableau associatif de la forme champ => valeur. 
 */
function select ($requete) {
	try {
		$result = $this->bdd->query($requete);
		if(!$result) {
			$err = "Empty SELECT";
			$this->lastError = $err;
			$this->errors[] = $this->lastError;
			return false;
		}
		
		$lines = array();
		while($line = $result->fetch(PDO::FETCH_ASSOC)) {
			$lines[] = $line;
		}
		if(empty($lines)) {
			$err = "Empty SELECT";
			$this->lastError = $err;
			$this->errors[] = $this->lastError;
			return false;
		}
		return $lines;
	}
	catch (PDOException $e) {
		$this->lastError = $e->getMessage();
		$this->errors[] = $this->lastError;
		return false;
	}
}

/*
 * Effectue un update sur une table.
 * Paramètres :
 * 		$table : la table où effectuer l'update
 * 		$colonnes : tableau associatif de la forme champ => valeur
 * 		$conditions : idem, conditions après le WHERE
 * 
 * Retourne true si l'update s'est fait correctement, et false sinon
 */
function update ($table, $colonnes, $conditions) {
	
	$colonnes_ = array() ;
	foreach($colonnes as $colonne => $valeur) {
		if (!is_numeric($valeur)) {
			$valeur = $this->bdd->quote($valeur) ;
		}
		$colonnes_[] = "$colonne = $valeur" ;
	}
 
	$conditions_ = array() ;
	foreach($conditions as $condition => $valeur) {
		if (!is_numeric($valeur)) {
			$valeur = $this->bdd->quote($valeur) ;
		}
		$conditions_[] = "$condition = $valeur" ;
	}
 
	$sql = "UPDATE $table SET " ;
	$sql .= join(', ', $colonnes_) ;
	$sql .= ' WHERE ' . join(' AND ', $conditions_) ;
	
	//var_dump($sql);
 
	try {
		$resultat = $this->bdd->exec($sql);
		return true;
	}
	catch (PDOException $e) {
		$this->lastError = $e->getMessage();
		$this->errors[] = $this->lastError;
		return false;
	}
}

/*
 * Supprime un enregistrement de la table
 * Paramètres :
 * 		$table : la table où supprimer l'enregistrement
 * 		$conditions : tableau associatif des conditions après le WHERE
 * 
 * Retourne true si la suppression s'est faite correctement, et false sinon
 */
function delete ($table, $conditions) {
	$conditions_ = array() ;
	foreach($conditions as $condition => $valeur) {
		if (!is_numeric($valeur)) {
			$valeur = $this->bdd->quote($valeur) ;
		}
		$conditions_[] = "$condition = $valeur" ;
	}
 
	$sql = "DELETE FROM $table WHERE " . join(' AND ', $conditions_) ;
	
	//var_dump($sql);
	
	try {
		$resultat = $this->bdd->exec($sql) ;
		return true ;
	}
	catch (PDOException $e) {
		$this->lastError = $e->getMessage();
		$this->errors[] = $this->lastError;
		return false;
	}
}
 
/*
 * Insère un élément dans la base
 * Paramètres :
 * 		$table : la table où insérer l'enregistrement
 * 		$valeurs : tableau associatif de la forme champ => valeur
 * 
 * Retourne l'id de l'élément inséré si l'insertion s'est faite correctement. Si l'insertion s'est faite correctement mais qu'on ne peut récupérer d'id, retourne 1. Sinon, retourne false.
 */
function insert ($table, $valeurs) {
	$colonnes_ = array_keys($valeurs) ;
	$valeurs_ = array_values($valeurs) ;
	foreach($valeurs_ as $clef => $valeur) {
		if (!is_numeric($valeur)) {
			$valeur = $this->bdd->quote($valeur) ;
		}
		$valeurs_[$clef] = $valeur ;
	}
 
	$sql = "INSERT INTO $table (" ;
	$sql .= join(', ', $colonnes_) ;
	$sql .= ') VALUES (' ;
	$sql .= join(', ', $valeurs_) ;
	$sql .= ');' ;
 
	try {
		$lines = $this->bdd->exec($sql);
		if($lines > 0) {
			$lastId = $this->bdd->lastInsertId();
			if(!$lastId)
				return $lines;
			else
				return $lastId;
		}
		else {
			return false;
		}
	}
	catch (PDOException $e) {
		$this->lastError = $e->getMessage();
		$this->errors[] = $this->lastError;
		return false;
	}
}

/*
 * Teste l'existence d'une table ou d'un élément d'une table
 * Paramètres :
 *   $_table : la table à tester
 *   $_field (optionnel) : le champ de la clé primaire de $_table
 *   $_value (optionnel) : la valeur de la clé primaire de l'élément recherché
 *   
 * Si les paramètres $_field et $_value ne sont pas donnés, teste si la $_table existe (retourne true ou false).
 * Si un seul des deux paramètres $_field ou $_value est donné, retourne false.
 * Si $_field et $_value sont donnés, cherche l'élément correspondant et retourne true si trouvé et false sinon.
 */
function exists($_table, $_field = null, $_value = null) {
  if(($_field == null || $_value == null) && $_field != $_value) {
    return false;
  }
  else if($_field == null) {
    foreach ($this->tables as $table) {
      if($table == $_table)
        return true;
    }
    return false;
  }
  else {
    $element = $this->select("select ".$_field." from ".$_table." where ".$_field."='".$_value."';");
    return $element;
  }
}
	
}