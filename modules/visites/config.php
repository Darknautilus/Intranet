<?php

include(PATH_MODELES."/bdd.class.php");

function isValidMail($address) {
	if(!preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]_?])*@[[:alnum:]]([-.]?[[:alnum:]])+\.([a-z]{2,6})$`',
			$address))
	{
		return false;
	}
	else
	{
		return true;
	}
}