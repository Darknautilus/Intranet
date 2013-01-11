/* ** cartouche ********************************************************************* */
/* Script complet de gestion d'une requête de type XMLHttpRequest                     */
/* Par Sébastien de la Marck (aka Thunderseb)                                         */
/* ********************************************************************************** */

function getXMLHttpRequest() {
	var xhr = null;
	
	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest(); 
		}
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	
	return xhr;
}




/* **************************************************************************************** */
/* Accordéon pour l'affichage du contenu des tables											*/
/* **************************************************************************************** */

function baseRequest(callback, tableName, targetFile) {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			callback(xhr.responseText, tableName);
			$(".loader_image").style.display = "none";
		}
		else {
			$(".loader_image").style.display = "inline";
		}
	};
	
	xhr.open("POST", targetFile, true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("ajax=true&table="+tableName);
}

function setAccordionBody(content, tableName) {
	$("#"+tableName+" div").html(content);
}


/* **************************************************************************************** */
/* Formulaire de saisie de visite															*/
/* Évolution possible :																		*/
/* 	affichage d'une info-bulle avec la nature de l'erreur sur le champ						*/
/* **************************************************************************************** */

/*
 * Utilisation :
 * 	- Faire un formulaire avec la classe .form-control
 * 	- Toujours dans la balise <form>, définir l'attribut "form-control-action" qui est un lien vers un fichier PHP. Ce fichier se charge des controles.
 * 	- Chaque ensemble label/champ/markup constitue un groupe qu'il faut matérialiser par un div utilisant la classe .form-control-group.
 *  - Si le champ de ce groupe est requis, rajouter la classe .required au groupe.
 *  - Dans chaque groupe, le markup utilise la classe .form-markup, le champ la classe .form-element et le label la classe .form-label
 *  - Le champ d'envoi (type="submit") est en dehors de tout groupe et utilise la classe .form-submit.
 *  - Le .form-markup est un block qui contient tous les markups (images, texte ou autre). Il y a deux types de markups : valide et invalide. Pour chacun, utiliser respectivement .form-markup-valid et .form-markup-invalid
 *  
 *  Fichier de controle PHP :
 *  	Paramètres :
 *  		$_POST["ajax"] : Permet de controler si le fichier a été appelé par Javascript ou non.
 *  						 Si défini, = "true" sinon, indéfini. Tester avec isset().
 *  		$_POST["field"] : Contient l'id du champ qui vient de perdre le focus.
 *  		$_POST["value"] : Contient la valeur du champ qui vient de perdre le focus.
 *  	Comment structurer les tests :
 *  		- Tester si le fichier a été appelé par Javascript, et non par un autre script : if(isset($_POST["ajax"]))
 *  			Pratique si l'on veut inclure ces tests dans un script plus global.
 *  		- Faire un switch sur $_POST["field"] et tester tous les cas correspondants aux id des champs du formulaire.
 *  		- Dans chaque cas, faire un echo("OK") si le champ est valide, et echo(message_d'erreur) sinon. Attention le script ne doit renvoyer qu'une seule chaine de caractères à la fois !
 */


// Initialisation du formulaire
$(document).ready(function() {
	var formAction;
	var required;
	var elem;
	$(".form-control").each(function() {
		formAction = $(this).attr("form-control-action");
		$(this).find(".form-control-group").each(function() {
			if($(this).find(".form-element").attr("required")) {
				required = true;
			}
			else {
				required = false;
			}
			// Action onblur
			elem = $(this).find(".form-element");
			elem.attr("onblur","formRequest(setValidMarkup,'"+elem.attr("id")+"', '"+required+"','"+formAction+"');");
			formRequest(setValidMarkup,elem.attr("id"), required, formAction);
		});
	});
});

function updateFormMarkup(controlGroup) {
	if($(controlGroup).attr("valid") == "false") {
		$(controlGroup).find(".form-markup").find(".form-markup-valid").css("display","none");
		$(controlGroup).find(".form-markup").find(".form-markup-invalid").css("display","inline-block");
	}
	else {
		$(controlGroup).find(".form-markup").find(".form-markup-invalid").css("display","none");
		$(controlGroup).find(".form-markup").find(".form-markup-valid").css("display","inline-block");
	}
}


function formRequest(callback, fieldName, required, targetFile) {
	var xhr = getXMLHttpRequest();
	var fieldValue = $("#"+fieldName).val();
	var controlGroup = $("#"+fieldName).parent(".form-control-group");
	
	xhr.onreadystatechange = function() {
		if(xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			callback(xhr.responseText, controlGroup);
		}
	};
	
	xhr.open("POST", targetFile, true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("ajax=true&field="+fieldName+"&required="+required+"&value="+fieldValue);
}

function setValidMarkup(valid,controlGroup) {
	if(valid == "OK") {
		$(controlGroup).attr("valid","true");
	}
	else {
		$(controlGroup).attr("valid","false");
	}
	updateFormMarkup($(controlGroup));
}