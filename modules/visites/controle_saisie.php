<?php

if(isset($_POST["ajax"]) && $_POST["ajax"] == "true") {
	if($_POST["required"] == "true" && empty($_POST["value"])) {
		echo "NOK";
	}
	else {
		switch ($_POST["field"]) {
			case "nom":
				if(strlen($_POST["value"]) > 30)
					echo "NOK";
				else
					echo "OK";
				break;
			
			case "adresse":
				if(strlen($_POST["value"]) > 50)
					echo "NOK";
				else
					echo "OK";
				break;
				
			case "tel":
				if(strlen($_POST["value"]) != 10)
					echo "NOK";
				else
					echo "OK";
				break;
				
			case "email":
				if(strlen($_POST["value"]) > 20)
					echo "NOK";
				else
					echo "OK";
				break;
				
			case "dispo":
				if(strlen($_POST["value"]) > 50)
					echo "NOK";
				else
					echo "OK";
				break;
		
			default:
				break;
		}	
	}	
}
