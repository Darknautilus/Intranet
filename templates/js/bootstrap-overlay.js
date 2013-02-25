/*

Fichier rajoutant des fonctionnalités à Bootstrap

Sommaire :
	- HTML in Popovers

*/

/*
	HTML in Popovers
	Permet de définir le contenu d'un popover comme étant du HTML
	
	Utilisation :
		L'élément déclenchant le popover doit avoir la classe .html-popover-trigger
		Le div contenant le HTML à insérer doit avoir un id.
		L'élément déclencheur doit avoir un attribut html-popover-content référençant cet id (i.e html-popover-content="#monId")
		Un élément <button> du contenu peut avoir la classe .html-popover-close : un clic sur cet élément fermera le popover.
*/
$(document).ready(function(){
  $('.html-popover-trigger').each(function() {
	  var content = $($(this).attr("html-popover-content"));
	  var trigger = $(this);
	  trigger.popover({
		  html : true,
		  content: function() {
			  return content.html();
		  }
	  });
	  content.find(".html-popover-close").each(function() {
		  $(this).attr("onclick","$('.html-popover-trigger').popover('hide');");
	  });
  });
});



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
/*				ATTENTION !! : fonction obsolète, voir pageCall()							*/
/* **************************************************************************************** */

function baseRequest(callback, tableName, targetFile) {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			callback(xhr.responseText, tableName);
			$(".loader_image").css("display","none");
		}
		else {
			$(".loader_image").css("display","inline");
		}
	};
	
	xhr.open("POST", targetFile, true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("ajax=true&table="+tableName);
}

function setAccordionBody(content, id) {
	$("#"+id+" .content").html(content);
}

/*
	Change block content v2
	
	Utilise l'API de JQuery
*/
function pageCall(block, targetURL) {
	$.ajax({
		type:"get",
		url:targetURL,
		dataType:"html",
		success:function(ret) {
			$(block).html(ret);
		}
	});
}