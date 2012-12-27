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