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
			$("#loader").style.display = "none";
		}
		else {
			$("#loader").style.display = "inline";
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
/* **************************************************************************************** */

// Initialisation du formulaire
$(document).ready(function() {
	$(".form-control").each(function() {
		$(this).find(".form-control-group").each(function() {
			if($(this).hasClass("required")) {
				$(this).attr("valid","false");
			}
			else {
				$(this).attr("valid","true");
			}
		})
		updateFormMarkup($(this));
	})
});

function updateFormMarkup(form) {
	var allValid = true;
	form.find(".form-control-group").each(function() {
		if($(this).attr("valid") == "false") {
			$(this).find(".form-markup").find(":first").css("display","inline-block");
			allValid = false;
		}
		else {
			$(this).find(".form-markup").find(":first").css("display","none");
		}
	});
	if(!allValid) {
		form.find(".form-submit").attr("disabled","true");
	}
}


function formRequest(callback, fieldName, targetFile) {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if(xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			callback(xhr.responseText, fieldName);
		}
		else {
			
		}
	};
	
	xhr.open("POST", targetFile, true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("ajax=true&field="+fieldName);
}

function setValidMarkup() {
	
}