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
/* Envoi de formulaire de visite															*/
/* **************************************************************************************** */

function majMarkupCaddie(nbVisites, action) {
	$(".markupCaddie").html("<a href=\""+action+"\">Mon <i class=\"icon-shopping-cart\"></i> : <span class=\"badge badge-success\">"+nbVisites+"</span></a>");
}

$(document).ready(function() {
	
	$(".formGererVisitesCaddie").submit(function(e) {
		var form = $(this);
		var idbien = $("#idbien").val();
		var priorite = $("#priorite").val();
		var caddieMarkupLink = $(this).find("input[name='caddieMarkupLink']").val();
		
		$.ajax({
			type: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize(),
            dataType: "json",
            success: function(data){
            	if(data.modify == "modified") {
            		if(data.result) {
            			form.parent().find(".alert").remove();
            			form.parent().prepend("<p class=\"alert alert-success alert-block\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>Visite modifiée avec la priorité "+data.priorite+"</p>");
            		}
            		else {
            			form.parent().find(".alert").remove();
            			form.parent().prepend("<p class=\"alert alert-danger alert-block\">Cette visite n'existe pas !</p>");
            		}
            	}
            	else if(data.modify == "deleted") {
            		if(data.result) {
            			form.parent().parent().remove();
            		}
            		else {
            			form.parent().find(".alert").remove();
            			form.parent().prepend("<p class=\"alert alert-danger alert-block\">Cette visite n'existe pas !</p>");
            		}
            	}
            	else {
            		if(data.result) {
            			form.parent().html("<p class=\"alert alert-success alert-block\">Visite ajoutée avec la priorite "+data.priorite+"</p>");
            		}
            		else {
            			form.parent().html("<p class=\"alert alert-block\">Visite déjà ajoutée avec la priorite "+data.priorite+"</p>");
            		}
            	}
            	if(data.nbVisites > 0) {
            		$(".markupCaddie").css("display","block");
            		majMarkupCaddie(data.nbVisites, caddieMarkupLink);
            	}
            	else if(data.nbVisites == 0) {
            		$(".markupCaddie").css("display","none");
            		// On recharge la page
            		location.reload();
            	}
            }
		});
		return false;
	});
	
});

/* **************************************************************************************** */
/* Accordéon pour l'affichage du contenu des tables											*/
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



/*
  Pour la modification des biens
  
  Pour les values : si le texte affiché dans une cellule est différent du texte qu'on veut modifier (e.g. texte tronqué), spécifier dans le html l'attribut value avec la vraie valeur de la cellule.
*/
$(document).ready(function() {
	$(".editable-row .edition-in").css("display","none");
	$(".cell-editor").css("display","none");
	$(".editable-cell.hidden").css("display","none");
});
$(".editable-row .edition-trigger").click(function() {
	// La ligne éditable
	var line = $(this).parents("tr");
	// La cellule contenant les boutons d'action
	var actionscell = line.children(".actions-cell");
	// On met les boutons en mode édition
	actionscell.find(".edition-out").css("display","none");
	actionscell.find(".edition-in").css("display","block");
	
	// On met la ligne en surbrillance
	line.addClass("info");
	
	// On parcourt chaque cellule à éditer
	line.find(".editable-cell").each(function() {
		var cell = $(this);
		// On sauvegarde la valeur du champ dans un attribut
		cell.attr("init-value",cell.html());
		if(cell.attr("value") == undefined) {
			cell.attr("value",cell.html());
		}
		// On transforme la cellule avec un input ou le cell-editor spécifié
		if(cell.attr("cell-editor") != undefined) {
			cell.html($(cell.attr("cell-editor")).html());
		}
		else {
			cell.html("<input type=\"text\" name=\""+cell.attr("name")+"\" class=\"span12\" value=\""+cell.attr("value")+"\" />");
		}
	});
	line.keypress(function(e) {
		if(e.keyCode == 13) {
			onValid(line.children("actions-cell").children("action-valid"));
		}
	});
	
});
function onValid(button) {
	var line = button.parents("tr");
	var actionscell = line.children(".actions-cell");
	
	// On supprime le message d'erreur éventuel (.error-line suivant la ligne actuelle)
	line.find("+ .error-line").remove();
	
	var strData = "";
	line.find(".editable-cell").each(function() {
		strData += $(this).attr("name") + "=" + $(this).children(":first").attr("value") + "&";
	});
	strData += "ajax=true";
	$.ajax({
		type: "post",
		url: line.attr("action"),
		data: strData,
		dataType: "json",
		success : function(data) {
			if(data.result) {
				line.find(".editable-cell").each(function() {
					
					$(this).html(data.values[$(this).attr("name")]);
					$(this).attr("value",data.values[$(this).attr("name")]);
					$(this).attr("init-value",data.values[$(this).attr("name")]);
					
					// cas spécial pour la catégorie
					if($(this).attr("name") == "idtype") {
						$(this).html(data.values.nomtype);
						$($(this).attr("cell-editor")).children("select").children("option").each(function() {
							if($(this).attr("value") == data.values.idtype) {
								$(this).attr("selected","selected");
							}
							else {
								$(this).removeAttr("selected");
							}
						});
					}
					
					line.removeClass("info");
					line.removeClass("error");
					// On met les boutons en mode non-edition
					actionscell.find(".edition-in").css("display","none");
					actionscell.find(".edition-out").css("display","block");
					// On supprime le message d'erreur éventuel
					line.find("+ .error-line").remove();
					
				});
			}
			else {
				// On marque l'erreur avec un fond rouge
				line.removeClass("info");
				line.addClass("error");
				var errors = "<tr class=\"error error-line\"><td colspan=\"6\" style=\"text-align:center;\"><ul class=\"unstyled\">";
				$.each(data.errors, function(index, value) {
					errors += "<li>- "+value+"</li>";
				});
				errors += "</ul></td></tr>";
				line.after(errors);
			}
		}
	});
	return false;
	
}
// Si on clique sur le bouton valider
$(".action-valid").click(function () {
	onValid($(this));
});
// Si on clique sur le bouton annuler
$(".action-cancel").click(function() {
	
	var line = $(this).parents("tr");
	var actionscell = line.children(".actions-cell");
	
	// On remet toutes les cases dans l'état initial
	line.find(".editable-cell").each(function() {
		$(this).html($(this).attr("init-value"));
	});
	// On met les boutons en mode non-edition
	actionscell.find(".edition-in").css("display","none");
	actionscell.find(".edition-out").css("display","block");
	// On enlève la surbrillance
	line.removeClass("info");
	line.removeClass("error");
	
	// On supprime le message d'erreur éventuel
	line.find("+ .error-line").remove();
	
	return false;
});




