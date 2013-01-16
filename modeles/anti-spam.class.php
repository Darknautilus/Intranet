<?php

class AntiSpam {
	
	private $nb1;
	private $nb2;
	private $result;
	
	function __construct() {
		$this->nb1 = mt_rand(0,100);
		$this->nb2 = mt_rand(0,100);
		$this->result = $this->nb1+$this->nb2;
	}
	
	function getMessage() {
		return "Combien font $this->nb1 + $this->nb2 ?";
	}
	
	function getResult() {
		return $this->result;
	}
	
	function isCorrect($_result) {
		return $_result == $this->result;
	}
	
}