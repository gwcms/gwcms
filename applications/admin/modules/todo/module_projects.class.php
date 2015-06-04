<?php


class Module_Projects extends GW_Common_Module
{	
	
	function viewDefault()
	{
		//Array('conditions'=>'type<2')
		$this->viewList();
		
	}
	
	

	
	function viewForm()
	{
		
		parent::viewForm();
		//d::dumpas($this->parent);
	}

	
	
	
}