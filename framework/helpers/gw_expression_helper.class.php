<?php


use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class GW_Expression_Helper
{
	
	use Singleton;
	
	
	function __construct() {
		
		

		$this->obj = new ExpressionLanguage();

		
	}
	
	
	function evaluate($code, $vars=false)
	{
		return $this->obj->evaluate($code, $vars);
	}
	
	
}