/*
	Pour la suppression de bien
*/
$(document).ready(function() {
	$(".bien-deleter").click(function() {
		var a = $(this);
		var line = a.parents("tr");
		
		var choix = confirm("Etes-vous sûr de vouloir supprimer ce bien ?");
		
		if(choix) {
			$.ajax({
				type: "post",
				url: a.attr("href"),
				data: "ajax=true",
				dataType: "json",
				success: function(data) {
					if(data.result) {
						line.remove();
					}
					else {
						// On marque l'erreur avec un fond rouge
						line.addClass("error");
						var errors = "<tr class=\"error error-line\"><td colspan=\"6\" style=\"text-align:center;\"><ul class=\"unstyled\">";
						$.each(data.errors, function(index, value) {
							errors += "<li>- "+value+"</li>";
						});
						errors += "</ul></td></tr>";
						line.after(errors);
					}
				}
			});
		}
		return false;
	});
});


/*
 * Calcul du montant des biens
 */
$(document).ready(function() {
	stats_launch();
});
$("#stats_montantBiens .refresh").click(function() {
	stats_launch();
});
function stats_launch() {
	$("#stats_montantBiens .tab-pane").each(function() {
		var tab = $(this);
		var url = tab.parents("#stats_montantBiens").attr("target-url");
		var data = "ajax=true";
		if(tab.attr("id") == "montant") {
			data += "&section=montant";
			$.ajax({
				type: "post",
				url: url,
				data: data,
				dataType: "json",
				success: function(data) {
					tab.find("#stats_montantTotal").html(data.montantTotal+" €");
					tab.find("#stats_montantComm").html(data.montantCom+" €");
				}
			});
		}
		else if(tab.attr("id") == "montantCat") {
			var select  = tab.find("select");
			data += "&section=montantCat&type="+select.val();
			$.ajax({
				type: "post",
				url: url,
				data: data,
				dataType: "json",
				success: function(data) {
					tab.find("#stats_montantTotal").html(data.montantTotal+" €");
					tab.find("#stats_montantComm").html(data.montantCom+" €");
				}
			});
		}
		else if(tab.attr("id") == "affDetails") {
			var input = tab.find("#idbien");
			data += "&section=affDetails&idbien="+input.val();
			$.ajax({
				type: "post",
				url: url,
				data: data,
				dataType: "json",
				success: function(data) {
					if(data.result) {
						tab.find("#errorBien").html("");
						tab.find("#titrebien").html(data.bien.titrebien);
						tab.find("#description").html(data.bien.detailbien);
						tab.find("#adrbien").html(data.bien.adrbien);
						tab.find("#typebien").html(data.bien.nomtype);
						tab.find("#detailbien").html("<a class=\"btn btn-block btn-info\" href=\""+data.urlDetails+"\">Plus de détails</a>");
					}
					else {
						tab.find("#titrebien").html("");
						tab.find("#detailbien").html("");
						tab.find("#description").html("");
						tab.find("#adrbien").html("");
						tab.find("#typebien").html("");
						tab.find("#errorBien").html("<p><em>Aucun bien trouvé</em></p>");
					}
				}
			});
		}
	});
}